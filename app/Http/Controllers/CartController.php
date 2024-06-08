<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get the authenticated user's ID
        $userId = auth()->id();

        // Retrieve the cart items for the authenticated user
        $cartItems = Cart::where('user_id', $userId)->with('product')->get();

        return response()->json([
            'status' => true,
            'message' => 'Keranjang berhasil diambil',
            'data' => $cartItems
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function addToCart(Request $request)
    // {
    //     // Validate the request
    //     $validator = Validator::make($request->all(), [
    //         'product_id' => 'required|exists:products,id',
    //         'quantity' => 'required|integer|min:1',
    //     ]);

    //     // Check if the validation fails
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     // Retrieve the product
    //     $product = Product::find($request->input('product_id'));

    //     // Check if the product exists
    //     if (!$product) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Product not found'
    //         ], 404);
    //     }

    //     // Check if requested quantity is available
    //     if ($product->stock < $request->input('quantity')) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Requested quantity exceeds available stock'
    //         ], 422);
    //     }

    //     // Calculate the total price
    //     $totalPrice = $product->price * $request->input('quantity');

    //     // Deduct product stock
    //     $product->stock -= $request->input('quantity');
    //     $product->save();

    //     // Create a new cart entry
    //     $cart = Cart::create([
    //         'user_id' => auth()->id(), // Get the authenticated user's ID
    //         'product_id' => $request->input('product_id'),
    //         'quantity' => $request->input('quantity'),
    //         'totalPrice' => $totalPrice,
    //     ]);

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Product successfully added to cart',
    //         'data' => $cart
    //     ]);
    // }
    public function addToCart(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Retrieve the product
        $product = Product::find($request->input('product_id'));

        // Check if the product exists
        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // Check if requested quantity is available
        if ($product->stock < $request->input('quantity')) {
            return response()->json([
                'status' => false,
                'message' => 'Requested quantity exceeds available stock'
            ], 422);
        }

        // Calculate the total price
        $totalPrice = $product->price * $request->input('quantity');

        // Deduct product stock
        $product->stock -= $request->input('quantity');
        $product->save();

        // Create a new cart entry
        $cart = Cart::create([
            'user_id' => auth()->id(), // Get the authenticated user's ID
            'product_id' => $request->input('product_id'),
            'quantity' => $request->input('quantity'),
            'totalPrice' => $totalPrice,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Product successfully added to cart',
            'data' => $cart
        ]);
    }

    public function editCart(Request $request, $id)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'quantity' => 'required|integer|min:1',
    ]);

    // Check if the validation fails
    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    // Retrieve the cart entry
    $cart = Cart::find($id);

    // Check if the cart entry exists
    if (!$cart) {
        return response()->json([
            'status' => false,
            'message' => 'Cart entry not found'
        ], 404);
    }

    // Retrieve the product
    $product = Product::find($cart->product_id);

    // Check if the product exists
    if (!$product) {
        return response()->json([
            'status' => false,
            'message' => 'Product not found'
        ], 404);
    }

    // Restore the previous quantity to the product stock
    $product->stock += $cart->quantity;

    // Check if the new requested quantity is available
    if ($product->stock < $request->input('quantity')) {
        // If not, restore the stock to its original state before returning an error
        $product->stock -= $cart->quantity;
        return response()->json([
            'status' => false,
            'message' => 'Requested quantity exceeds available stock'
        ], 422);
    }

    // Deduct the new quantity from the product stock
    $product->stock -= $request->input('quantity');
    $product->save();

    // Calculate the new total price
    $totalPrice = $product->price * $request->input('quantity');

    // Update the cart entry with the new quantity and total price
    $cart->quantity = $request->input('quantity');
    $cart->totalPrice = $totalPrice;
    $cart->save();

    return response()->json([
        'status' => true,
        'message' => 'Cart entry successfully updated',
        'data' => $cart
    ]);
}






    // public function checkout(Request $request)
    // {
    //     $request->validate([
    //         'user_id' => 'required|exists:users,id',
    //     ]);

    //     $cartItems = Cart::where('user_id', $request->user_id)->get();

    //     if ($cartItems->isEmpty()) {
    //         return response()->json(['message' => 'Tidak ada item di keranjang untuk di-checkout'], 400);
    //     }

    //     foreach ($cartItems as $item) {
    //         Order::create([
    //             'user_id' => $item->user_id,
    //             'product_id' => $item->product_id,
    //             'quantity' => $item->quantity,
    //         ]);

    //         $item->delete(); // Hapus item dari keranjang setelah membuat pesanan
    //     }

    //     return response()->json(['message' => 'Pesanan berhasil dibuat'], 200);
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function show(Cart $cart)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cart $cart)
    {
        //
    }
}
