<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\History;
use App\Models\User;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use App\Services\CartService;
use App\Jobs\SendThanksMail;
use App\Jobs\SendOrderedMail;

class CartController extends Controller
{
    public function index()
     {
   


        $user = User::findOrFail(Auth::id());
        $products = $user->products;
        
        $totalPrice = 0;
        
        foreach($products as $product){
           
            $totalPrice += $product->price * $product->pivot->quantity;
            
        }

        

       

        return view('user.cart', 
            compact('products', 'totalPrice'));
    }



    public function thanks(Request $request)
    {

        // "url" => "http://127.0.0.1:8000/cart/checkout";

         
       
        
         $user = User::findOrFail(Auth::id());
         $histories = History::where('user_id', $user->id)->get();
         $historyInfo = History::with('product.imageFirst')->where('user_id', $user->id)->get();
         $checkoutFlag = false;

         if($request->session()->get("checkFlag") === true){
            $checkoutFlag = true;
         }
         
         session()->forget("checkFlag");

        //  dd($data = $request->session()->all(),$checkoutFlag);
        //  if($request->session()->get("_previous") === "http://127.0.0.1:8000"){
        //     $checkoutFlag = true;
        //  }
        //  dd($checkoutFlag,$request->session()->get("_previous")->get("url"));
        //    dd($checkoutFlag,$request->session()->get("_previous"),$request->session()->get("_previous"."url"));

    //  dd($request->session()->get("url"));

        return view('user.thanks'
        ,
         compact('historyInfo','checkoutFlag'));
    }

    





    public function add(Request $request)
    {
        $itemInCart = Cart::where('product_id', $request->product_id)
        ->where('user_id', Auth::id())->first();

        if($itemInCart){
            $itemInCart->quantity += $request->quantity;
            $itemInCart->save();

        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity
            ]);
        }
        
        return redirect()->route('user.cart.index');
    }

    public function delete($id)
    {
        Cart::where('product_id', $id)
        ->where('user_id', Auth::id())
        ->delete();

        return redirect()->route('user.cart.index');
    }

    public function checkout()
    {
        

        $user = User::findOrFail(Auth::id());
        $products = $user->products;
        
        $lineItems = [];
        foreach($products as $product){
            $quantity = '';
            $quantity = Stock::where('product_id', $product->id)->sum('quantity');

            if($product->pivot->quantity > $quantity){
                return redirect()->route('user.cart.index');
            } else {
                $lineItem = [
                    'name' => $product->name,
                    'description' => $product->information,
                    'amount' => $product->price,
                    'currency' => 'jpy',
                    'quantity' => $product->pivot->quantity,
                ];
                array_push($lineItems, $lineItem);    
            }
        }
        // dd($lineItems);
        foreach($products as $product){
            Stock::create([
                'product_id' => $product->id,
                'type' => \Constant::PRODUCT_LIST['reduce'],
                'quantity' => $product->pivot->quantity * -1
            ]);
        }

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [$lineItems],
            'mode' => 'payment',
            'success_url' => route('user.cart.success'),
            'cancel_url' => route('user.cart.cancel'),
        ]);

        $publicKey = env('STRIPE_PUBLIC_KEY');

        return view('user.checkout', 
            compact('session', 'publicKey'));
    }

    public function success(Request $request)
    {
        ////
        $items = Cart::where('user_id', Auth::id())->get();
        $products = CartService::getItemsInCart($items);
        $user = User::findOrFail(Auth::id());

        //dd( $items, $products,$user);

        SendThanksMail::dispatch($products, $user);
        foreach($products as $product)
        {
            SendOrderedMail::dispatch($product, $user);
        }
        // dd('ユーザーメール送信テスト');
        ////



        foreach($items as $item){
            // dd( Auth::id(),$item->product_id,$item->quantity);
             History::create([
                'user_id' => Auth::id(),
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
             ]);
        
            }



        Cart::where('user_id', Auth::id())->delete();
        // dd($request->session()->get("_previous"));

        $request->session()->put(['checkFlag' => true]);

        return redirect()->route('user.cart.thanks');
    }

    public function cancel()
    {
        $user = User::findOrFail(Auth::id());

        foreach($user->products as $product){
            Stock::create([
                'product_id' => $product->id,
                'type' => \Constant::PRODUCT_LIST['add'],
                'quantity' => $product->pivot->quantity
            ]);
        }

        return redirect()->route('user.cart.index');
    }
}
