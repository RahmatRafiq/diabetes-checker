<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Rekam Medis Pasien</title>

    <!-- Inline CSS untuk memastikan style diterapkan ke PDF -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        .card {
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
            padding: 20px;
        }

        .card-header {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-radius: 5px 5px 0 0;
        }

        .card-body {
            padding: 20px;
        }

        .row {
            display: flex;
            justify-content: space-between;
        }

        .col-md-6 {
            width: 48%;
        }

        h3, h4 {
            text-align: center;
        }

        p {
            margin: 5px 0;
        }

        .text-end {
            text-align: right;
        }

        .divider {
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }

        img {
            max-height: 150px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Detail Rekam Medis Pasien</h3>
            </div>
            <div class="card-body">
                <!-- Section: Informasi Pasien -->
                <h4>Informasi Pasien</h4>
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

                <hr class="divider">

                <!-- Section: Informasi Medis -->
                <h4>Informasi Medis</h4>
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

                <hr class="divider">

                <!-- Section: Hasil Diagnosa -->
                <h4>Hasil Diagnosa</h4>
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

                <hr class="divider">

                <!-- Section: Foto Kaki Pasien -->
                <h4>Foto Kaki Pasien</h4>
                <div class="row">
                    <div class="col-md-6 text-center">
                        @if($punggungKakiKiri)
                            <p><strong>Punggung Kaki Kiri:</strong></p>
                            <img src="{{ $punggungKakiKiri }}" alt="Punggung Kaki Kiri">
                        @else
                            <p>Foto Punggung Kaki Kiri tidak tersedia.</p>
                        @endif
                    </div>
                    <div class="col-md-6 text-center">
                        @if($punggungKakiKanan)
                            <p><strong>Punggung Kaki Kanan:</strong></p>
                            <img src="{{ $punggungKakiKanan }}" alt="Punggung Kaki Kanan">
                        @else
                            <p>Foto Punggung Kaki Kanan tidak tersedia.</p>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 text-center">
                        @if($telapakKakiKiri)
                            <p><strong>Telapak Kaki Kiri:</strong></p>
                            <img src="{{ $telapakKakiKiri }}" alt="Telapak Kaki Kiri">
                        @else
                            <p>Foto Telapak Kaki Kiri tidak tersedia.</p>
                        @endif
                    </div>
                    <div class="col-md-6 text-center">
                        @if($telapakKakiKanan)
                            <p><strong>Telapak Kaki Kanan:</strong></p>
                            <img src="{{ $telapakKakiKanan }}" alt="Telapak Kaki Kanan">
                        @else
                            <p>Foto Telapak Kaki Kanan tidak tersedia.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
