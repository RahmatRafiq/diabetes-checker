@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">Detail Rekam Medis Pasien</h3>
        </div>
        <div class="card-body">
            <!-- Section: Informasi Pasien -->
            <h4 class="mb-3 text-center">Informasi Pasien</h4>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nama Pasien:</strong> {{ $record->patient->name }}</p>
                    <p><strong>Tanggal Lahir:</strong> {{ \Carbon\Carbon::parse($record->patient->dob)->format('d M Y') }}</p>
                    <p><strong>Jenis Kelamin:</strong> {{ $record->patient->gender }}</p>
                    <p><strong>Kontak:</strong> {{ $record->patient->contact }}</p>
                    <p><strong>Alamat:</strong> {{ $record->patient->address }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Tingkat Pendidikan:</strong> {{ $record->patient->education_level ?? 'Data tidak tersedia' }}</p>
                    <p><strong>Pekerjaan:</strong> {{ $record->patient->occupation ?? 'Data tidak tersedia' }}</p>
                    <p><strong>Berat Badan:</strong> {{ $record->patient->weight ? $record->patient->weight . ' kg' : 'Data tidak tersedia' }}</p>
                    <p><strong>Tinggi Badan:</strong> {{ $record->patient->height ? $record->patient->height . ' cm' : 'Data tidak tersedia' }}</p>
                    <p><strong>Lama Menderita Diabetes:</strong> {{ $record->patient->years_with_diabetes ? $record->patient->years_with_diabetes . ' tahun' : 'Data tidak tersedia' }}</p>
                </div>
            </div>

            <!-- Divider -->
            <hr class="my-4">

            <!-- Section: Informasi Medis -->
            <h4 class="mb-3 text-center">Informasi Medis</h4>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Terapi DM:</strong> {{ $record->patient->dm_therapy ?? 'Data tidak tersedia' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Nilai GDS:</strong> {{ $record->patient->gds ? $record->patient->gds . ' mg/dL' : 'Data tidak tersedia' }}</p>
                    <p><strong>Nilai HbA1c:</strong> {{ $record->patient->hba1c ? $record->patient->hba1c . ' %' : 'Data tidak tersedia' }}</p>
                    <p><strong>Jenis Diet:</strong> {{ $record->patient->diet_type ?? 'Data tidak tersedia' }}</p>
                </div>
            </div>

            <!-- Divider -->
            <hr class="my-4">

            <!-- Section: Hasil Diagnosa -->
            <h4 class="mb-3 text-center">Hasil Diagnosa</h4>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Tanggal Diagnosa:</strong> {{ $record->created_at->format('d M Y') }}</p>
                    <p><strong>Kategori Risiko:</strong> {{ $record->kategori_risiko }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Hasil Diagnosa:</strong> {{ $record->hasil }}</p>
                    <p><strong>Angiopati:</strong> {{ $record->angiopati }}</p>
                    <p><strong>Neuropati:</strong> {{ $record->neuropati }}</p>
                    <p><strong>Deformitas:</strong> {{ $record->deformitas }}</p>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('medical-records.index') }}" class="btn btn-secondary">Kembali ke Rekam Medis</a>
        </div>
    </div>
</div>
@endsection
