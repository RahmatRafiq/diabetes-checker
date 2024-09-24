<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    // Menampilkan daftar pasien
    // Menampilkan daftar rekam medis pasien
    public function index()
    {
        $medicalRecords = MedicalRecord::with('patient')->get();

        return view('app.medical-record.index', compact('medicalRecords'));
    }

    // Menampilkan detail rekam medis
    public function show($id)
    {
        $record = MedicalRecord::with('patient')->findOrFail($id);

        return view('app.medical-record.show', compact('record'));
    }

    // Menampilkan form untuk input diagnosa berdasarkan patient_id
    public function create(Request $request)
    {
        // Ambil pasien berdasarkan ID yang dikirim dari tombol cek pasien
        $patient = Patient::findOrFail($request->patient_id);

        return view('app.medical-record.create', compact('patient'));
    }

    // Proses input diagnosa dan simpan hasilnya
    public function store(Request $request)
    {
        $request->validate([
            'jariJari1' => 'required',
            'jariJari3' => 'required',
            'jariJari5' => 'required',
            'dorsalPedis' => 'required',
            'plantar' => 'required',
            'deformitasKanan' => 'required',
            'deformitasKiri' => 'required',
        ]);

        $patient = Patient::findOrFail($request->patient_id);

        // Menghitung skor angiopati, neuropati, dan deformitas
        $angiopatiScore = ($request->jariJari1 === '-' || $request->jariJari3 === '-' || $request->jariJari5 === '-') ? 1 : 0;
        $neuropatiScore = ($request->dorsalPedis === '-' || $request->plantar === '-') ? 1 : 0;
        $deformitasScore = ($request->deformitasKanan === '+' || $request->deformitasKiri === '+') ? 2 : 0;

        $totalScore = $angiopatiScore + $neuropatiScore + $deformitasScore;

        // Menentukan kategori risiko
        if ($totalScore === 0) {
            $kategori = 0;
            $hasil = "Tidak Berisiko";
        } elseif ($totalScore === 1) {
            $kategori = 1;
            $hasil = "Risiko Rendah";
        } elseif ($totalScore <= 3) {
            $kategori = 2;
            $hasil = "Risiko Sedang";
        } else {
            $kategori = 3;
            $hasil = "Risiko Tinggi";
        }

        // Simpan rekam medis ke database
        MedicalRecord::create([
            'patient_id' => $patient->id,
            'angiopati' => $request->jariJari1 . ', ' . $request->jariJari3 . ', ' . $request->jariJari5,
            'neuropati' => $request->dorsalPedis . ', ' . $request->plantar,
            'deformitas' => $request->deformitasKanan . ', ' . $request->deformitasKiri,
            'kategori_risiko' => $kategori,
            'hasil' => $hasil,
        ]);

        // Kembali ke halaman hasil diagnosa
        return redirect()->route('medical-record.result', ['kategori' => $kategori, 'hasil' => $hasil]);
    }

    // Menampilkan hasil diagnosa
    public function result($kategori, $hasil)
    {
        return view('app.medical-record.result', compact('kategori', 'hasil'));
    }
}
