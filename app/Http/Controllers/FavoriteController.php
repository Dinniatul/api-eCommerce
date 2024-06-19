<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavoriteController extends Controller
{
    public function getFavorite()
    {
        $user = auth()->user();
        $favorites = Favorite::where('user_id', $user->id)->with('product')->get();

        return response()->json([
            'status' => true,
            'message' => 'Data favorite berhasil ditampilkan',
            'data' => $favorites
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal memasukkan produk ke favorite',
                'errors' => $validator->errors()
            ], 400);
        }

        $favorite = Favorite::create([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id
        ]);

        $product = Product::find($request->product_id);

        $productDetail = [
            'product_name' => $product->product_name,
            'image' => $product->image,
            'description' => $product->description,
            'price' => $product->price,
            'stock' => $product->stock,
        ];

        $favorite->productDetails = $productDetail;

        return response()->json([
            'status' => true,
            'message' => 'Favorite berhasil dibuat',
            'data' => [
                'favorite' => $favorite,
            ]
        ]);
    }

    public function check(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengecek produk favorit',
                'errors' => $validator->errors()
            ], 400);
        }

        $isFavorite = Favorite::where('user_id', auth()->id())
                            ->where('product_id', $request->product_id)
                            ->exists();

        return response()->json([
            'status' => true,
            'isFavorite' => $isFavorite
        ]);
    }

    public function destroy($product_id)
    {
        // $favorite = Favorite::where('user_id', auth()->id())->where('id', $id)->first();
        $favorite = Favorite::where('user_id', auth()->id())
                        ->where('product_id', $product_id)
                        ->first();

        if (!$favorite) {
            return response()->json([
                'status' => false,
                'message' => 'Favorite tidak ditemukan atau tidak dimiliki oleh pengguna',
            ], 404);
        }

        $favorite->delete();

        return response()->json([
            'status' => true,
            'message' => 'Favorite berhasil dihapus',
        ]);
    }
}
