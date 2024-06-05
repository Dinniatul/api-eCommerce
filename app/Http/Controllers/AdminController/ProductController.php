<?php

namespace App\Http\Controllers\AdminController;

use App\Models\Product;
use App\Models\Categories;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
        $products = Product::latest()->get();

        return view('product.index',['products'=>$products]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $category = Categories::all();
        return view('product.create',['category'=>$category]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validateData = $request->validate([
            'product_name' => 'required',
            'image' => 'required|image|mimes:png,jpg,jpeg|max:2048', // Added max size validation
            'description' => 'required',
            'price' => 'required|numeric', // Ensured price is numeric
            'stock' => 'required|integer', // Ensured stock is integer
            'category_id' => 'required|exists:categories,id', // Ensured category exists in the database
        ]);
    
        // Store the image and get its filename
        $image = $request->file('image');
        $imagePath = $image->storeAs('public/images', $image->hashName());
    
        // Create the product with the validated data and stored image filename
        Product::create([
            'product_name' => $validateData['product_name'],
            'image' => $image->hashName(),
            'description' => $validateData['description'],
            'price' => $validateData['price'],
            'stock' => $validateData['stock'],
            'category_id' => $validateData['category_id'],
        ]);
    
        // Redirect to the product index with a success message
        return redirect()->route('product.index')->with('success', 'Product created successfully.');
    }

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
        $category = Categories::all();
        return view('product.edit',['product'=>$product,'category'=>$category]);
    }


    public function update(Request $request, Product $product)
{
    $validateData = $request->validate([
        'product_name' => 'required',
        'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048', // Added max size validation
        'description' => 'required',
        'price' => 'required|numeric', // Ensured price is numeric
        'stock' => 'required|integer', // Ensured stock is integer
        'category_id' => 'required|exists:categories,id', // Ensured category exists in the database
    ]);

    // If a new image is uploaded, store it and set the image path
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imagePath = $image->storeAs('public/images', $image->hashName());
        $validateData['image'] = $image->hashName();
    }

    // Update the product with the validated data
    $product->update($validateData);

    // Redirect to the product index with a success message
    return redirect()->route('product.index')->with('success', 'Product updated successfully.');
}


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product){

        $product->delete();
        return redirect()->route('product.index')->with('success','Data Berhasil di Hapus');
        
    }
}
