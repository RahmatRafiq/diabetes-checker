<?php

namespace App\Http\Controllers;

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
        return view('app.medical-record.show', compact('record'));
    }

    public function create()
    {
        $patient = Patient::where('user_id', auth()->user()->id)->firstOrFail();
        return view('app.medical-record.create', compact('patient'));
    }

    public function store(Request $request)
    {
        // Validasi data form dan file
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

        // Logika perhitungan diagnosa
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

        // Simpan rekam medis
        MedicalRecord::create([
            'patient_id' => $patient->id,
            'angiopati' => $request->jariJari1 . ', ' . $request->jariJari3 . ', ' . $request->jariJari5,
            'neuropati' => $request->dorsalPedis . ', ' . $request->plantar,
            'deformitas' => $request->deformitasKanan . ', ' . $request->deformitasKiri,
            'kategori_risiko' => $kategori,
            'hasil' => $hasil,
        ]);

        // Simpan foto di folder custom
        if ($request->hasFile('punggung_kaki_kiri')) {
            $this->storeFile($patient, $request->file('punggung_kaki_kiri'), 'kaki-kiri', 'punggung_kaki_kiri');
        }
        if ($request->hasFile('telapak_kaki_kiri')) {
            $this->storeFile($patient, $request->file('telapak_kaki_kiri'), 'kaki-kiri', 'telapak_kaki_kiri');
        }
        if ($request->hasFile('punggung_kaki_kanan')) {
            $this->storeFile($patient, $request->file('punggung_kaki_kanan'), 'kaki-kanan', 'punggung_kaki_kanan');
        }
        if ($request->hasFile('telapak_kaki_kanan')) {
            $this->storeFile($patient, $request->file('telapak_kaki_kanan'), 'kaki-kanan', 'telapak_kaki_kanan');
        }

        return back()->with([
            'nama_pasien' => $patient->name,
            'kategori' => $kategori,
            'hasil' => $hasil,
        ]);
    }

    private function storeFile($patient, $file, $folder, $fileName)
    {
        // Store the file with Spatie's Media Library
        $patient->addMedia($file)
            ->usingFileName($fileName . '_' . time() . '.' . $file->getClientOriginalExtension())
            ->withCustomProperties(['patient_id' => $patient->id])
            ->toMediaCollection($patient->getCustomMediaPath($folder));
    }
    

    public function exportPDF($id)
    {
        // Ambil data rekam medis dan pasien terkait
        $record = MedicalRecord::with('patient')->findOrFail($id);

        // Gunakan view yang khusus untuk PDF export dengan inline style
        $pdf = PDF::loadView('app.medical-record.export', compact('record'))
            ->setPaper('a4', 'portrait'); // Atur ukuran dan orientasi kertas

        // Unduh PDF dengan nama file yang sesuai
        return $pdf->download('rekam_medis_pasien.pdf');
    }
}
