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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //     // $orders = Order::with(['user', 'product'])->get();
    //     // $orders->transform(function ($order) {
    //     //     $order->subTotal = $this->formatRupiah($order->subTotal);
    //     //     return $order;
    //     // });

    //     // return response()->json([
    //     //     'status' => true,
    //     //     'message' => 'Data order berhasil ditampilkan',
    //     //     'data' => $orders
    //     // ]);

    //     $user = auth()->
    // }
    public function index(Request $request)
    {
        // Mendapatkan pesanan berdasarkan pengguna yang membuatnya
        $orders = Order::with(['user', 'cart.product'])->where('user_id', auth()->id())->get();

        // Mengonversi total pembayaran ke dalam format Rupiah
        $orders->transform(function ($order) {
            $order->totalPayment = $this->formatRupiah($order->totalPayment);
            return $order;
        });

        return response()->json([
            'status' => true,
            'message' => 'Data pesanan berhasil ditampilkan',
            'data' => $orders
        ]);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    // public function store(Request $request)
    // {
    //     // Validate the request
    //     $validator = Validator::make($request->all(), [
    //         'cart_id' => 'required|array|min:1',
    //         'cart_id.*' => 'exists:cart,id',  // Corrected here
    //     ]);

    //     // Check if the validation fails
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     // Retrieve the cart items
    //     $cartItems = Cart::whereIn('id', $request->input('cart_id'))
    //         ->where('user_id', auth()->id())
    //         ->get();

    //     if ($cartItems->isEmpty()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'No valid cart items found'
    //         ], 404);
    //     }


    //     // Calculate the total payment
    //     $totalPayment = $cartItems->sum('totalPrice');

    //     // Create a new order
    //     $order = Order::create([
    //         'user_id' => auth()->id(),
    //         'cart_id' => $request->input('cart_id')[0],
    //         'totalPayment' => $totalPayment,
    //         'status' => 'Belum Dibayar'
    //     ]);

    //     // Attach cart items to the order
    //     foreach ($cartItems as $cartItem) {
    //         $cartItem->update(['order_id' => $order->id]);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Order berhasil dibuat',
    //         'data' => $order
    //     ]);
    // }




    public function store(Request $request)
    {
        // dd($request->all());

        // dd($request->cart_id);
        // Validate the request
        $validator = Validator::make($request->all(), [
            'cart' => 'required|array|min:1',
            'cart.*' => 'exists:cart,id',  // Validate against 'carts' table
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
                'status' => 'Belum Dibayar'
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






    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'cart_id' => 'required|exists:cart,id',
    //         'status' => 'required|in:Sudah Dibayar,Belum Dibayar',
    //     ]);

    //     $user_id = Auth::id();

    //     // Retrieve the cart and ensure it belongs to the authenticated user
    //     $cart = Cart::where('id', $request->cart_id)
    //         ->where('user_id', $user_id)
    //         ->firstOrFail();

    //     // Get the total price from the cart
    //     $totalPayment = $cart->totalPrice;

    //     // Create the order
    //     $order = Order::create([
    //         'user_id' => auth()->id(),
    //         'cart_id' => $request->cart_id,
    //         'totalPayment' => $totalPayment,
    //         'status' => $request->status,
    //     ]);

    //     return response()->json([
    //         'message' => 'Order created successfully',
    //         'order' => $order,
    //     ], 201);
    // }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        if (Auth::user()->role === 'admin') {
            // Validasi untuk admin
            $validator = Validator::make($request->all(), [
                'status' => 'required|string',
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
            // Validasi untuk pelanggan
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()
                ], 400);
            }

            $product = Product::find($request->product_id);

            // Kembalikan stok sebelumnya jika quantity baru lebih kecil
            $quantityDifference = $request->quantity - $order->quantity;
            if ($quantityDifference < 0) {
                $product->stock += $order->quantity - $request->quantity;
            }

            // Periksa apakah stok produk cukup untuk quantity baru
            if ($product->stock < $quantityDifference) {
                return response()->json([
                    'status' => false,
                    'message' => 'Stok tidak cukup'
                ], 400);
            }

            // Kurangi stok dengan selisih antara quantity yang baru dan lama
            $product->stock -= $quantityDifference;
            $product->save();

            // Perbarui order
            $order->product_id = $request->product_id;
            $order->quantity = $request->quantity;
            $order->subTotal = $request->quantity * $product->price;
            $order->subTotal = $order->subTotal;
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
