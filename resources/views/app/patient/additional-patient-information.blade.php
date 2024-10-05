@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="h3 mb-3">Edit Profil Pasien {{ $patient->user->name }}</h2>

    <!-- Toast Notification -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 5">
        @if(session('success'))
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
        @endif

        @if($errors->any())
        <div id="toast-error" class="toast align-items-center text-white bg-danger border-0" role="alert"
            aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
            <div class="d-flex">
                <div class="toast-body">
                    Terjadi kesalahan pada form, mohon periksa kembali.
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
        @endif
    </div>

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
                        <input type="date" class="form-control @error('dob') is-invalid @enderror" id="dob" name="dob"
                            value="{{ old('dob', $patient->dob->format('Y-m-d')) }}" required>
                        @error('dob')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <!-- Jenis Kelamin -->
                    <div class="col-md-6">
                        <label for="gender" class="form-label">Jenis Kelamin</label>
                        <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender"
                            required>
                            <option value="male" {{ old('gender', $patient->gender) == 'male' ? 'selected' : ''
                                }}>Laki-laki</option>
                            <option value="female" {{ old('gender', $patient->gender) == 'female' ? 'selected' : ''
                                }}>Perempuan</option>
                        </select>
                        @error('gender')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <!-- Kontak -->
                    <div class="col-md-6">
                        <label for="contact" class="form-label">Kontak</label>
                        <input type="text" class="form-control @error('contact') is-invalid @enderror" id="contact"
                            name="contact" value="{{ old('contact', $patient->contact) }}" required>
                        @error('contact')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <!-- Alamat -->
                    <div class="col-md-6">
                        <label for="address" class="form-label">Alamat</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address"
                            name="address" value="{{ old('address', $patient->address) }}" required>
                        @error('address')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
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
                        <input type="number" class="form-control @error('weight') is-invalid @enderror" id="weight"
                            name="weight" value="{{ old('weight', $patient->weight) }}">
                        @error('weight')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <!-- Tinggi Badan -->
                    <div class="col-md-6">
                        <label for="height" class="form-label">Tinggi Badan (cm)</label>
                        <input type="number" class="form-control @error('height') is-invalid @enderror" id="height"
                            name="height" value="{{ old('height', $patient->height) }}">
                        @error('height')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <!-- Tahun Dengan Diabetes -->
                    <div class="col-md-6">
                        <label for="years_with_diabetes" class="form-label">Tahun dengan Diabetes</label>
                        <input type="number" class="form-control @error('years_with_diabetes') is-invalid @enderror"
                            id="years_with_diabetes" name="years_with_diabetes"
                            value="{{ old('years_with_diabetes', $patient->years_with_diabetes) }}">
                        @error('years_with_diabetes')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <!-- Terapi DM -->
                    <div class="col-md-6">
                        <label for="dm_therapy" class="form-label">Terapi DM</label>
                        <select class="form-select @error('dm_therapy') is-invalid @enderror" id="dm_therapy"
                            name="dm_therapy">
                            <option value="diet" {{ old('dm_therapy', $patient->dm_therapy) == 'diet' ? 'selected' : ''
                                }}>Diet Saja</option>
                            <option value="oral_medication" {{ old('dm_therapy', $patient->dm_therapy) ==
                                'oral_medication' ? 'selected' : '' }}>Obat Oral</option>
                            <option value="insulin" {{ old('dm_therapy', $patient->dm_therapy) == 'insulin' ? 'selected'
                                : '' }}>Insulin</option>
                            <option value="oral_and_insulin" {{ old('dm_therapy', $patient->dm_therapy) ==
                                'oral_and_insulin' ? 'selected' : '' }}>Kombinasi Obat Oral dan Insulin</option>
                            <option value="none" {{ old('dm_therapy', $patient->dm_therapy) == 'none' ? 'selected' : ''
                                }}>Tidak Ada Terapi</option>
                        </select>
                        @error('dm_therapy')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <!-- GDS -->
                    <div class="col-md-6">
                        <label for="gds" class="form-label">GDS</label>
                        <input type="number" step="0.1" class="form-control @error('gds') is-invalid @enderror" id="gds"
                            name="gds" value="{{ old('gds', $patient->gds) }}">
                        @error('gds')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <!-- HbA1c -->
                    <div class="col-md-6">
                        <label for="hba1c" class="form-label">HbA1c (%)</label>
                        <input type="number" step="0.1" class="form-control @error('hba1c') is-invalid @enderror"
                            id="hba1c" name="hba1c" value="{{ old('hba1c', $patient->hba1c) }}">
                        @error('hba1c')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
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
                        <select class="form-select @error('education_level') is-invalid @enderror" id="education_level"
                            name="education_level">
                            <option value="none" {{ old('education_level', $patient->education_level) == 'none' ?
                                'selected' : '' }}>Tidak Sekolah</option>
                            <option value="sd" {{ old('education_level', $patient->education_level) == 'sd' ? 'selected'
                                : '' }}>SD/Sederajat</option>
                            <option value="smp" {{ old('education_level', $patient->education_level) == 'smp' ?
                                'selected' : '' }}>SMP/Sederajat</option>
                            <option value="sma" {{ old('education_level', $patient->education_level) == 'sma' ?
                                'selected' : '' }}>SMA/Sederajat</option>
                            <option value="diploma" {{ old('education_level', $patient->education_level) == 'diploma' ?
                                'selected' : '' }}>Diploma</option>
                            <option value="sarjana" {{ old('education_level', $patient->education_level) == 'sarjana' ?
                                'selected' : '' }}>Sarjana (S1)</option>
                            <option value="magister" {{ old('education_level', $patient->education_level) == 'magister'
                                ? 'selected' : '' }}>Magister (S2)</option>
                            <option value="doktor" {{ old('education_level', $patient->education_level) == 'doktor' ?
                                'selected' : '' }}>Doktor (S3)</option>
                        </select>
                        @error('education_level')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <!-- Pekerjaan -->
                    <div class="col-md-6">
                        <label for="occupation" class="form-label">Pekerjaan</label>
                        <input type="text" class="form-control @error('occupation') is-invalid @enderror"
                            id="occupation" name="occupation" value="{{ old('occupation', $patient->occupation) }}">
                        @error('occupation')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <!-- Agama -->
                    <div class="col-md-6">
                        <label for="religion" class="form-label">Agama</label>
                        <input type="text" class="form-control @error('religion') is-invalid @enderror" id="religion"
                            name="religion" value="{{ old('religion', $patient->religion) }}">
                        @error('religion')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <!-- Suku -->
                    <div class="col-md-6">
                        <label for="ethnicity" class="form-label">Suku</label>
                        <input type="text" class="form-control @error('ethnicity') is-invalid @enderror" id="ethnicity"
                            name="ethnicity" value="{{ old('ethnicity', $patient->ethnicity) }}">
                        @error('ethnicity')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <!-- Status Pernikahan -->
                    <div class="col-md-6">
                        <label for="marital_status" class="form-label">Status Pernikahan</label>
                        <select class="form-select @error('marital_status') is-invalid @enderror" id="marital_status"
                            name="marital_status">
                            <option value="single" {{ old('marital_status', $patient->marital_status) == 'single' ?
                                'selected' : '' }}>Belum Menikah</option>
                            <option value="married" {{ old('marital_status', $patient->marital_status) == 'married' ?
                                'selected' : '' }}>Menikah</option>
                            <option value="divorced" {{ old('marital_status', $patient->marital_status) == 'divorced' ?
                                'selected' : '' }}>Cerai</option>
                        </select>
                        @error('marital_status')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <!-- Penyakit Peserta -->
                    <div class="col-md-6">
                        <label for="medical_condition" class="form-label">Penyakit Peserta</label>
                        <select class="form-select @error('medical_condition') is-invalid @enderror"
                            id="medical_condition" name="medical_condition">
                            <option value="none" {{ old('medical_condition', $patient->medical_condition) == 'none' ?
                                'selected' : '' }}>Tidak Ada Penyakit</option>
                            <option value="hypertension" {{ old('medical_condition', $patient->medical_condition) ==
                                'hypertension' ? 'selected' : '' }}>Hipertensi</option>
                            <option value="heart_disease" {{ old('medical_condition', $patient->medical_condition) ==
                                'heart_disease' ? 'selected' : '' }}>Penyakit Jantung</option>
                            <option value="high_cholesterol" {{ old('medical_condition', $patient->medical_condition) ==
                                'high_cholesterol' ? 'selected' : '' }}>Kolesterol Tinggi</option>
                            <option value="neuropathy" {{ old('medical_condition', $patient->medical_condition) ==
                                'neuropathy' ? 'selected' : '' }}>Neuropati</option>
                            <option value="retinopathy" {{ old('medical_condition', $patient->medical_condition) ==
                                'retinopathy' ? 'selected' : '' }}>Retinopati</option>
                            <option value="kidney_failure" {{ old('medical_condition', $patient->medical_condition) ==
                                'kidney_failure' ? 'selected' : '' }}>Gagal Ginjal</option>
                        </select>
                        @error('medical_condition')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <!-- Riwayat Luka -->
                <div class="col-md-6">
                    <label for="wound_history" class="form-label">Riwayat Luka</label>
                    <select class="form-select @error('wound_history') is-invalid @enderror" id="wound_history"
                        name="wound_history">
                        <option value="no_wound" {{ old('wound_history', $patient->wound_history) == 'no_wound' ?
                            'selected' : '' }}>Tidak Ada Luka</option>
                        <option value="minor_wound" {{ old('wound_history', $patient->wound_history) == 'minor_wound' ?
                            'selected' : '' }}>Luka Ringan</option>
                        <option value="moderate_wound" {{ old('wound_history', $patient->wound_history) ==
                            'moderate_wound' ? 'selected' : '' }}>Luka Sedang</option>
                        <option value="severe_wound" {{ old('wound_history', $patient->wound_history) == 'severe_wound'
                            ? 'selected' : '' }}>Luka Berat</option>
                        <option value="amputation" {{ old('wound_history', $patient->wound_history) == 'amputation' ?
                            'selected' : '' }}>Amputasi</option>
                    </select>
                    @error('wound_history')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
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
    document.addEventListener("DOMContentLoaded", function () {
        @if(session('success'))
            var toastSuccess = new bootstrap.Toast(document.getElementById('toast-success'));
            toastSuccess.show();
        @endif

        @if($errors->any())
            var toastError = new bootstrap.Toast(document.getElementById('toast-error'));
            toastError.show();
        @endif

        // Mengubah input suku menjadi huruf kecil saat mengetik atau submit
        document.getElementById('ethnicity').addEventListener('input', function() {
            this.value = this.value.toLowerCase();
        });

        document.querySelector("form").addEventListener("submit", function () {
            let ethnicityInput = document.getElementById('ethnicity');
            if (ethnicityInput) {
                ethnicityInput.value = ethnicityInput.value.toLowerCase();
            }
        });
    });
</script>
@endpush