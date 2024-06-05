@extends('layouts.main')
@section('container')

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        DataTable Product
    </div>
    <div class="card-body">
        <div class="panel-body">
            <a href="{{route('product.create')}}" class="btn btn-md btn-success mb-3">TAMBAH DATA</a>
        </div>
        <div class="table-responsive w-100">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <th class="text-center">No</th>
                    <th class="text-center">Product Name</th>
                    <th class="text-center">Image</th>
                    <th class="text-center">Description</th>
                    <th class="text-center">Price</th>
                    <th class="text-center">Stock</th>
                    <th class="text-center">Category</th>
                    <th class="text-center">Aksi</th>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                    @endphp
                    @forelse ($products as $data)
                        <tr>
                            <td class="text-center">{{ $no++}}</td>
                            <td class="text-center">{{ $data->product_name}}</td>
                            <td class="text-center">
                                <img src="{{ Storage::url('public/images/').$data->image }}" class="rounded" style="width: 150px">
                            </td>
                            <td class="text-center">{{ $data->description}}</td>
                            <td class="text-center">{{ $data->price}}</td>
                            <td class="text-center">{{ $data->stock}}</td>
                            <td class="text-center">{{ $data->category->category_name}}</td>
                            <td class="text-center">
                                <a href="{{route('product.edit',['product'=>$data->id])}}" class="btn btn-warning btn-circle">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <form onsubmit="return confirm('Apakah Anda Yakin ?');" action="{{route('product.destroy', ['product'=>$data->id])}}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-circle">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                
                            </td>
                        </tr>
                    @empty
                        
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection