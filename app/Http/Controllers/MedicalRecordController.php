<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index()
    {
        $medicalRecords = MedicalRecord::with('patient')->get();
        return view('app.medical-record.index', compact('medicalRecords'));
    }

    public function show($id)
    {
        $record = MedicalRecord::with('patient')->findOrFail($id);
        return view('app.medical-record.show', compact('record'));
    }

    public function create()
    {
        $patient = Patient::where('user_id', auth()->user()->id)->firstOrFail();
        return view('app.medical-record.create', compact('patient'));
    }

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

        $patient = Patient::where('user_id', auth()->user()->id)->firstOrFail();

        $angiopatiScore = ($request->jariJari1 === '-' || $request->jariJari3 === '-' || $request->jariJari5 === '-') ? 1 : 0;
        $neuropatiScore = ($request->dorsalPedis === '-' || $request->plantar === '-') ? 1 : 0;
        $deformitasScore = ($request->deformitasKanan === '+' || $request->deformitasKiri === '+') ? 2 : 0;

        $totalScore = $angiopatiScore + $neuropatiScore + $deformitasScore;

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

        MedicalRecord::create([
            'patient_id' => $patient->id,
            'angiopati' => $request->jariJari1 . ', ' . $request->jariJari3 . ', ' . $request->jariJari5,
            'neuropati' => $request->dorsalPedis . ', ' . $request->plantar,
            'deformitas' => $request->deformitasKanan . ', ' . $request->deformitasKiri,
            'kategori_risiko' => $kategori,
            'hasil' => $hasil,
        ]);

        return back()->with([
            'nama_pasien' => $patient->name,
            'kategori' => $kategori,
            'hasil' => $hasil,
        ]);
    }
}
