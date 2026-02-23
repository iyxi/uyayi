<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Assuming you have a Product model

class CustomerController extends Controller
{
    public function index()
    {
        // For now, just return a view or string to test
        return view('shop.index'); 
    }

    public function cart()
    {
        return view('shop.cart');
    }

    // Add empty methods for the others so the routes don't crash
    public function addToCart() {}
    public function checkout() {}
    public function showOrder() {}
}