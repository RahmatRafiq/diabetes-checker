<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MedicalRecord;
use App\Models\Patient;

class MedicalRecordSeeder extends Seeder
{
    public function run()
    {
        $patients = Patient::all(); // Mendapatkan semua pasien yang telah di-seed

        foreach ($patients as $patient) {
            // Randomize input for medical record fields
            $jariJari1 = rand(0, 1) ? '+' : '-';
            $jariJari3 = rand(0, 1) ? '+' : '-';
            $jariJari5 = rand(0, 1) ? '+' : '-';
            $dorsalPedis = rand(0, 1) ? '+' : '-';
            $plantar = rand(0, 1) ? '+' : '-';
            $deformitasKanan = rand(0, 1) ? '+' : '-';
            $deformitasKiri = rand(0, 1) ? '+' : '-';

            // Perhitungan skor seperti yang ada di controller
            $angiopatiScore = ($jariJari1 === '-' || $jariJari3 === '-' || $jariJari5 === '-') ? 1 : 0;
            $neuropatiScore = ($dorsalPedis === '-' || $plantar === '-') ? 1 : 0;
            $deformitasScore = ($deformitasKanan === '+' || $deformitasKiri === '+') ? 2 : 0;

            $totalScore = $angiopatiScore + $neuropatiScore + $deformitasScore;

            // Menentukan kategori risiko dan hasil berdasarkan skor
            $kategori = 0;
            $hasil = "Tidak Berisiko";
            if ($totalScore === 1) {
                $kategori = 1;
                $hasil = "Risiko Rendah";
            } elseif ($totalScore <= 3) {
                $kategori = 2;
                $hasil = "Risiko Sedang";
            } else {
                $kategori = 3;
                $hasil = "Risiko Tinggi";
            }

            // Membuat data rekam medis
            MedicalRecord::create([
                'patient_id' => $patient->id,
                'angiopati' => "$jariJari1, $jariJari3, $jariJari5",
                'neuropati' => "$dorsalPedis, $plantar",
                'deformitas' => "$deformitasKanan, $deformitasKiri",
                'kategori_risiko' => $kategori,
                'hasil' => $hasil,
            ]);
        }
    }
}
