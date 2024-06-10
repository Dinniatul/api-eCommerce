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

        return view('order.index', ['order' => $order]);
    }



    public function show(Order $order)
    {
        return view('order.show');
    }



    // public function update(Request $request, $id)
    // {
    //     $order = Order::find($id);

    //     if (!$order) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Order tidak ditemukan'
    //         ], 404);
    //     }

    //     (Auth::user()->role === 'admin') {
    //         // Validasi untuk admin
    //         $validator = Validator::make($request->all(), [
    //             'status' => 'required|string',
    //         ]);

    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => $validator->errors()
    //             ], 400);
    //         }

    //         $order->status = $request->status;
    //         $order->save();

    //         return view('order.index');
    //     }
    // }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return redirect()->route('order.index')->with('success', 'Status updated successfully.');
    }






    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Order $order)
    // {
    //     $product = Product::find($order->product_id);

    //     // Return the stock back
    //     $product->stock += $order->quantity;
    //     $product->save();

    //     $order->delete();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Order berhasil dihapus'
    //     ]);
    // }

    private function formatRupiah($number)
    {
        return 'Rp ' . number_format($number, 3, ',', '.');
    }
}
