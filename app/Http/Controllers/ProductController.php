<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();
        if ($products->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Data produk kosong',
                'data' => []
            ], 200);
        }

        // Jika ada kategoriu
        return response()->json([
            'status' => true,
            'message' => 'Data produk berhasil ditampilkan',
            'data' => $products
        ], 200);
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
        $category = Categories::all();
        $validator = Validator::make($request->all(), [
            'product_name' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'category_id' => 'required',

        ], [
            'product_name.required' => 'Nama Produk tidak boleh kosong',
            // 'image.image' => 'Harus Gambar',
            'description.required' => 'Deskripsi tidak boleh kosong',
            'price.required' => 'Harga tidak boleh kosong',
            'stock.required' => 'Stok tidak boleh kosong',
            'category_id.required' => 'Harus pilih Kategori',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Produk gagal ditambahkan',
                'data' => $validator->errors()
            ], 422);
        }

        $image = $request->file('image');
        // $nama_file = time() . '_' . $image->getClientOriginalName();
        $nama_file = $image->getClientOriginalName();
        // $lokasi_file = public_path('/public/images');
        $lokasi_file = public_path('/image');
        $image->move($lokasi_file, $nama_file);



        $products = Product::create([
            'product_name' => $request->input('product_name'),
            // 'image' => public_path('images/' . $nama_file),
            'image' => $nama_file,
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'stock' => $request->input('stock'),
            'category_id' => $request->input('category_id'),
        ]);

        $products->category = $products->category;
        return response()->json([
            'status' => true,
            'message' => 'Produk berhasil ditambahkan',
            'data' => $products
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        $category = Categories::all();
        $validator = Validator::make($request->all(), [
            'product_name' => 'sometimes|required',
            'image' => 'sometimes|required|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'sometimes|required',
            'price' => 'sometimes|required',
            'stock' => 'sometimes|required',
            'category_id' => 'sometimes|required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Produk gagal diupdate',
                'data' => $validator->errors()
            ], 422);
        }

        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        $product->update($request->only(['product_name', 'description', 'price', 'stock', 'category_id']));

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $nama_file = time() . '_' . $image->getClientOriginalName();
            $lokasi_file = public_path('/images');
            $image->move($lokasi_file, $nama_file);

            // Delete old image
            if (File::exists(public_path($product->image))) {
                File::delete(public_path($product->image));
            }

            // Update with new image path
            $product->image = 'images/' . $nama_file;
            $product->save();
        }

        $product->category = $product->category;

        return response()->json([
            'status' => true,
            'message' => 'Produk berhasil diupdate',
            'data' => $product
        ]);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        // Delete image
        if (File::exists(public_path($product->image))) {
            File::delete(public_path($product->image));
        }

        $product->delete();

        return response()->json([
            'status' => true,
            'message' => 'Produk berhasil dihapus',
        ]);
    }
}
