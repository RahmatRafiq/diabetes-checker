@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Form DFU -->
    <form action="{{ route('medical-record.store') }}" method="POST">
        @csrf
        <!-- Angiopati Card -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title">Angiopati</h5>
                <p class="card-subtitle">Pemeriksaan pembuluh darah perifer untuk mendeteksi adanya gangguan aliran
                    darah.</p>
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
                <p class="card-subtitle">Pemeriksaan saraf perifer untuk mendeteksi adanya kerusakan pada saraf kaki.
                </p>
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
                <p class="card-subtitle">Pemeriksaan deformitas kaki untuk mendeteksi adanya perubahan bentuk yang tidak
                    normal.</p>
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

<!-- SweetAlert untuk menampilkan hasil diagnosa -->
@endsection

@push('javascript')
<script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
@if(session('hasil'))
<script>
    // Tentukan ikon berdasarkan kategori risiko
    let customIcon;
    let kategoriRisiko = {{ session('kategori') }};

    if (kategoriRisiko === 0) {
        customIcon = '😌'; // Wajah santai untuk "Tidak Berisiko"
    } else if (kategoriRisiko === 1) {
        customIcon = '🙂'; // Wajah tenang untuk "Risiko Rendah"
    } else if (kategoriRisiko === 2) {
        customIcon = '😟'; // Wajah khawatir untuk "Risiko Sedang"
    } else {
        customIcon = '😰'; // Wajah cemas untuk "Risiko Tinggi"
    }

    Swal.fire({
        title: 'Hasil Diagnosa',
        html: `Nama Pasien: <strong>{{ session('nama_pasien') }}</strong><br>
               Hasil: <strong>{{ session('hasil') }}</strong><br>
               Kategori Risiko: <strong>{{ session('kategori') }}</strong><br>
               <span style="font-size: 50px;">${customIcon}</span>`, // Tampilkan ikon wajah besar
        confirmButtonText: 'OK'
    });
</script>
@endif
@endpush
