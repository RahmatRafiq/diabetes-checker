<!-- resources/views/app/patient/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Buat Pasien Baru</h1>
    <form action="{{ route('patients.store') }}" method="POST">
        @csrf

        <!-- Input for User Data -->
        <div class="card mb-4">
            <div class="card-header">Informasi User</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                        required>
                </div>
            </div>
        </div>

        <!-- Input for Patient Data -->
        <div class="card mb-4">
            <div class="card-header">Informasi Pasien</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="dob" class="form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control" id="dob" name="dob" required>
                </div>

                <div class="mb-3">
                    <label for="gender" class="form-label">Jenis Kelamin</label>
                    <select class="form-control" id="gender" name="gender" required>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="contact" class="form-label">Kontak</label>
                    <input type="text" class="form-control" id="contact" name="contact" required>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Alamat</label>
                    <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Buat Pasien</button>
    </form>
</div>
@endsection