@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-3">Rekam Medis Pasien</h3>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama Pasien</th>
                    <th>Tanggal Diagnosa</th>
                    <th>Hasil Diagnosa</th>
                    <th>Kategori Risiko</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($medicalRecords as $record)
                <tr>
                    <td>{{ $record->patient->name }}</td>
                    <td>{{ $record->created_at->format('d M Y') }}</td>
                    <td>{{ $record->hasil }}</td>
                    <td>{{ $record->kategori_risiko }}</td>
                    <td>
                        <a href="{{ route('medical-records.show', $record->id) }}" class="btn btn-info btn-sm">Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
