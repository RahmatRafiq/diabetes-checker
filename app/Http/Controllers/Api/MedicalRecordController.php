<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function apiTest()
    {
        $medicalRecords = MedicalRecord::all();
        return response()->json($medicalRecords);
    }
    public function index()
    {
        $patient = auth()->user()->patient;

        // Cek apakah profil pasien lengkap
        if (!$this->isProfileComplete($patient)) {
            return response()->json(['message' => 'Please complete your profile before accessing medical records.'], 400);
        }

        $medicalRecords = MedicalRecord::where('patient_id', $patient->id)->get();
        return response()->json($medicalRecords);
    }

    public function show($id)
    {
        $patient = auth()->user()->patient;

        // Cek apakah profil pasien lengkap
        if (!$this->isProfileComplete($patient)) {
            return response()->json(['message' => 'Please complete your profile before accessing medical records.'], 400);
        }

        $medicalRecord = MedicalRecord::findOrFail($id);
        return response()->json($medicalRecord);
    }

    public function store(Request $request)
    {
        $patient = auth()->user()->patient;

        // Cek apakah profil pasien lengkap
        if (!$this->isProfileComplete($patient)) {
            return response()->json(['message' => 'Please complete your profile before creating a medical record.'], 400);
        }

        // Validasi input rekam medis
        $request->validate([
            'jariJari1' => 'required',
            'jariJari3' => 'required',
            'jariJari5' => 'required',
            'dorsalPedis' => 'required',
            'plantar' => 'required',
            'deformitasKanan' => 'required',
            'deformitasKiri' => 'required',
        ]);

        // Hitung skor risiko berdasarkan input
        $angiopatiScore = ($request->jariJari1 === '-' || $request->jariJari3 === '-' || $request->jariJari5 === '-') ? 1 : 0;
        $neuropatiScore = ($request->dorsalPedis === '-' || $request->plantar === '-') ? 1 : 0;
        $deformitasScore = ($request->deformitasKanan === '+' || $request->deformitasKiri === '+') ? 2 : 0;

        $totalScore = $angiopatiScore + $neuropatiScore + $deformitasScore;

        // Kategorisasi risiko
        if ($totalScore === 0) {
            $kategori = 'Tidak Berisiko';
        } elseif ($totalScore === 1) {
            $kategori = 'Risiko Rendah';
        } elseif ($totalScore === 2) {
            $kategori = 'Risiko Sedang';
        } else {
            $kategori = 'Risiko Tinggi';
        }

        // Simpan rekam medis
        $medicalRecord = MedicalRecord::create([
            'patient_id' => $patient->id,
            'angiopati' => $request->jariJari1 . ', ' . $request->jariJari3 . ', ' . $request->jariJari5,
            'neuropati' => $request->dorsalPedis . ', ' . $request->plantar,
            'deformitas' => $request->deformitasKanan . ', ' . $request->deformitasKiri,
            'kategori_risiko' => $kategori,
        ]);

        return response()->json(['message' => 'Medical record created successfully', 'record' => $medicalRecord]);
    }

    /**
     * Helper untuk memeriksa apakah profil pasien lengkap
     */
    private function isProfileComplete($patient)
    {
        return $patient && $patient->dob && $patient->gender && $patient->contact && $patient->address;
    }
}
