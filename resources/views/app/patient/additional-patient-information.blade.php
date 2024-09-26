@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="h3 mb-3">Edit Profil Pasien {{ $patient->user->name }}</h2> 

    <!-- Toast Notification -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 5">
        <div id="toast-success" class="toast align-items-center text-white bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
            <div class="d-flex">
                <div class="toast-body">
                    {{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    @if(session('success'))
    <script>
        var toastSuccess = new bootstrap.Toast(document.getElementById('toast-success'));
            toastSuccess.show();
    </script>
    @endif

    <form method="POST" action="{{ route('patient.profile.update') }}">
        @csrf

        <!-- Informasi Dasar -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title">Informasi Dasar</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <!-- Tanggal Lahir -->
                    <div class="col-md-6">
                        <label for="dob" class="form-label">Tanggal Lahir</label>
                        <input type="date" class="form-control" id="dob" name="dob"
                            value="{{ old('dob', $patient->dob->format('Y-m-d')) }}" required>
                    </div>
                    <!-- Jenis Kelamin -->
                    <div class="col-md-6">
                        <label for="gender" class="form-label">Jenis Kelamin</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="male" {{ old('gender', $patient->gender) == 'male' ? 'selected' : ''
                                }}>Laki-laki</option>
                            <option value="female" {{ old('gender', $patient->gender) == 'female' ? 'selected' : ''
                                }}>Perempuan</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <!-- Kontak -->
                    <div class="col-md-6">
                        <label for="contact" class="form-label">Kontak</label>
                        <input type="text" class="form-control" id="contact" name="contact"
                            value="{{ old('contact', $patient->contact) }}" required>
                    </div>
                    <!-- Alamat -->
                    <div class="col-md-6">
                        <label for="address" class="form-label">Alamat</label>
                        <input type="text" class="form-control" id="address" name="address"
                            value="{{ old('address', $patient->address) }}" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Kesehatan -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="card-title">Informasi Kesehatan</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <!-- Berat Badan -->
                    <div class="col-md-6">
                        <label for="weight" class="form-label">Berat Badan (kg)</label>
                        <input type="number" class="form-control" id="weight" name="weight"
                            value="{{ old('weight', $patient->weight) }}">
                    </div>
                    <!-- Tinggi Badan -->
                    <div class="col-md-6">
                        <label for="height" class="form-label">Tinggi Badan (cm)</label>
                        <input type="number" class="form-control" id="height" name="height"
                            value="{{ old('height', $patient->height) }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <!-- Tahun Dengan Diabetes -->
                    <div class="col-md-6">
                        <label for="years_with_diabetes" class="form-label">Tahun dengan Diabetes</label>
                        <input type="number" class="form-control" id="years_with_diabetes" name="years_with_diabetes"
                            value="{{ old('years_with_diabetes', $patient->years_with_diabetes) }}">
                    </div>
                    <!-- Terapi DM -->
                    <div class="col-md-6">
                        <label for="dm_therapy" class="form-label">Terapi DM</label>
                        <input type="text" class="form-control" id="dm_therapy" name="dm_therapy"
                            value="{{ old('dm_therapy', $patient->dm_therapy) }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <!-- GDS -->
                    <div class="col-md-6">
                        <label for="gds" class="form-label">GDS</label>
                        <input type="number" step="0.1" class="form-control" id="gds" name="gds"
                            value="{{ old('gds', $patient->gds) }}">
                    </div>
                    <!-- HbA1c -->
                    <div class="col-md-6">
                        <label for="hba1c" class="form-label">HbA1c (%)</label>
                        <input type="number" step="0.1" class="form-control" id="hba1c" name="hba1c"
                            value="{{ old('hba1c', $patient->hba1c) }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Tambahan -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="card-title">Informasi Tambahan</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <!-- Tingkat Pendidikan -->
                    <div class="col-md-6">
                        <label for="education_level" class="form-label">Tingkat Pendidikan</label>
                        <input type="text" class="form-control" id="education_level" name="education_level"
                            value="{{ old('education_level', $patient->education_level) }}">
                    </div>
                    <!-- Pekerjaan -->
                    <div class="col-md-6">
                        <label for="occupation" class="form-label">Pekerjaan</label>
                        <input type="text" class="form-control" id="occupation" name="occupation"
                            value="{{ old('occupation', $patient->occupation) }}">
                    </div>
                </div>
                <div class="mb-3">
                    <!-- Tipe Diet -->
                    <label for="diet_type" class="form-label">Tipe Diet</label>
                    <input type="text" class="form-control" id="diet_type" name="diet_type"
                        value="{{ old('diet_type', $patient->diet_type) }}">
                </div>
            </div>
        </div>

        <!-- Tombol Submit -->
        <div class="text-center">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection


@push('javascript')
<script>
    // Menampilkan toast success jika ada pesan sukses di session
    @if(session('success'))
        var toastSuccess = new bootstrap.Toast(document.getElementById('toast-success'));
        toastSuccess.show();
    @endif

    // Menampilkan toast error jika ada pesan error
    @if($errors->any())
        var toastError = new bootstrap.Toast(document.getElementById('toast-error'));
        toastError.show();
    @endif
</script>
@endpush