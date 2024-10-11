@extends('layouts.app')

@section('content')
<div class="card mb-3">
    <div class="alert alert-info" role="alert">
        <h4 class="alert-heading">Data Rekam Medis Pasien</h4>
    </div>
    <div class="card-body">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title">Data Rekam Medis Pasien</h5>
        </div>
        <div class="table-responsive">
            <table class="table styled-table" id="medical-records">
                <thead>
                    <tr>
                        <th>Nama Pasien</th>
                        <th>Angiopati</th>
                        <th>Neuropati</th>
                        <th>Deformitas</th>
                        <th>Kategori Risiko</th>
                        <th>Hasil</th>
                        <th>Aksi</th>
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
@endpush

@push('javascript')
<script src="{{ asset('assets/DataTables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#medical-records').DataTable({
            responsive: true,
            serverSide: true,
            processing: true,
            paging: true,
            ajax: {
                url: '{{ route('medical-record.json') }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [
                { data: 'patient_name' },
                { data: 'angiopati' },
                { data: 'neuropati' },
                { data: 'deformitas' },
                { data: 'kategori_risiko' },
                { data: 'hasil' },
                { 
                    data: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        const detailButton = '<a href="{{ route('medical-records.show', ':id') }}" class="btn btn-info btn-sm me-2">Detail</a>'.replace(':id', row.id);
                        const editButton = '<a href="/medical-records/' + row.id + '/edit" class="btn btn-primary btn-sm">Edit</a>';
                        return '<div class="d-flex">' + detailButton + editButton + '</div>';
                    }
                }
            ]
        });
    });
</script>
@endpush
