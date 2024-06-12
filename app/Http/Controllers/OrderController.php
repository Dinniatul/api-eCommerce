<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{

    public function getOrderbyUserId($userId)
    {
        // Find orders belonging to the specified user ID
        $orders = Order::with('cart')->where('user_id', $userId)->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No orders found for this user'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Orders found for the user',
            'data' => $orders
        ]);
    }

    public function showAllOrders()
    {
        // Check if the authenticated user is an admin
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Find all orders
        $orders = Order::with('cart')->get();

        return response()->json([
            'status' => true,
            'message' => 'All orders retrieved successfully',
            'data' => $orders
        ]);
    }



    public function store(Request $request)
    {
        // dd($request->all());

        // dd($request->cart_id);
        // Validate the request
        $validator = Validator::make($request->all(), [
            'cart' => 'required|array|min:1',
            'cart.*' => 'exists:cart,id',
            'methodPayment' => 'required'
            // Validate against 'carts' table
        ]);

        // Check if the validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Retrieve the cart items
        $cartItems = Cart::whereIn('id', $request->input('cart'))
            ->where('user_id', auth()->id())
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No valid cart items found'
            ], 404);
        }

        // Calculate the total payment
        $totalPayment = $cartItems->sum('totalPrice');

        // Format total payment to Indonesian Rupiah
        $formattedTotalPayment = $this->formatRupiah($totalPayment);
        // Create a new order
        foreach ($request->cart as $value) {
            // dd($value);
            $order = Order::create([
                'user_id' => auth()->id(),
                'cart_id' => $value['cart_id'], // Set the cart_id here
                'totalPayment' => $totalPayment, // Total payment in decimal format
                'status' => 'Belum Dibayar',
                'methodPayment' => $request->methodPayment

            ]);
        }


        // Attach cart items to the order
        foreach ($cartItems as $cartItem) {
            $cartItem->update(['order_id' => $order->id]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Order berhasil dibuat',
            'data' => [
                'order' => $order,

            ]
        ]);
    }


    public function update(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        $user = Auth::user();

        if ($user->role === 'admin') {
            // Validation for admin
            $validator = Validator::make($request->all(), [
                'status' => 'required|string|in:Sudah Dibayar,Selesai,Belum Dibayar',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()
                ], 400);
            }

            $order->status = $request->status;
            $order->save();

            return response()->json([
                'status' => true,
                'message' => 'Status order berhasil diupdate',
                'data' => $order
            ]);
        } else {
            // Ensure the order belongs to the authenticated user
            if ($order->user_id !== $user->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Validation for customer
            $validator = Validator::make($request->all(), [
                'status' => 'required|string|in:Selesai',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()
                ], 400);
            }

            // Update status to "Selesai" for customer
            $order->status = $request->status;
            $order->save();

            return response()->json([
                'status' => true,
                'message' => 'Order berhasil diupdate',
                'data' => $order
            ]);
        }
    }








    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        $product = Product::find($order->product_id);

        // Return the stock back
        $product->stock += $order->quantity;
        $product->save();

        $order->delete();

        return response()->json([
            'status' => true,
            'message' => 'Order berhasil dihapus'
        ]);
    }

    private function formatRupiah($number)
    {
        return 'Rp ' . number_format($number, 3, ',', '.');
    }
}
