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
        // Mengambil data rekam medis dengan relasi pasien
        $medicalRecords = MedicalRecord::with('patient')->get();

        // Mengirim data rekam medis ke view index
        return view('app.medical-record.index', compact('medicalRecords'));
    }

    public function json(Request $request)
    {
        $search = $request->search['value'];
        $query = MedicalRecord::query()
            ->join('patients', 'medical_records.patient_id', '=', 'patients.id')
            ->join('users', 'patients.user_id', '=', 'users.id')
            ->select('medical_records.*', 'users.name as patient_name');

        // Mengatur pencarian
        if ($request->filled('search.value')) {
            $query->where('users.name', 'like', "%{$search}%")
                ->orWhere('angiopati', 'like', "%{$search}%")
                ->orWhere('neuropati', 'like', "%{$search}%")
                ->orWhere('deformitas', 'like', "%{$search}%")
                ->orWhere('kategori_risiko', 'like', "%{$search}%")
                ->orWhere('hasil', 'like', "%{$search}%");
        }

        // Kolom yang akan digunakan untuk pengurutan
        $columns = [
            'id',
            'patient_name',
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

        // Mendapatkan data rekam medis dan mendecode JSON sebelum dikirim ke DataTables
        $data = $query->get()->map(function ($record) {
            // Decode JSON fields
            $record->angiopati = json_decode($record->angiopati, true);
            $record->neuropati = json_decode($record->neuropati, true);
            $record->deformitas = json_decode($record->deformitas, true);
            return $record;
        });

        // Mengambil data untuk DataTables dengan paginasi
        $data = DataTable::paginate($query, $request);

        return response()->json($data);
    }

    public function show($id)
    {
        // Menemukan rekam medis berdasarkan ID
        $record = MedicalRecord::with('patient')->findOrFail($id);

        // Decode JSON fields sebelum mengirim ke view
        $record->angiopati = json_decode($record->angiopati, true);
        $record->neuropati = json_decode($record->neuropati, true);
        $record->deformitas = json_decode($record->deformitas, true);

        // Menghitung BMI jika tinggi dan berat badan tersedia
        $bmi = null;
        $bmiCategory = 'Data tidak tersedia';
        if ($record->patient && $record->patient->height && $record->patient->weight) {
            $heightInMeters = $record->patient->height / 100;
            $bmi = $record->patient->weight / ($heightInMeters * $heightInMeters);
            $bmi = round($bmi, 2);

            // Kategori BMI
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

        // Menampilkan gambar yang terupload
        $punggungKaki = $record->getFirstMediaUrl('punggung-kaki', 'punggung_kaki') ?: null;
        $telapakKaki = $record->getFirstMediaUrl('telapak-kaki', 'telapak_kaki') ?: null;

        return view('app.medical-record.show', compact('record', 'punggungKaki', 'telapakKaki', 'bmi', 'bmiCategory'));
    }

    public function create()
    {
        // Mendapatkan pasien yang terkait dengan pengguna saat ini
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
        ]);

        $patient = Patient::where('user_id', auth()->user()->id)->firstOrFail();

        // Skor angiopati, neuropati, dan deformitas
        $angiopatiScore = ($request->jariJari1 === '-' || $request->jariJari3 === '-' || $request->jariJari5 === '-') ? 1 : 0;
        $neuropatiScore = ($request->dorsalPedis === '-' || $request->plantar === '-') ? 1 : 0;
        $deformitasScore = ($request->deformitasKanan === '+' || $request->deformitasKiri === '+') ? 2 : 0;

        $totalScore = $angiopatiScore + $neuropatiScore + $deformitasScore;

        // Menentukan kategori risiko dan hasil diagnosa
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

        // Simpan rekam medis dengan nilai deskriptif
        $medicalRecord = MedicalRecord::create([
            'patient_id' => $patient->id,
            'angiopati' => json_encode([
                'jariJari1' => $request->jariJari1,
                'jariJari3' => $request->jariJari3,
                'jariJari5' => $request->jariJari5,
            ]),
            'neuropati' => json_encode([
                'dorsalPedis' => $request->dorsalPedis,
                'plantar' => $request->plantar,
            ]),
            'deformitas' => json_encode([
                'deformitasKanan' => $request->deformitasKanan,
                'deformitasKiri' => $request->deformitasKiri,
            ]),
            'kategori_risiko' => $kategori,
            'hasil' => $hasil,
        ]);

        // Simpan gambar jika ada
        if ($request->hasFile('punggung_kaki')) {
            $this->storeFile($medicalRecord, $request->file('punggung_kaki'), 'punggung-kaki', 'punggung_kaki');
        }
        if ($request->hasFile('telapak_kaki')) {
            $this->storeFile($medicalRecord, $request->file('telapak_kaki'), 'telapak-kaki', 'telapak_kaki');
        }

        return back()->with([
            'nama_pasien' => $patient->name,
            'kategori' => $kategori,
            'hasil' => $hasil,
        ]);
    }

    public function exportPDF($id)
    {
        $record = MedicalRecord::with('patient')->findOrFail($id);

        // Decode JSON fields sebelum membuat PDF
        $record->angiopati = json_decode($record->angiopati, true);
        $record->neuropati = json_decode($record->neuropati, true);
        $record->deformitas = json_decode($record->deformitas, true);

        // Mengambil tinggi dan berat badan
        $height = $record->patient->height;
        $weight = $record->patient->weight;

        // Menghitung BMI
        $bmi = null;
        $bmiCategory = 'Data tidak tersedia';
        if ($height && $weight) {
            $heightInMeters = $height / 100;
            $bmi = $weight / ($heightInMeters * $heightInMeters);
            $bmi = round($bmi, 2);

            // Kategori BMI
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
        $punggungKaki = $record->getFirstMediaUrl('punggung-kaki', 'punggung_kaki') ?: null;
        $telapakKaki = $record->getFirstMediaUrl('telapak-kaki', 'telapak_kaki') ?: null;

        // Mengirim data ke view PDF
        $pdf = PDF::loadView('app.medical-record.export', compact('record', 'height', 'weight', 'bmi', 'bmiCategory', 'punggungKaki', 'telapakKaki'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('rekam_medis_pasien.pdf');
    }

    private function storeFile($medicalRecord, $file, $collectionName, $fileName)
    {
        // Fungsi untuk menyimpan file (gambar)
        $medicalRecord->addMedia($file)
            ->usingFileName($fileName . '.' . $file->getClientOriginalExtension())
            ->toMediaCollection($collectionName);
    }
}
