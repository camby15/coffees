<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\Product\Cart;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Models\Product\Order;


class ProductsController extends Controller
{
    public function singleProduct($id)
    {
        $product = Product::find($id);

        $relatedProducts = Product::where('type', $product->type)
        ->where('id', '!=', $id)->take('4')
        ->orderBy('id', 'desc')
        ->get();

        //checking for Products  in cart
        $checkingInCart = Cart::where('pro_id', $id)
        ->where('user_id', Auth::user()->id)
        ->count();

        return view('products.productsingle', compact('product', 'relatedProducts', 'checkingInCart'));
    }

    public function addCart(Request $request, $id)       //this is when your Submit  a from
    {

        $addCart =  Cart::create([
            "pro_id" => $request->pro_id,
            "name" => $request->name,
            "price" => $request->price,
            "image" => $request->image,
            "user_id" => Auth::user()->id,

        ]);



        return Redirect::route('product.single', $id)->with( ['success' => 'Product added to cart successfully'] );


    }

    public function cart()
    {

        $cartProducts = Cart::where('user_id', Auth::user()->id)
        ->orderBy('id', 'desc')
        ->get();

        $totalPrice = Cart::where('user_id', Auth::user()->id)
        ->orderBy('id', 'desc')
        ->sum('price');


        return view('products.cart', compact('cartProducts', 'totalPrice'));
    }


    public function deleteProductCart($id)
    {
        $deleteProductCart = Cart::where('pro_id', $id)
          ->where('user_id', Auth::user()->id);
          

          $deleteProductCart->delete();


        if($deleteProductCart){
            return Redirect::route('cart')->with( ['delete' => 'Product deleted from cart successfully'] );
        }


    }


    public function prepareCheckout(Request $request)      
    {

        $value = $request->price;

        $price = Session::put('price', $value); 

        $newPrice = Session::get($price);




            if($newPrice > 0){
                return Redirect::route('checkout');
            }


    }
    public function checkout()
    {
       return view('products.checkout');
    }

    //storeCheckout
    public function storeCheckout(Request $request)       
    {
        $checkout = Order::create($request->all());

        echo 'Well come to payment gateway';
            
    



        return Redirect::route('product.pay');


    }

    








}
