@extends('layouts.main')
@section('container')

<div class="container">
    <h1>Buat Pesanan Baru</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form action="{{ route('orders.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="user_id">Pilih Pengguna</label>
            <select class="form-select @error('user_id') is-invalid @enderror" name="user_id">
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
            @error('user_id')
                <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="product_id">Pilih Produk</label>
            <select class="form-select @error('product_id') is-invalid @enderror" name="product_id">
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->product_name }}</option>
                @endforeach
            </select>
            @error('product_id')
                <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="quantity">Jumlah</label>
            <input class="form-control @error('quantity') is-invalid @enderror" name="quantity" type="number" placeholder="Masukkan Jumlah" value="{{ old('quantity') }}">
            @error('quantity')
                <div class="alert alert-danger mt-2">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-success">Buat Pesanan</button>
        </div>
    </form>
</div>
