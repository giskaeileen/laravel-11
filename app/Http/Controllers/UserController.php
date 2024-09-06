<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index() :view
    {
        //get all products
        $products = Product::latest()->paginate(10);

        //render view with products
        return view('users.index', compact('products'));
    }

        /**
     * show
     *
     * @param  mixed $id
     * @return View
     */
    public function show(string $id): View
    {
        //get product by ID
        $product = Product::findOrFail($id);

        //render view with product
        return view('users.show', compact('product'));
    }
}
