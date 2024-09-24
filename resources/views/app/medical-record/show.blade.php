@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card mb-4">
        <div class="card-header">
            <h3>Detail Rekam Medis</h3>
        </div>
        <div class="card-body">
            <h5>Nama Pasien: {{ $record->patient->name }}</h5>
            <p><strong>Tanggal Diagnosa:</strong> {{ $record->created_at->format('d M Y') }}</p>
            <p><strong>Hasil Diagnosa:</strong> {{ $record->hasil }}</p>
            <p><strong>Kategori Risiko:</strong> {{ $record->kategori_risiko }}</p>
            <p><strong>Angiopati:</strong> {{ $record->angiopati }}</p>
            <p><strong>Neuropati:</strong> {{ $record->neuropati }}</p>
            <p><strong>Deformitas:</strong> {{ $record->deformitas }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('medical-records.index') }}" class="btn btn-secondary">Kembali ke Rekam Medis</a>
        </div>
    </div>
</div>
@endsection
