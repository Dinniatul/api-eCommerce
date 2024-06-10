@extends('layouts.main')
@section('container')
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Konfirmasi Status Pembayaran
        </div>
        <div class="card-body">
            <div class="table-responsive w-100">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Nama Customer</th>
                            <th class="text-center">Total Pembayaran</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @forelse ($order as $data)
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td class="text-center">{{ $data->user->first_name }} {{ $data->user->last_name }}</td>
                                <td class="text-center">{{ $data->totalPayment }}</td>
                                <td class="text-center">{{ $data->status }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning btn-circle" data-bs-toggle="modal"
                                        data-bs-target="#editStatusModal-{{ $data->id }}">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <form onsubmit="return confirm('Apakah Anda Yakin ?');" action="" method="POST"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger btn-circle">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Status Modal -->
                            <div class="modal fade" id="editStatusModal-{{ $data->id }}" tabindex="-1"
                                aria-labelledby="editStatusModalLabel-{{ $data->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editStatusModalLabel-{{ $data->id }}">Edit
                                                Status Pembayaran</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('order.update', $data->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="status-{{ $data->id }}"
                                                        class="form-label">Status</label>
                                                    <select class="form-select" id="status-{{ $data->id }}"
                                                        name="status">
                                                        <option value="Belum Dibayar"
                                                            {{ $data->status == 'Belum Dibayar' ? 'selected' : '' }}>Belum
                                                            Dibayar
                                                        </option>
                                                        <option value="Sudah Dibayar"
                                                            {{ $data->status == 'Sudah Dibayar' ? 'selected' : '' }}>Sudah
                                                            Dibayar</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No Data Available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
