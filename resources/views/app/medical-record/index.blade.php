@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Daftar Pasien</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Tanggal Lahir</th>
                <th>Jenis Kelamin</th>
                <th>Kontak</th>
                <th>Alamat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($patients as $patient)
                <tr>
                    <td>{{ $patient->id }}</td>
                    <td>{{ $patient->name }}</td>
                    <td>{{ $patient->dob }}</td>
                    <td>{{ $patient->gender }}</td>
                    <td>{{ $patient->contact }}</td>
                    <td>{{ $patient->address }}</td>
                    <td>
                        <a href="{{ route('medical-record.create', ['patient_id' => $patient->id]) }}" class="btn btn-primary">Cek Pasien</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
