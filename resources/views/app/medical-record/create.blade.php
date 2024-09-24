@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Card Utama yang Membungkus Seluruh Form -->
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title">Form Diagnosa Risiko Diabetes</h3>
            <p class="card-subtitle text-muted">Silakan isi form di bawah ini untuk melakukan pemeriksaan.</p>
        </div>

        <div class="card-body">
            <form action="{{ route('medical-record.store') }}" method="POST">
                @csrf
                <input type="hidden" name="patient_id" value="{{ $patient->id }}">

                <!-- Angiopati Card -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title">Angiopati</h5>
                        <p class="card-subtitle">Pemeriksaan pembuluh darah perifer untuk mendeteksi adanya gangguan aliran darah.</p>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="jariJari1" class="form-label">Jari-Jari 1: </label>
                            <select name="jariJari1" id="jariJari1" class="form-control" required>
                                <option value="-">Tidak Teraba (-)</option>
                                <option value="+">Teraba (+)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="jariJari3" class="form-label">Jari-Jari 3: </label>
                            <select name="jariJari3" id="jariJari3" class="form-control" required>
                                <option value="-">Tidak Teraba (-)</option>
                                <option value="+">Teraba (+)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="jariJari5" class="form-label">Jari-Jari 5: </label>
                            <select name="jariJari5" id="jariJari5" class="form-control" required>
                                <option value="-">Tidak Teraba (-)</option>
                                <option value="+">Teraba (+)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Neuropati Card -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title">Neuropati</h5>
                        <p class="card-subtitle">Pemeriksaan saraf perifer untuk mendeteksi adanya kerusakan pada saraf kaki.</p>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="dorsalPedis" class="form-label">Dorsal Pedis: </label>
                            <select name="dorsalPedis" id="dorsalPedis" class="form-control" required>
                                <option value="-">Tidak Teraba (-)</option>
                                <option value="+">Teraba (+)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="plantar" class="form-label">Plantar: </label>
                            <select name="plantar" id="plantar" class="form-control" required>
                                <option value="-">Tidak Teraba (-)</option>
                                <option value="+">Teraba (+)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Deformitas Card -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white">
                        <h5 class="card-title">Deformitas</h5>
                        <p class="card-subtitle">Pemeriksaan deformitas kaki untuk mendeteksi adanya perubahan bentuk yang tidak normal.</p>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="deformitasKanan" class="form-label">Deformitas Kanan: </label>
                            <select name="deformitasKanan" id="deformitasKanan" class="form-control" required>
                                <option value="-">Tidak Ada (-)</option>
                                <option value="+">Ada (+)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="deformitasKiri" class="form-label">Deformitas Kiri: </label>
                            <select name="deformitasKiri" id="deformitasKiri" class="form-control" required>
                                <option value="-">Tidak Ada (-)</option>
                                <option value="+">Ada (+)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit Diagnosa</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
