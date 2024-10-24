<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Rekam Medis Pasien</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            padding: 20px;
        }

        .header {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 10px 0;
            margin-bottom: 20px;
        }

        .section-title {
            text-align: center;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }

        .column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 10px;
        }

        .column p {
            margin: 5px 0;
        }

        hr {
            border: 1px solid #9c9c9c;
            margin: 20px 0;
        }

        .text-center {
            text-align: center;
        }

        .img-fluid {
            max-width: 100%;
            height: auto;
            max-height: 150px;
        }

        .footer {
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h3>Detail Rekam Medis Pasien</h3>
        </div>

        <!-- Section: Informasi Pasien -->
        <h4 class="section-title">Informasi Pasien</h4>
        <div class="row">
            <div class="column">
                <p><strong>Nama Pasien:</strong> {{ $record->patient->name }}</p>
                <p><strong>Tanggal Lahir:</strong> {{ \Carbon\Carbon::parse($record->patient->dob)->format('d M Y') }}
                </p>
                <p><strong>Jenis Kelamin:</strong> {{ $record->patient->gender }}</p>
                <p><strong>Kontak:</strong> {{ $record->patient->contact }}</p>
                <p><strong>Alamat:</strong> {{ $record->patient->address }}</p>
            </div>
            <div class="column">
                <p><strong>Tingkat Pendidikan:</strong> {{ $record->patient->education_level ?? 'Data tidak tersedia' }}
                </p>
                <p><strong>Pekerjaan:</strong> {{ $record->patient->occupation ?? 'Data tidak tersedia' }}</p>
                <p><strong>Berat Badan:</strong> {{ $record->patient->weight ? $record->patient->weight . ' kg' : 'Data
                    tidak tersedia' }}</p>
                <p><strong>Tinggi Badan:</strong> {{ $record->patient->height ? $record->patient->height . ' cm' : 'Data
                    tidak tersedia' }}</p>
                <p><strong>Lama Menderita Diabetes:</strong> {{ $record->patient->years_with_diabetes ?
                    $record->patient->years_with_diabetes . ' tahun' : 'Data tidak tersedia' }}</p>
            </div>
        </div>

        <hr>

        <!-- Section: Informasi Medis -->
        <h4 class="section-title">Informasi Medis</h4>
        <div class="row">
            <div class="column">
                <p><strong>Terapi DM:</strong> {{ $record->patient->dm_therapy ?? 'Data tidak tersedia' }}</p>
                <p><strong>BMI :</strong> {{ $bmi ? $bmi . ' kg/m² (' . $bmiCategory . ')' : 'Data tidak tersedia' }}
                </p>
                <p><strong>Riwayat Luka:</strong> {{ $record->patient->wound_history ?? 'Data tidak tersedia' }}</p>
                @if($record->patient->wound_history === 'Ya')
                <p><strong>Deskripsi Riwayat Luka:</strong> {{ $record->patient->wound_history_description ?? 'Data
                    tidak tersedia' }}</p>
                @endif
            </div>
            <div class="column">
                <p><strong>Nilai GDS:</strong> {{ $record->patient->gds ? $record->patient->gds . ' mg/dL' : 'Data tidak
                    tersedia' }}</p>
                <p><strong>Nilai HbA1c:</strong> {{ $record->patient->hba1c ? $record->patient->hba1c . ' %' : 'Data
                    tidak tersedia' }}</p>
                <p><strong>Jenis Diet:</strong> {{ $record->patient->diet_type ?? 'Data tidak tersedia' }}</p>
            </div>
        </div>

        <hr>

        <!-- Section: Hasil Diagnosa -->
        <h4 class="section-title">Hasil Diagnosa</h4>
        <div class="row">
            <div class="column">
                <p><strong>Tanggal Diagnosa:</strong> {{ $record->created_at->format('d M Y') }}</p>
                <p><strong>Kategori Risiko:</strong> {{ $record->kategori_risiko }}</p>
            </div>
            <div class="column">
                <p><strong>Hasil Diagnosa:</strong> {{ $record->hasil }}</p>
                <p><strong>Angiopati:</strong> {{ $record->angiopati }}</p>
                <p><strong>Neuropati:</strong> {{ $record->neuropati }}</p>
                <p><strong>Deformitas:</strong> {{ $record->deformitas }}</p>
            </div>
        </div>

        <hr>

        <!-- Section: Foto Kaki Pasien -->
        <h4 class="section-title">Foto Kaki Pasien</h4>
        <div class="row">
            <div class="column text-center">
                @if($punggungKaki)
                <p><strong>Punggung Kaki:</strong></p>
                <img src="{{ $punggungKaki }}" alt="Punggung Kaki" class="img-fluid">
                @else
                <p>Foto Punggung Kaki tidak tersedia.</p>
                @endif
            </div>
            <div class="column text-center">
                @if($telapakKaki)
                <p><strong>Telapak Kaki:</strong></p>
                <img src="{{ $telapakKaki }}" alt="Telapak Kaki" class="img-fluid">
                @else
                <p>Foto Telapak Kaki tidak tersedia.</p>
                @endif
            </div>
        </div>
    </div>
</body>

</html>