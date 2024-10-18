<?php

namespace App\Http\Controllers;

use App\Helpers\DataTable;
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
    public function json(Request $request)
    {
        $search = $request->search['value'];
        $query = MedicalRecord::query()
            ->join('patients', 'medical_records.patient_id', '=', 'patients.id') // Bergabung dengan tabel patients
            ->join('users', 'patients.user_id', '=', 'users.id') // Bergabung dengan tabel users
            ->select('medical_records.*', 'users.name as patient_name'); // Memilih kolom yang diperlukan

        // Mengatur pencarian
        if ($request->filled('search.value')) {
            $query->where('users.name', 'like', "%{$search}%") // Pencarian berdasarkan nama pengguna
                ->orWhere('angiopati', 'like', "%{$search}%")
                ->orWhere('neuropati', 'like', "%{$search}%")
                ->orWhere('deformitas', 'like', "%{$search}%")
                ->orWhere('kategori_risiko', 'like', "%{$search}%")
                ->orWhere('hasil', 'like', "%{$search}%");
        }

        // Kolom yang akan digunakan untuk pengurutan
        $columns = [
            'id',
            'patient_name', // Menggunakan alias dari join
            'angiopati',
            'neuropati',
            'deformitas',
            'kategori_risiko',
            'hasil',
        ];

        // Mengatur pengurutan
        if ($request->filled('order')) {
            $query->orderBy($columns[$request->order[0]['column']], $request->order[0]['dir']);
        }

        // Mengambil data untuk DataTables
        $data = DataTable::paginate($query, $request);

        return response()->json($data);
    }

    public function show($id)
    {
        $record = MedicalRecord::with('patient')->findOrFail($id);

        $bmi = null;
        $bmiCategory = 'Data tidak tersedia'; // Default untuk kategori BMI
        if ($record->patient && $record->patient->height && $record->patient->weight) {
            // Menghitung BMI
            $heightInMeters = $record->patient->height / 100; // Konversi dari cm ke meter
            $bmi = $record->patient->weight / ($heightInMeters * $heightInMeters); // Rumus BMI
            $bmi = round($bmi, 2); // Membulatkan hasil BMI ke 2 desimal

            // Logika untuk menentukan kategori BMI
            if ($bmi < 18.5) {
                $bmiCategory = 'Underweight (Kurus)';
            } elseif ($bmi >= 18.5 && $bmi < 25) {
                $bmiCategory = 'Normal weight (Berat Badan Normal)';
            } elseif ($bmi >= 25 && $bmi < 30) {
                $bmiCategory = 'Overweight (Berat Badan Berlebih)';
            } else {
                $bmiCategory = 'Obesity (Obesitas)';
            }
        }

        $punggungKaki = $record->getFirstMediaUrl('punggung-kaki', 'punggung_kaki') ?: null;
        $telapakKaki = $record->getFirstMediaUrl('telapak-kaki', 'telapak_kaki') ?: null;
        return view('app.medical-record.show', compact('record', 'punggungKaki', 'telapakKaki', 'bmi', 'bmiCategory'));
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
            'punggung_kaki' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'telapak_kaki' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'punggung_kaki_kiri' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'telapak_kaki_kiri' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'punggung_kaki_kanan' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'telapak_kaki_kanan' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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

        if ($request->hasFile('punggung_kaki')) {
            $this->storeFile($medicalRecord, $request->file('punggung_kaki'), 'punggung-kaki', 'punggung_kaki');
        }
        if ($request->hasFile('telapak_kaki')) {
            $this->storeFile($medicalRecord, $request->file('telapak_kaki'), 'telapak-kaki', 'telapak_kaki');
        }
        // if ($request->hasFile('punggung_kaki_kiri')) {
        //     $this->storeFile($medicalRecord, $request->file('punggung_kaki_kiri'), 'punggung-kaki-kiri', 'punggung_kaki_kiri');
        // }
        // if ($request->hasFile('telapak_kaki_kiri')) {
        //     $this->storeFile($medicalRecord, $request->file('telapak_kaki_kiri'), 'telapak-kaki-kiri', 'telapak_kaki_kiri');
        // }
        // if ($request->hasFile('punggung_kaki_kanan')) {
        //     $this->storeFile($medicalRecord, $request->file('punggung_kaki_kanan'), 'punggung-kaki-kanan', 'punggung_kaki_kanan');
        // }
        // if ($request->hasFile('telapak_kaki_kanan')) {
        //     $this->storeFile($medicalRecord, $request->file('telapak_kaki_kanan'), 'telapak-kaki-kanan', 'telapak_kaki_kanan');
        // }

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

        // Mengambil tinggi dan berat badan
        $height = $record->patient->height; // Tinggi dalam cm
        $weight = $record->patient->weight; // Berat dalam kg

        // Menghitung BMI
        $bmi = null;
        $bmiCategory = 'Data tidak tersedia'; // Default untuk kategori BMI
        if ($height && $weight) {
            // Menghitung BMI
            $heightInMeters = $height / 100; // Konversi dari cm ke meter
            $bmi = $weight / ($heightInMeters * $heightInMeters); // Rumus BMI
            $bmi = round($bmi, 2); // Membulatkan hasil BMI ke 2 desimal

            // Logika untuk menentukan kategori BMI
            if ($bmi < 18.5) {
                $bmiCategory = 'Underweight (Kurus)';
            } elseif ($bmi >= 18.5 && $bmi < 25) {
                $bmiCategory = 'Normal weight (Berat Badan Normal)';
            } elseif ($bmi >= 25 && $bmi < 30) {
                $bmiCategory = 'Overweight (Berat Badan Berlebih)';
            } else {
                $bmiCategory = 'Obesity (Obesitas)';
            }
        }

        // Menyimpan URL gambar media
        $punggungKakiKiri = '';
        $telapakKakiKiri = '';
        $punggungKakiKanan = '';
        $telapakKakiKanan = '';

        if ($record->getFirstMediaUrl('punggung-kaki', 'punggung_kaki')) {
            $path = $record->getFirstMediaPath('punggung-kaki', 'punggung_kaki');
            $punggungKaki = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($path));
        }

        if ($record->getFirstMediaUrl('telapak-kaki', 'telapak_kaki')) {
            $path = $record->getFirstMediaPath('telapak-kaki', 'telapak_kaki');
            $telapakKaki = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($path));
        }

        // Mengirim data ke view PDF
        $pdf = PDF::loadView('app.medical-record.export', compact('record', 'height', 'weight', 'bmi', 'bmiCategory', 'punggungKaki', 'telapakKaki'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('rekam_medis_pasien.pdf');
    }

}
