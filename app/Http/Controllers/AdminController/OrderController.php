<?php

namespace App\Http\Controllers\AdminController;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        if (Auth::user()->role === 'admin') {
            // Admin: Menampilkan semua pesanan
            $orders = Order::with(['user', 'product'])->get();
        } else {
            // Pelanggan: Menampilkan hanya pesanan mereka sendiri
            $orders = Order::with(['user', 'product'])->where('user_id', Auth::id())->get();
        }

        // Format subTotal menggunakan formatRupiah
        $orders->transform(function ($order) {
            $order->subTotal = $this->formatRupiah($order->subTotal);
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'status' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return back()->with('failed','order failed');
        }

        $product = Product::find($request->product_id);

        if ($product->stock < $request->quantity) {
            return back()->with('stock','stock is low');
        }

        $subTotal = $request->quantity * $product->price;

        $order = new Order();
        $order->user_id = auth()->id();  // Get the ID of the authenticated user
        $order->product_id = $request->product_id;
        $order->quantity = $request->quantity;
        $order->subTotal = $subTotal;
        $order->status = 'Belum Dibayar';
        $order->save();

        // Update product stock
        $product->stock -= $request->quantity;
        $product->save();

        $order->subTotal = $this->formatRupiah($order->subTotal);

        return back()->with('order','order is success');
    }

    private function codeUnique()
    {
        $uniqueCode = substr(md5(uniqid(mt_rand(), true)), 0, 8);
        return strtoupper($uniqueCode);
    }


    public function storeCart(Request $request)
    {
        $code = $this->codeUnique();
       
        foreach ($request->data as $item) {

            $validator = Validator::make($item->all(), [
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'status' => 'sometimes|string',
            ]);
    
            if ($validator->fails()) {
                return back()->with('failed','order failed');
            }

            $product = Product::find($item->product_id);

            if ($product->stock < $item->quantity) {
                return back()->with('stock','stock is low');
            }
            $subTotal = $item->quantity * $product->price;

            $order = new Order();
            $order->user_id = auth()->id();  // Get the ID of the authenticated user
            $order->product_id = $item->product_id;
            $order->quantity = $item->quantity;
            $order->subTotal = $subTotal;
            $order->status = 'Belum Dibayar';
            $order->transaction_code = $code;
            $order->save();
    
            // Update product stock
            $product->stock -= $item->quantity;
            $product->save();
    
            $order->subTotal = $this->formatRupiah($order->subTotal);
    
        }

        return back()->with('order','order is success');
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        return response()->json([
            'status' => true,
            'message' => 'Data order berhasil ditampilkan',
            'data' => $order
        ]);
    }

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
