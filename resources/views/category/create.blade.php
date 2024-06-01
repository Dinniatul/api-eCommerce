@extends('layouts.main')
@section('container')

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        DataTable Category
    </div>
    <div class="card-body">
        <form action="{{route('category.store')}}" method="POST">
            @csrf
            @method('post')
            <div class="mb-3">
                <label for="name">Category Name</label>
                <input class="form-control"  name="category_name" type="text" placeholder="Masukkan Category Name">
            </div>
            <div class="mb-3">
                <label for="email">Description</label>
                <input class="form-control"  name="description" type="text" placeholder="Masukkan Description">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success">Submit Data</button>
            </div>
        </form>

    </div>
</div>

@endsection