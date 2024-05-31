@extends('layouts.main')
@section('container')

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        DataTable User
    </div>
    <div class="card-body">
        <form action="{{route('user.store')}}" method="POST">
            @csrf
            @method('post')
            <div class="mb-3">
                <label for="name">Username</label>
                <input class="form-control"  name="username" type="text" placeholder="Masukkan Username">
            </div>
            <div class="mb-3">
                <label for="email">Email address</label>
                <input class="form-control"  name="email" type="email" placeholder="Masukkan Email">
            </div>
            <div class="mb-3">
                <label for="password">Password</label>
                <input class="form-control" name="password" type="text" placeholder="Masukkan Password">
            </div>
            <div class="mb-3">
                <label for="name">First Name</label>
                <input class="form-control"  name="first_name" type="text" placeholder="Masukkan First Name">
            </div>
            <div class="mb-3">
                <label for="name">Last Name</label>
                <input class="form-control"  name="last_name" type="text" placeholder="Masukkan Last Name">
            </div>
            <div class="mb-3">
                <label for="name">Phone</label>
                <input class="form-control"  name="phone" type="text" placeholder="Masukkan Phone">
            </div>
            <div class="mb-3">
                <label for="name">Address</label>
                <input class="form-control"  name="address" type="text" placeholder="Masukkan Address">
            </div>
            <div class="form-group">
                <label><b>Role</b> <span class="text-danger">*</span></label>
                <select class="form-control form-control-sm" name="role">
                    <option value="" disabled="true" selected="true">Choose Role</option>
                    <option value="admin">Admin</option>
                    <option value="customer">Customer</option>
                </select>
                <span class="text-danger error-text role_error"></span>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success">Submit Data</button>
            </div>
        </form>

    </div>
</div>

@endsection