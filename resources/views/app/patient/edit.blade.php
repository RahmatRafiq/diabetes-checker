<!-- resources/views/app/patient/edit.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Pasien</h1>
    <form action="{{ route('patients.update', $patient->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Input for User Data -->
        <div class="card mb-4">
            <div class="card-header">Informasi User</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $patient->user->name }}"
                        required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ $patient->user->email }}"
                        required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password (Opsional)</label>
                    <input type="password" class="form-control" id="password" name="password">
                    <small class="text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                </div>
            </div>
        </div>

        <!-- Input for Patient Data -->
        <div class="card mb-4">
            <div class="card-header">Informasi Pasien</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="dob" class="form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control" id="dob" name="dob" value="{{ $patient->dob }}" required>
                </div>

                <div class="mb-3">
                    <label for="gender" class="form-label">Jenis Kelamin</label>
                    <select class="form-control" id="gender" name="gender" required>
                        <option value="Laki-laki" {{ $patient->gender === 'Laki-laki' ? 'selected' : '' }}>Laki-laki
                        </option>
                        <option value="Perempuan" {{ $patient->gender === 'Perempuan' ? 'selected' : '' }}>Perempuan
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="contact" class="form-label">Kontak</label>
                    <input type="text" class="form-control" id="contact" name="contact" value="{{ $patient->contact }}"
                        required>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Alamat</label>
                    <textarea class="form-control" id="address" name="address" rows="3"
                        required>{{ $patient->address }}</textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>
@endsection