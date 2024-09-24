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
    public function uploadFootPhotos(Request $request)
    {
        try {
            $request->validate([
                'punggung_kaki_kiri' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'telapak_kaki_kiri' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'punggung_kaki_kanan' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'telapak_kaki_kanan' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'patient_id' => 'required|exists:patients,id', // Validasi ID pasien
            ]);

            // Dapatkan pasien berdasarkan ID
            $patient = Patient::findOrFail($request->input('patient_id'));

            // Simpan file di folder "kaki-kiri" dan "kaki-kanan"
            $this->storeFile($patient, $request, 'punggung_kaki_kiri', 'kaki-kiri', 'punggung');
            $this->storeFile($patient, $request, 'telapak_kaki_kiri', 'kaki-kiri', 'telapak');
            $this->storeFile($patient, $request, 'punggung_kaki_kanan', 'kaki-kanan', 'punggung');
            $this->storeFile($patient, $request, 'telapak_kaki_kanan', 'kaki-kanan', 'telapak');

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error uploading foot photos: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while uploading foot photos. Please try again later.'], 500);
        }
    }

    private function storeFile($patient, $request, $field, $folder, $fileName)
    {
        $fileExtension = $request->file($field)->getClientOriginalExtension();
        $patient->addMediaFromRequest($field)
            ->usingFileName($fileName . '_' . time() . '.' . $fileExtension)
            ->toMediaCollection('patients/' . $patient->id . '/' . $folder); // Sesuaikan path penyimpanan berdasarkan patient_id
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
