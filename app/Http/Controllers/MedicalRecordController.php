<?php

namespace App\Http\Controllers;

use App\Helpers\MediaLibrary;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
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
    
        $punggungKakiKiri = $record->getFirstMediaUrl('punggung-kaki-kiri', 'punggung_kaki_kiri') ?: null;
        $telapakKakiKiri = $record->getFirstMediaUrl('telapak-kaki-kiri', 'telapak_kaki_kiri') ?: null;
        $punggungKakiKanan = $record->getFirstMediaUrl('punggung-kaki-kanan', 'punggung_kaki_kanan') ?: null;
        $telapakKakiKanan = $record->getFirstMediaUrl('telapak-kaki-kanan', 'telapak_kaki_kanan') ?: null;
        return view('app.medical-record.show', compact('record', 'punggungKakiKiri', 'telapakKakiKiri', 'punggungKakiKanan', 'telapakKakiKanan'));
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
            'punggung_kaki_kiri' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'telapak_kaki_kiri' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'punggung_kaki_kanan' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'telapak_kaki_kanan' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $patient = Patient::where('user_id', auth()->user()->id)->firstOrFail();
    
        $angiopatiScore = ($request->jariJari1 === '-' || $request->jariJari3 === '-' || $request->jariJari5 === '-') ? 1 : 0;
        $neuropatiScore = ($request->dorsalPedis === '-' || $request->plantar === '-') ? 1 : 0;
        $deformitasScore = ($request->deformitasKanan === '+' || $request->deformitasKiri === '+') ? 2 : 0;
    
        $totalScore = $angiopatiScore + $neuropatiScore + $deformitasScore;
    
        $kategori = 0;
        $hasil = "Tidak Berisiko";
        if ($totalScore === 0) {
            $kategori = 0;
            $hasil = "Tidak Berisiko";
        } elseif ($totalScore === 1) {
            $kategori = 1;
            $hasil = "Risiko Rendah";
        } elseif ($totalScore === 2) {
            $kategori = 2;
            $hasil = "Risiko Sedang";
        } else {
            $kategori = 3;
            $hasil = "Risiko Tinggi";
        }
    
        $medicalRecord = MedicalRecord::create([
            'patient_id' => $patient->id,
            'angiopati' => $request->jariJari1 . ', ' . $request->jariJari3 . ', ' . $request->jariJari5,
            'neuropati' => $request->dorsalPedis . ', ' . $request->plantar,
            'deformitas' => $request->deformitasKanan . ', ' . $request->deformitasKiri,
            'kategori_risiko' => $kategori,
            'hasil' => $hasil,
        ]);
    
        if ($request->hasFile('punggung_kaki_kiri')) {
            $this->storeFile($medicalRecord, $request->file('punggung_kaki_kiri'), 'punggung-kaki-kiri', 'punggung_kaki_kiri');
        }
        if ($request->hasFile('telapak_kaki_kiri')) {
            $this->storeFile($medicalRecord, $request->file('telapak_kaki_kiri'), 'telapak-kaki-kiri', 'telapak_kaki_kiri');
        }
        if ($request->hasFile('punggung_kaki_kanan')) {
            $this->storeFile($medicalRecord, $request->file('punggung_kaki_kanan'), 'punggung-kaki-kanan', 'punggung_kaki_kanan');
        }
        if ($request->hasFile('telapak_kaki_kanan')) {
            $this->storeFile($medicalRecord, $request->file('telapak_kaki_kanan'), 'telapak-kaki-kanan', 'telapak_kaki_kanan');
        }
    
        return back()->with([
            'nama_pasien' => $patient->name,
            'kategori' => $kategori,
            'hasil' => $hasil,
        ]);
    }

    private function storeFile($medical_record, $file, $folder, $fileName)
    {
        $medical_record->addMedia($file)
            ->usingFileName($fileName . '_' . time() . '.' . $file->getClientOriginalExtension())
            ->withCustomProperties(['patient_id' => $medical_record->id])
            ->toMediaCollection($folder);
    }

    public function exportPDF($id)
    {
        $record = MedicalRecord::with('patient')->findOrFail($id);
    
        $punggungKakiKiri = '';
        $telapakKakiKiri = '';
        $punggungKakiKanan = '';
        $telapakKakiKanan = '';
    
        if ($record->getFirstMediaUrl('punggung-kaki-kiri', 'punggung_kaki_kiri')) {
            $path = $record->getFirstMediaPath('punggung-kaki-kiri', 'punggung_kaki_kiri');
            $punggungKakiKiri = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($path));
        }
    
        if ($record->getFirstMediaUrl('punggung-kaki-kanan', 'punggung_kaki_kanan')) {
            $path = $record->getFirstMediaPath('punggung-kaki-kanan', 'punggung_kaki_kanan');
            $punggungKakiKanan = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($path));
        }
    
        if ($record->getFirstMediaUrl('telapak-kaki-kiri', 'telapak_kaki_kiri')) {
            $path = $record->getFirstMediaPath('telapak-kaki-kiri', 'telapak_kaki_kiri');
            $telapakKakiKiri = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($path));
        }
    
        if ($record->getFirstMediaUrl('telapak-kaki-kanan', 'telapak_kaki_kanan')) {
            $path = $record->getFirstMediaPath('telapak-kaki-kanan', 'telapak_kaki_kanan');
            $telapakKakiKanan = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($path));
        }
    
        $pdf = PDF::loadView('app.medical-record.export', compact('record', 'punggungKakiKiri', 'telapakKakiKiri', 'punggungKakiKanan', 'telapakKakiKanan'))
            ->setPaper('a4', 'portrait');
    
        return $pdf->download('rekam_medis_pasien.pdf');
    }
}
