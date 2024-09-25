<?php

namespace App\Http\Controllers;

use App\Helpers\MediaLibrary;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
// Import helper MediaLibrary

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
    
        // Simpan rekam medis
        $medicalRecord = MedicalRecord::create([
            'patient_id' => $patient->id,
            'angiopati' => $request->jariJari1 . ', ' . $request->jariJari3 . ', ' . $request->jariJari5,
            'neuropati' => $request->dorsalPedis . ', ' . $request->plantar,
            'deformitas' => $request->deformitasKanan . ', ' . $request->deformitasKiri,
            'kategori_risiko' => $kategori,
            'hasil' => $hasil,
        ]);
    
        // Gunakan helper MediaLibrary untuk menyimpan media ke disk 'patient'
        if ($request->hasFile('punggung_kaki_kiri')) {
            $patient->addMedia($request->file('punggung_kaki_kiri'))
                ->usingFileName('punggung_kaki_kiri_' . time() . '.' . $request->file('punggung_kaki_kiri')->getClientOriginalExtension())
                ->toMediaCollection('kaki-kiri', 'patient'); // Specify the 'patient' disk here
        }
        if ($request->hasFile('telapak_kaki_kiri')) {
            $patient->addMedia($request->file('telapak_kaki_kiri'))
                ->usingFileName('telapak_kaki_kiri_' . time() . '.' . $request->file('telapak_kaki_kiri')->getClientOriginalExtension())
                ->toMediaCollection('kaki-kiri', 'patient'); // Specify the 'patient' disk here
        }
        if ($request->hasFile('punggung_kaki_kanan')) {
            $patient->addMedia($request->file('punggung_kaki_kanan'))
                ->usingFileName('punggung_kaki_kanan_' . time() . '.' . $request->file('punggung_kaki_kanan')->getClientOriginalExtension())
                ->toMediaCollection('kaki-kanan', 'patient'); // Specify the 'patient' disk here
        }
        if ($request->hasFile('telapak_kaki_kanan')) {
            $patient->addMedia($request->file('telapak_kaki_kanan'))
                ->usingFileName('telapak_kaki_kanan_' . time() . '.' . $request->file('telapak_kaki_kanan')->getClientOriginalExtension())
                ->toMediaCollection('kaki-kanan', 'patient'); // Specify the 'patient' disk here
        }
    
        return back()->with([
            'nama_pasien' => $patient->name,
            'kategori' => $kategori,
            'hasil' => $hasil,
        ]);
    }
    

    private function storeFile($patient, $file, $folder, $fileName)
    {
        // Metode ini bisa tetap ada untuk menangani validasi atau tugas tambahan lainnya
        $patient->addMedia($file)
            ->usingFileName($fileName . '_' . time() . '.' . $file->getClientOriginalExtension())
            ->withCustomProperties(['patient_id' => $patient->id])
            ->toMediaCollection($folder);
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
