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
    //     $user = User::findOrFail(Auth::id());
    //     $histories = History::where('user_id', $user->id)->get();
    //     $historyInfo = History::with('product.imageFirst')->where('user_id', $user->id)->get();
    //     foreach($historyInfo as $history){
           
    //         dd($history->product->price);
    //        }
         
        // $products = Product::where('product_id', $histories->product_id)->get();
       
         //dd($histories);
          //foreach($histories as $history){
           //dd($history);
        //    $products[] = Product::where('id', $history->product_id)->first();
        //    array_push($array,$history->quantity);
         //}
        // // $itemInCart = Cart::where('product_id', $request->product_id)
        // dd($products);
        
         
        
        // $totalPrice = 0;
        
        // foreach($products as $product){
        //    // dd($product->pivot);
        //     // dd($product);
        //     $totalPrice += $product->price * $product->pivot->quantity;
        // }

        // foreach($histories as $history){
        //     // dd($history->product_id->price);

        //     $prod = Product::where('id', $history->product_id)->first();
           
        //      $totalPrice += $prod->price * $history->quantity;
        //      //dd($totalPrice);
        //  }


        $user = User::findOrFail(Auth::id());
        $products = $user->products;
        
        $totalPrice = 0;
        
        foreach($products as $product){
           
            $totalPrice += $product->price * $product->pivot->quantity;
            
        }


        // foreach ($products as $product ){
        //     dd($product->imageFirst);
        // }





        

        return view('user.cart', 
            compact('products', 'totalPrice'));
    }



    public function thanks()
    {
        
         $user = User::findOrFail(Auth::id());
         $histories = History::where('user_id', $user->id)->get();
        $historyInfo = History::with('product.imageFirst')->where('user_id', $user->id)->get();

        //   foreach($historyInfo as $history){
        //     foreach($history->product as $product){
        //          dd($product->imageFirst->filename);
        //     }}
       
        
        // $totalPrice = 0;
        
        // foreach($products as $product){
        //    // dd($product->pivot);
        //     // dd($product);
        //     $totalPrice += $product->price * $product->pivot->quantity;
        // // }

        // foreach($histories as $history){
        //     // dd($history->product_id->price);

        //     $prod = Product::where('id', $history->product_id)->first();
           
        //      $totalPrice += $prod->price * $history->quantity;
        //      //dd($totalPrice);
        //  }
        // }

        return view('user.thanks'
        ,
         compact('historyInfo'));
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

    public function success()
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
                // 'goodsPrice' => Product::findOrFail($item->product_id)->price
             ]);
        
            }



        Cart::where('user_id', Auth::id())->delete();

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
