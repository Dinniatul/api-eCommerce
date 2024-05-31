<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Trusthub;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Categories::all();

        // Jika tidak ada kategori
        if ($categories->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Data kategori kosong',
                'categories' => []
            ], 200);
        }

        // Jika ada kategori
        return response()->json([
            'status' => true,
            'message' => 'Data kategori berhasil ditampilkan',
            'categories' => $categories
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
        $validator = Validator::make($request->all(), [
            'category_name' => 'required',
            'description' => 'required',

        ], [
            'category_name.required' => 'Kategori tidak boleh kosong',
            'description.required' => " Deskripsi tidak boleh kosong",

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => "Proses tambah kategori gagal",
                'data' => $validator->errors()
            ], 401);
        }

        $categories = Categories::create([
            'category_name' => $request->input('category_name'),
            'description' => $request->input('description'),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Kategori berhasil ditambahkan',
            'data' => $categories
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Categories  $categories
     * @return \Illuminate\Http\Response
     */
    public function show(Categories $categories)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Categories  $categories
     * @return \Illuminate\Http\Response
     */
    public function edit(Categories $categories)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Categories  $categories
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $categories = Categories::find($id);
        $validator = Validator::make($request->all(), [
            'category_name' => 'required',
            'description' => 'required',

        ], [
            'category_name.required' => 'Kategori tidak boleh kosong',
            'description.required' => " Deskripsi tidak boleh kosong",

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => "Proses Update kategori gagal",
                'data' => $validator->errors()
            ], 401);
        }

        $categories->update([
            'category_name' => $request->input('category_name'),
            'description' => $request->input('description'),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Kategori berhasil diupdate',
            'data' => $categories
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Categories  $categories
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $categories = Categories::find($id);
        $categories->delete();
        return response()->json([
            'status' => true,
            'message' => 'Kategori Berhasil dihapus'
        ], 200);
    }
}
