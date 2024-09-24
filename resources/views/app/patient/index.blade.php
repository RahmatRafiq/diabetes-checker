@extends('layouts.app')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title">Data Pasien</h5>
            <div class="mb-3">
                <a href="{{ route('patients.create') }}" class="btn btn-success">Tambah Pasien Baru</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table styled-table" id="patients">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Tanggal Lahir</th>
                        <th>Jenis Kelamin</th>
                        <th>Kontak</th>
                        <th>Alamat</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('assets/DataTables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/badges.css') }}">
@endpush

@push('javascript')
<script src="{{ asset('assets/DataTables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
<script>
    $('#patients').DataTable({
        responsive: true,
        serverSide: true,
        processing: true,
        paging: true,
        ajax: {
            url: '{{ route('patients.json') }}',  // Route untuk JSON data pasien
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'user.email' },
            { data: 'dob' },
            { data: 'gender' },
            { data: 'contact' },
            { data: 'address' },
            {
                data: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    // Membuat tombol edit
                    const editButton = `<a href="{{ route('patients.edit', ':id') }}" class="btn btn-primary me-2">Edit</a>`.replace(':id', row.id);
                    
                    // Membuat tombol hapus dengan konfirmasi SweetAlert
                    const deleteButton = `<button class="btn btn-danger" onclick="deleteRow(${row.id})">Delete</button>`;
                    
                    // Menggabungkan kedua tombol dalam satu container
                    return `<div class="d-flex">${editButton}${deleteButton}</div>`;
                }
            }
        ]
    });

    // Fungsi untuk menghapus baris pasien
    function deleteRow(id) {
        const url = `{{ route('patients.destroy', ':id') }}`.replace(':id', id);

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Data pasien ini tidak dapat dipulihkan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Tidak, batalkan'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        $('#patients').DataTable().ajax.reload();
                        Swal.fire('Dihapus!', 'Data pasien berhasil dihapus.', 'success');
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Gagal menghapus pasien.', 'error');
                    }
                });
            }
        });
    }
</script>
@endpush
