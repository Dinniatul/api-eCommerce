<?php

namespace App\Http\Controllers\AdminController;

use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
   
   
    public function index()
    {
        $order = Order::latest()->get();

        return view('order.index',['order'=>$order]);
    }


    public function create()
    {
        $user = User::all();
        $product = Product::all();
        return view('product.create',['user'=>$user,'product'=>$product]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'status' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return back()->with('failed', 'Order failed');
        }

        $product = Product::find($request->product_id);

        if ($product->stock < $request->quantity) {
            return back()->with('stock', 'Stock is low');
        }

        $subTotal = $request->quantity * $product->price;

        $order = new Order();
        $order->user_id = $request->user_id;
        $order->product_id = $request->product_id;
        $order->quantity = $request->quantity;
        $order->subTotal = $subTotal;
        $order->status = 'Belum Dibayar';
        $order->save();

        // Update product stock
        $product->stock -= $request->quantity;
        $product->save();

        return redirect()->route('admin.orders.index')->with('success', 'Order created successfully.');
    }


    // public function store(Request $request)
    // {
    //     $carts = Cart::all();

    //     if ($carts->isEmpty()) {
    //         return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
    //     }

    //     foreach ($carts as $cart) {
    //         Order::create([
    //             'user_id' => $cart->user_id,
    //             'product_id' => $cart->product_id,
    //             'quantity' => $cart->quantity,
    //             'subTotal' => $cart->product->price * $cart->quantity,
    //             'status' => 'Belum Dibayar',
    //         ]);
    //     }

    //     Cart::where('user_id', Auth::id())->delete();

    //     return redirect()->route('order.index')->with('success', 'Order placed successfully.');
    // }




    public function storeCart(Request $request) 
    {
       
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
            $order->save();
    
            // Update product stock
            $product->stock -= $item->quantity;
            $product->save();
    
            $order->subTotal = $this->formatRupiah($order->subTotal);
    
        }

        return back()->with('order','order is success');
    }


    public function show(Order $order)
    {
        return response()->json([
            'status' => true,
            'message' => 'Data order berhasil ditampilkan',
            'data' => $order
        ]);
    }

 
    public function edit(Order $order)
    {
        //
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
