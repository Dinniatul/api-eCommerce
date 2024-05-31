@extends('layouts.main')
@section('container')

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        DataTable User
    </div>
    <div class="card-body">
        <div class="panel-body">
            <a href="{{route('user.create')}}" class="btn btn-md btn-success mb-3">TAMBAH DATA</a>
        </div>
        <div class="table-responsive w-100">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <th class="text-center">No</th>
                    <th class="text-center">Username</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Nama Depan</th>
                    <th class="text-center">Nama Belakang</th>
                    <th class="text-center">Telephone</th>
                    <th class="text-center">Alamat</th>
                    <th class="text-center">Role</th>
                    <th class="text-center">Aksi</th>
                </thead>
                <tbody>
                    @php
                        $no = 1;
                    @endphp
                    @forelse ($users as $data)
                        <tr>
                            <td class="text-center">{{ $no++}}</td>
                            <td class="text-center">{{ $data->username}}</td>
                            <td class="text-center">{{ $data->email}}</td>
                            <td class="text-center">{{ $data->first_name}}</td>
                            <td class="text-center">{{ $data->last_name}}</td>
                            <td class="text-center">{{ $data->phone}}</td>
                            <td class="text-center">{{ $data->address}}</td>
                            <td class="text-center">{{ $data->role}}</td>
                            <td class="text-center">
                                <a href="{{route('user.edit',['user'=>$data->id])}}" class="btn btn-warning btn-circle">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <form onsubmit="return confirm('Apakah Anda Yakin ?');" action="{{route('user.destroy', ['user'=>$data->id])}}" method="POST">
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