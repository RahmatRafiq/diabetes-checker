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
        // Mengambil semua rekam medis dengan relasi ke pasien
        $medicalRecords = MedicalRecord::with('patient')->get();
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

        // Memproses data sebelum dikirim ke DataTables
        $data = $query->get()->map(function ($record) {
            // Convert the array data into readable strings
            $record->angiopati = 'Dorsal: ' . $record->angiopati['dorsal'] . ', Plantar: ' . $record->angiopati['plantar'];
            $record->neuropati = 'JariJari1: ' . $record->neuropati['jariJari1'] . ', JariJari3: ' . $record->neuropati['jariJari3'] . ', JariJari5: ' . $record->neuropati['jariJari5'];
            $record->deformitas = 'Kiri: ' . $record->deformitas['kiri'] . ', Kanan: ' . $record->deformitas['kanan'];
            return $record;
        });

        // Mengambil data untuk DataTables dengan paginasi
        $data = DataTable::paginate($data, $request);

        return response()->json($data);
    }

    public function show($id)
    {
        // Menemukan rekam medis berdasarkan ID
        $record = MedicalRecord::with('patient')->findOrFail($id);

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

        // Mengonversi data array ke format string yang dapat dibaca
        $angiopati = 'Dorsal: ' . $record->angiopati['dorsal'] . ', Plantar: ' . $record->angiopati['plantar'];
        $neuropati = 'JariJari1: ' . $record->neuropati['jariJari1'] . ', JariJari3: ' . $record->neuropati['jariJari3'] . ', JariJari5: ' . $record->neuropati['jariJari5'];
        $deformitas = 'Kiri: ' . $record->deformitas['kiri'] . ', Kanan: ' . $record->deformitas['kanan'];

        $punggungKaki = $record->getFirstMediaUrl('punggung-kaki', 'punggung_kaki') ?: null;
        $telapakKaki = $record->getFirstMediaUrl('telapak-kaki', 'telapak_kaki') ?: null;

        // Kirim data ke view
        return view('app.medical-record.show', compact('record', 'punggungKaki', 'telapakKaki', 'bmi', 'bmiCategory', 'angiopati', 'neuropati', 'deformitas'));
    }

    public function create()
    {
        // Mendapatkan pasien berdasarkan pengguna saat ini
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

        // Laravel akan otomatis menghandle JSON
        $medicalRecord = MedicalRecord::create([
            'patient_id' => $patient->id,
            'angiopati' => [
                'dorsal' => ($request->dorsalPedis === '-' ? 'Tidak' : 'Ya'),
                'plantar' => ($request->plantar === '-' ? 'Tidak' : 'Ya'),
            ],
            'neuropati' => [
                'jariJari1' => ($request->jariJari1 === '-' ? 'Tidak Merasakan' : 'Merasakan'),
                'jariJari3' => ($request->jariJari3 === '-' ? 'Tidak Merasakan' : 'Merasakan'),
                'jariJari5' => ($request->jariJari5 === '-' ? 'Tidak Merasakan' : 'Merasakan'),
            ],
            'deformitas' => [
                'kiri' => ($request->deformitasKiri === '+' ? 'Ada deformitas' : 'Tidak ada deformitas'),
                'kanan' => ($request->deformitasKanan === '+' ? 'Ada deformitas' : 'Tidak ada deformitas'),
            ],
            'kategori_risiko' => $kategori,
            'hasil' => $hasil,
        ]);

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
        $height = $record->patient->height;
        $weight = $record->patient->weight;

        // Menghitung BMI
        $bmi = null;
        $bmiCategory = 'Data tidak tersedia';
        if ($height && $weight) {
            $heightInMeters = $height / 100;
            $bmi = $weight / ($heightInMeters * $heightInMeters);
            $bmi = round($bmi, 2);

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

        // Mengonversi data array ke format string yang dapat dibaca
        $angiopati = 'Dorsal: ' . $record->angiopati['dorsal'] . ', Plantar: ' . $record->angiopati['plantar'];
        $neuropati = 'JariJari1: ' . $record->neuropati['jariJari1'] . ', JariJari3: ' . $record->neuropati['jariJari3'] . ', JariJari5: ' . $record->neuropati['jariJari5'];
        $deformitas = 'Kiri: ' . $record->deformitas['kiri'] . ', Kanan: ' . $record->deformitas['kanan'];

        // Menyimpan URL gambar media
        $punggungKaki = $record->getFirstMediaUrl('punggung-kaki', 'punggung_kaki') ?: null;
        $telapakKaki = $record->getFirstMediaUrl('telapak-kaki', 'telapak_kaki') ?: null;

        // Mengirim data ke view PDF, termasuk string hasil konversi
        $pdf = PDF::loadView('app.medical-record.export', compact('record', 'height', 'weight', 'bmi', 'bmiCategory', 'angiopati', 'neuropati', 'deformitas', 'punggungKaki', 'telapakKaki'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('rekam_medis_pasien.pdf');
    }

}
