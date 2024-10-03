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

        if (!$this->isProfileComplete($patient)) {
            return response()->json(['message' => 'Please complete your profile before accessing medical records.'], 400);
        }

        $medicalRecords = MedicalRecord::where('patient_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get();
        return response()->json($medicalRecords);
    }

    public function show($id)
    {
        $patient = auth()->user()->patient;

        if (!$this->isProfileComplete($patient)) {
            return response()->json(['message' => 'Please complete your profile before accessing medical records.'], 400);
        }

        $medicalRecord = MedicalRecord::findOrFail($id);
        return response()->json($medicalRecord);
    }
    public function store(Request $request)
    {
        \Log::info('Store method called');
    
        // Validasi data menggunakan JSON payload
        $data = $request->validate([
            'jariJari1' => 'required|string',
            'jariJari3' => 'required|string',
            'jariJari5' => 'required|string',
            'dorsalPedis' => 'required|string',
            'plantar' => 'required|string',
            'deformitasKanan' => 'required|string',
            'deformitasKiri' => 'required|string',
            'punggung_kaki_kiri' => 'nullable|string', // Validasi untuk base64 image
            'telapak_kaki_kiri' => 'nullable|string',  // Validasi untuk base64 image
            'punggung_kaki_kanan' => 'nullable|string',
            'telapak_kaki_kanan' => 'nullable|string',
        ]);
    
        // Ambil data pasien dari pengguna yang login
        $patient = auth()->user()->patient;
    
        // Periksa apakah profil pasien sudah lengkap
        if (!$this->isProfileComplete($patient)) {
            \Log::warning('Profile incomplete for patient ID: ' . $patient->id);
            return response()->json(['message' => 'Please complete your profile before creating a medical record.'], 400);
        }
    
        \Log::info('Profile complete for patient ID: ' . $patient->id);
    
        // Hitung skor berdasarkan input
        $angiopatiScore = ($request->jariJari1 === '-' || $request->jariJari3 === '-' || $request->jariJari5 === '-') ? 1 : 0;
        $neuropatiScore = ($request->dorsalPedis === '-' || $request->plantar === '-') ? 1 : 0;
        $deformitasScore = ($request->deformitasKanan === '+' || $request->deformitasKiri === '+') ? 2 : 0;
        
        $totalScore = $angiopatiScore + $neuropatiScore + $deformitasScore;
    
        \Log::info('Scores calculated for patient ID: ' . $patient->id . ' - Angiopati: ' . $angiopatiScore . ', Neuropati: ' . $neuropatiScore . ', Deformitas: ' . $deformitasScore);
    
        // Tentukan kategori risiko
        $kategori = 0;
        $hasil = "Tidak Berisiko";
        if ($totalScore === 1) {
            $kategori = 1;
            $hasil = "Risiko Rendah";
        } elseif ($totalScore === 2) {
            $kategori = 2;
            $hasil = "Risiko Sedang";
        } elseif ($totalScore > 2) {
            $kategori = 3;
            $hasil = "Risiko Tinggi";
        }
    
        \Log::info('Risk category determined for patient ID: ' . $patient->id . ' - Category: ' . $kategori . ', Result: ' . $hasil);
    
        // Buat rekam medis baru
        $medicalRecord = MedicalRecord::create([
            'patient_id' => $patient->id,
            'angiopati' => $request->jariJari1 . ', ' . $request->jariJari3 . ', ' . $request->jariJari5,
            'neuropati' => $request->dorsalPedis . ', ' . $request->plantar,
            'deformitas' => $request->deformitasKanan . ', ' . $request->deformitasKiri,
            'kategori_risiko' => $kategori,
            'hasil' => $hasil,
        ]);
    
        \Log::info('Medical record created for patient ID: ' . $patient->id . ' - Record ID: ' . $medicalRecord->id);
    
        return response()->json(['message' => 'Medical record created successfully', 'record' => $medicalRecord, 'hasil' => $hasil]);
    }
    
    // public function store(Request $request)
    // { 
    //     \Log::info('Store method called');

    //     $patient = auth()->user()->patient;

    //     if (!$this->isProfileComplete($patient)) {
    //         \Log::warning('Profile incomplete for patient ID: ' . $patient->id);
    //         return response()->json(['message' => 'Please complete your profile before creating a medical record.'], 400);
    //     }

    //     \Log::info('Profile complete for patient ID: ' . $patient->id);

    //     $request->validate([
    //         'jariJari1' => 'required|string',
    //         'jariJari3' => 'required|string',
    //         'jariJari5' => 'required|string',
    //         'dorsalPedis' => 'required|string',
    //         'plantar' => 'required|string',
    //         'deformitasKanan' => 'required|string',
    //         'deformitasKiri' => 'required|string',
    //         'punggung_kaki_kiri' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //         'telapak_kaki_kiri' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //         'punggung_kaki_kanan' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //         'telapak_kaki_kanan' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ]);

    //     \Log::info('Request validated for patient ID: ' . $patient->id);

    //     $angiopatiScore = ($request->jariJari1 === '-' || $request->jariJari3 === '-' || $request->jariJari5 === '-') ? 1 : 0;
    //     $neuropatiScore = ($request->dorsalPedis === '-' || $request->plantar === '-') ? 1 : 0;
    //     $deformitasScore = ($request->deformitasKanan === '+' || $request->deformitasKiri === '+') ? 2 : 0;
    
    //     $totalScore = $angiopatiScore + $neuropatiScore + $deformitasScore;

    //     \Log::info('Scores calculated for patient ID: ' . $patient->id . ' - Angiopati: ' . $angiopatiScore . ', Neuropati: ' . $neuropatiScore . ', Deformitas: ' . $deformitasScore);

    //     $kategori = 0;
    //     $hasil = "Tidak Berisiko";
    //     if ($totalScore === 0) {
    //         $kategori = 0;
    //         $hasil = "Tidak Berisiko";
    //     } elseif ($totalScore === 1) {
    //         $kategori = 1;
    //         $hasil = "Risiko Rendah";
    //     } elseif ($totalScore === 2) {
    //         $kategori = 2;
    //         $hasil = "Risiko Sedang";
    //     } else {
    //         $kategori = 3;
    //         $hasil = "Risiko Tinggi";
    //     }

    //     \Log::info('Risk category determined for patient ID: ' . $patient->id . ' - Category: ' . $kategori . ', Result: ' . $hasil);

    //     $medicalRecord = MedicalRecord::create([
    //         'patient_id' => $patient->id,
    //         'angiopati' => $request->jariJari1 . ', ' . $request->jariJari3 . ', ' . $request->jariJari5,
    //         'neuropati' => $request->dorsalPedis . ', ' . $request->plantar,
    //         'deformitas' => $request->deformitasKanan . ', ' . $request->deformitasKiri,
    //         'kategori_risiko' => $kategori,
    //         'hasil' => $hasil,
    //     ]);

    //     \Log::info('Medical record created for patient ID: ' . $patient->id . ' - Record ID: ' . $medicalRecord->id);

    //     if ($request->hasFile('punggung_kaki_kiri')) {
    //         $this->storeFile($medicalRecord, $request->file('punggung_kaki_kiri'), 'punggung-kaki-kiri', 'punggung_kaki_kiri');
    //         \Log::info('File stored for punggung_kaki_kiri for record ID: ' . $medicalRecord->id);
    //     }
    //     if ($request->hasFile('telapak_kaki_kiri')) {
    //         $this->storeFile($medicalRecord, $request->file('telapak_kaki_kiri'), 'telapak-kaki-kiri', 'telapak_kaki_kiri');
    //         \Log::info('File stored for telapak_kaki_kiri for record ID: ' . $medicalRecord->id);
    //     }
    //     if ($request->hasFile('punggung_kaki_kanan')) {
    //         $this->storeFile($medicalRecord, $request->file('punggung_kaki_kanan'), 'punggung-kaki-kanan', 'punggung_kaki_kanan');
    //         \Log::info('File stored for punggung_kaki_kanan for record ID: ' . $medicalRecord->id);
    //     }
    //     if ($request->hasFile('telapak_kaki_kanan')) {
    //         $this->storeFile($medicalRecord, $request->file('telapak_kaki_kanan'), 'telapak-kaki-kanan', 'telapak_kaki_kanan');
    //         \Log::info('File stored for telapak_kaki_kanan for record ID: ' . $medicalRecord->id);
    //     }

    //     \Log::info('Store method completed for patient ID: ' . $patient->id);

    //     return response()->json(['message' => 'Medical record created successfully', 'record' => $medicalRecord, 'hasil' => $hasil]);
    // }

    private function storeFile($medicalRecord, $file, $folder, $fileName)
    {
        $medicalRecord->addMedia($file)
            ->usingFileName($fileName . '_' . time() . '.' . $file->getClientOriginalExtension())
            ->withCustomProperties(['patient_id' => $medicalRecord->id])
            ->toMediaCollection($folder);
    }

    private function isProfileComplete($patient)
    {
        return $patient && $patient->dob && $patient->gender && $patient->contact && $patient->address;
    }
}
