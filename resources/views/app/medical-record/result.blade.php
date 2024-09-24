@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Hasil Diagnosa</h1>
    <div class="alert alert-info">
        <h2>Kategori Risiko: {{ $kategori }}</h2>
        <p>{{ $hasil }}</p>
    </div>
    <a href="{{ route('medical-record.index') }}" class="btn btn-primary">Kembali ke Daftar Pasien</a>
</div>
@endsection