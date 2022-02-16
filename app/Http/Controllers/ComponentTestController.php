<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrimaryCategory;
use App\Models\Product;
use App\Models\Stock;


class ComponentTestController extends Controller
{
    //
    public function showComponent1(){
        $message = 'メッセージ123';
        return view('tests.component-test1',
        compact('message'));
    }
    public function showComponent2(){
        return view('tests.component-test2');
    }

    public function index(Request $request)
    {
        // dd($request);

        // 同期的に送信
         //Mail::to('test@example.com')
        // ->send(new TestMail());
        
        // 非同期に送信
         //SendThanksMail::dispatch();

        $categories = PrimaryCategory::with('secondary')
        ->get();


        $products = Product::availableItems()
        ->selectCategory($request->category ?? '0')
        ->searchKeyword($request->keyword)
        ->sortOrder($request->sort)
        ->paginate($request->pagination ?? '20');

        ( $products);

        return view('user.index', 
        compact('products', 'categories'));
    }


    public function show($id)
    {
        $product = Product::findOrFail($id);
        $quantity = Stock::where('product_id', $product->id)
        ->sum('quantity');

        if($quantity > 9){
            $quantity = 9;
          }

        return view('user.show', 
        compact('product', 'quantity'));
    }



}
