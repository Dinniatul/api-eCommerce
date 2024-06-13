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
    // public function index()
    // {
    //     // Get the authenticated user's ID
    //     $userId = auth()->id();

    //     // Retrieve the cart items for the authenticated user
    //     $cartItems = Cart::where('user_id', $userId)->with('product')->get();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Keranjang berhasil ditampilkan',
    //         'data' => $cartItems
    //     ]);
    // }

    public function index()
    {
        // Get the authenticated user's ID
        $userId = auth()->id();

        // Retrieve cart IDs that are already selected in orders
        $orderedCartIds = Order::where('user_id', $userId)
            ->pluck('cart_id')
            ->map(function ($cartIds) {
                return explode(',', $cartIds);
            })
            ->flatten()
            ->toArray();

        // Retrieve the cart items for the authenticated user that are not in the ordered cart IDs
        $cartItems = Cart::where('user_id', $userId)
            ->whereNotIn('id', $orderedCartIds)
            ->with('product')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Keranjang berhasil ditampilkan',
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
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        // Check if requested quantity is available
        if ($product->stock < $request->input('quantity')) {
            return response()->json([
                'status' => false,
                'message' => 'Stock tidak mencukupi'
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
            'message' => 'Produk berhasil dimasukkan ke keranjang',
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






    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cart  $cart
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
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

        // Restore the product stock
        $product->stock += $cart->quantity;
        $product->save();

        // Delete the cart entry
        $cart->delete();

        return response()->json([
            'status' => true,
            'message' => 'Keranjang berhasil dihapus'
        ]);
    }
}
