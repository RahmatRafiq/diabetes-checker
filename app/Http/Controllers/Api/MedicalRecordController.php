<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Patient;
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
        // Validasi data
        $request->validate([
            'jariJari1' => 'required|string',
            'jariJari3' => 'required|string',
            'jariJari5' => 'required|string',
            'dorsalPedis' => 'required|string',
            'plantar' => 'required|string',
            'deformitasKanan' => 'required|string',
            'deformitasKiri' => 'required|string',
            'punggung_kaki' => 'nullable|string',
            'telapak_kaki' => 'nullable|string',
        ]);

        // Ambil data pasien
        $patient = Patient::where('user_id', auth()->user()->id)->firstOrFail();

        // Perhitungan skor risiko
        $angiopatiScore = ($request->dorsalPedis === '-' || $request->plantar === '-') ? 1 : 0;
        $neuropatiScore = ($request->jariJari1 === '-' || $request->jariJari3 === '-' || $request->jariJari5 === '-') ? 1 : 0;
        $deformitasScore = ($request->deformitasKanan === '+' || $request->deformitasKiri === '+') ? 2 : 0;

        $totalScore = $angiopatiScore + $neuropatiScore + $deformitasScore;

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

        // Simpan rekam medis dengan data yang tepat
        $medicalRecord = MedicalRecord::create([
            'patient_id' => $patient->id,
            'angiopati' => json_encode([
                'dorsal' => ($request->dorsalPedis === '-' ? 'Tidak' : 'Ya'),
                'plantar' => ($request->plantar === '-' ? 'Tidak' : 'Ya'),
            ]),
            'neuropati' => json_encode([
                'jariJari1' => ($request->jariJari1 === '-' ? 'Tidak Merasakan' : 'Merasakan'),
                'jariJari3' => ($request->jariJari3 === '-' ? 'Tidak Merasakan' : 'Merasakan'),
                'jariJari5' => ($request->jariJari5 === '-' ? 'Tidak Merasakan' : 'Merasakan'),
            ]),
            'deformitas' => json_encode([
                'kiri' => ($request->deformitasKiri === '+' ? 'Ada deformitas' : 'Tidak ada deformitas'),
                'kanan' => ($request->deformitasKanan === '+' ? 'Ada deformitas' : 'Tidak ada deformitas'),
            ]),
            'kategori_risiko' => $kategori,
            'hasil' => $hasil,
        ]);

        // Simpan gambar jika ada
        if ($request->punggung_kaki) {
            $this->storeBase64File($medicalRecord, $request->punggung_kaki, 'punggung-kaki', 'punggung_kaki');
        }
        if ($request->telapak_kaki) {
            $this->storeBase64File($medicalRecord, $request->telapak_kaki, 'telapak-kaki', 'telapak_kaki');
        }

        // Mengembalikan response
        return response()->json([
            'message' => 'Medical record created successfully',
            'kategori' => $kategori,
            'hasil' => $hasil,
        ]);
    }

    private function storeBase64File($medical_record, $base64Image, $folder, $fileName)
    {
        // Ekstrak base64 string (hapus bagian data:image/...;base64,)
        list($type, $data) = explode(';', $base64Image);
        list(, $data) = explode(',', $data);
        $data = base64_decode($data);

        // Buat file dari data Base64 (tanpa membuat file fisik)
        $tempFile = tmpfile(); // Buka stream file sementara
        $metaData = stream_get_meta_data($tempFile);
        $tmpFilePath = $metaData['uri']; // Dapatkan path file sementara

        // Tulis konten Base64 ke stream file sementara
        fwrite($tempFile, $data);

        // Unggah langsung ke Media Library
        $medical_record->addMedia($tmpFilePath)
            ->usingFileName($fileName . '_' . time() . '.png') // Ekstensi bisa disesuaikan
            ->withCustomProperties(['patient_id' => $medical_record->id])
            ->toMediaCollection($folder);

        // Tutup stream file sementara, ini akan secara otomatis menghapus file sementara dari disk
        fclose($tempFile);
    }

    private function isProfileComplete($patient)
    {
        return $patient && $patient->dob && $patient->gender && $patient->contact && $patient->address;
    }
}
