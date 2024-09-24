<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    // Menampilkan form untuk input diagnosa berdasarkan user yang login
    public function create()
    {
        // Ambil pasien berdasarkan user yang login
        $patient = Patient::where('user_id', auth()->user()->id)->firstOrFail();

        return view('app.medical-record.create', compact('patient'));
    }

    // Proses input diagnosa dan simpan hasilnya
    public function store(Request $request)
    {
        // Validasi data form
        $request->validate([
            'jariJari1' => 'required',
            'jariJari3' => 'required',
            'jariJari5' => 'required',
            'dorsalPedis' => 'required',
            'plantar' => 'required',
            'deformitasKanan' => 'required',
            'deformitasKiri' => 'required',
        ]);

        // Ambil data pasien yang login
        $patient = Patient::where('user_id', auth()->user()->id)->firstOrFail();

        // Menghitung skor angiopati, neuropati, dan deformitas
        $angiopatiScore = ($request->jariJari1 === '-' || $request->jariJari3 === '-' || $request->jariJari5 === '-') ? 1 : 0;
        $neuropatiScore = ($request->dorsalPedis === '-' || $request->plantar === '-') ? 1 : 0;
        $deformitasScore = ($request->deformitasKanan === '+' || $request->deformitasKiri === '+') ? 2 : 0;

        $totalScore = $angiopatiScore + $neuropatiScore + $deformitasScore;

        // Menentukan kategori risiko berdasarkan total score
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

        // Kirim nama pasien, kategori, dan hasil melalui session untuk ditampilkan dengan SweetAlert
        return back()->with([
            'nama_pasien' => $patient->name,
            'kategori' => $kategori,
            'hasil' => $hasil,
        ]);
    }
}
