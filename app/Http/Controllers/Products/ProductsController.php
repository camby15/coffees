<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product\Product;
use Illuminate\Support\Facades\Auth;
use App\Models\Product\Cart;
use Redirect;

class ProductsController extends Controller
{
    public function singleProduct($id)
    {
        $product = Product::find($id);

        $relatedProducts = Product::where('type', $product->type)
        ->where('id', '!=', $id)->taken('4')
        ->orderBy('id', 'desc')
        ->get();

        //checking for Products  in cart
        $checkingInCart = Cart::where('pro_id', $id)
        ->where('user_id', Auth::user()->id)
        ->count();

        return view('products.productsingle', compact('product', 'relatedProducts', 'checkingInCart'));
    }

    public function addCart(Request $request, $id)
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


        return view('products.cart', compact('cartProducts'));
    }

}
