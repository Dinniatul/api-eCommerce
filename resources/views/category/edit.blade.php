@extends('layouts.main')
@section('container')

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        DataTable Category
    </div>
    <div class="card-body">
        <form action="{{route('category.update',['categories'=>$categories->id])}}" method="POST">
            @csrf
            @method('put')
            <div class="mb-3">
                <label for="name">Category Name</label>
                <input class="form-control"  name="category_name" value="{{old('category_name',$categories->category_name)}}" type="text" placeholder="Masukkan Category Name">
            </div>
            <div class="mb-3">
                <label for="email">Description</label>
                <input class="form-control"  name="description" value="{{old('description',$categories->description)}}" type="text" placeholder="Masukkan Description">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success">Submit Data</button>
            </div>
        </form>

    </div>
</div>

@endsection