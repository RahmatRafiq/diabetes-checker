@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">Detail Rekam Medis Pasien</h3>
        </div>
        <div class="card-body">
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

            <hr class="my-4">

            <h4 class="mb-3 text-center">Informasi Medis</h4>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Terapi DM:</strong> {{ $record->patient->dm_therapy ?? 'Data tidak tersedia' }}</p>
                    <p><strong>BMI :</strong> {{ $bmi ? $bmi . ' kg/m² (' . $bmiCategory . ')' : 'Data tidak tersedia' }}</p>
                    <p><strong>Riwayat Luka:</strong> {{ $record->patient->wound_history ?? 'Data tidak tersedia' }}</p>
                    @if($record->patient->wound_history === 'Ya')
                        <p><strong>Deskripsi Riwayat Luka:</strong> {{ $record->patient->wound_history_description ?? 'Data tidak tersedia' }}</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <p><strong>Nilai GDS:</strong> {{ $record->patient->gds ? $record->patient->gds . ' mg/dL' : 'Data tidak tersedia' }}</p>
                    <p><strong>Nilai HbA1c:</strong> {{ $record->patient->hba1c ? $record->patient->hba1c . ' %' : 'Data tidak tersedia' }}</p>
                    <p><strong>Jenis Diet:</strong> {{ $record->patient->diet_type ?? 'Data tidak tersedia' }}</p>
                </div>
            </div>

            <hr class="my-4">

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

            <hr class="my-4">

            <h4 class="mb-3 text-center">Foto Kaki Pasien</h4>
            <div class="row mb-4">
                <div class="col-md-6 text-center">
                    @if($punggungKakiKiri)
                    <p><strong>Punggung Kaki Kiri:</strong></p>
                    <img src="{{ $punggungKakiKiri }}" alt="Punggung Kaki Kiri" class="img-fluid mb-3" style="max-height: 200px; max-width: 100%;">
                    @else
                    <p>Foto Punggung Kaki Kiri tidak tersedia.</p>
                    @endif
                </div>
                <div class="col-md-6 text-center">
                    @if($punggungKakiKanan)
                    <p><strong>Punggung Kaki Kanan:</strong></p>
                    <img src="{{ $punggungKakiKanan }}" alt="Punggung Kaki Kanan" class="img-fluid mb-3" style="max-height: 200px; max-width: 100%;">
                    @else
                    <p>Foto Punggung Kaki Kanan tidak tersedia.</p>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 text-center">
                    @if($telapakKakiKiri)
                    <p><strong>Telapak Kaki Kiri:</strong></p>
                    <img src="{{ $telapakKakiKiri }}" alt="Telapak Kaki Kiri" class="img-fluid mb-3" style="max-height: 200px; max-width: 100%;">
                    @else
                    <p>Foto Telapak Kaki Kiri tidak tersedia.</p>
                    @endif
                </div>
                <div class="col-md-6 text-center">
                    @if($telapakKakiKanan)
                    <p><strong>Telapak Kaki Kanan:</strong></p>
                    <img src="{{ $telapakKakiKanan }}" alt="Telapak Kaki Kanan" class="img-fluid mb-3" style="max-height: 200px; max-width: 100%;">
                    @else
                    <p>Foto Telapak Kaki Kanan tidak tersedia.</p>
                    @endif
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="card-footer text-end">
            <a href="{{ route('medical-records.index') }}" class="btn btn-secondary">Kembali ke Rekam Medis</a>
            <a href="{{ route('medical-records.exportPDF', $record->id) }}" class="btn btn-primary">Ekspor PDF</a>
        </div>
    </div>
</div>
@endsection
