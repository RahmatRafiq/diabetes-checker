<?php

namespace App\Http\Controllers;

use App\Helpers\DataTable;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::with('user')->get();
        return view('app.patient.index', compact('patients'));
    }
    public function json(Request $request)
    {
        $search = $request->search['value']; // Nilai pencarian dari DataTables
        $query = Patient::query();

        // Definisi kolom yang digunakan untuk sorting
        $columns = [
            'id',
            'name',
            'user.email',
            'dob',
            'gender',
            'contact',
            'address',
        ];

        // Relasi dengan 'user' untuk mengambil email
        $query->with('user');

        // Pencarian berdasarkan nama, email, atau kontak
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('email', 'like', "%{$search}%");
                })
                ->orWhere('contact', 'like', "%{$search}%");
        }

        // Sorting berdasarkan kolom
        if ($request->filled('order')) {
            $query->orderBy($columns[$request->order[0]['column']], $request->order[0]['dir']);
        }

        // Menggunakan helper DataTable untuk paginasi
        $data = DataTable::paginate($query, $request);

        return response()->json($data);
    }
    public function create()
    {
        return view('app.patient.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'dob' => 'required|date',
            'gender' => 'required|string',
            'contact' => 'required|string|max:15',
            'address' => 'required|string|max:255',
        ]);

        // Buat user terlebih dahulu
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);

        // Assign role 'pasien' ke user yang baru dibuat
        $role = Role::where('name', 'pasien')->first();
        $user->assignRole($role);

        // Buat data pasien terkait
        Patient::create([
            'user_id' => $user->id,
            'name' => $validatedData['name'],
            'dob' => $validatedData['dob'],
            'gender' => $validatedData['gender'],
            'contact' => $validatedData['contact'],
            'address' => $validatedData['address'],
        ]);

        return redirect()->route('patients.index')->with('success', 'Pasien dan User berhasil dibuat.');
    }

    public function edit(Patient $patient)
    {
        return view('app.patient.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $patient->user_id,
            'password' => 'nullable|string|min:8|confirmed',
            'dob' => 'required|date',
            'gender' => 'required|string',
            'contact' => 'required|string|max:15',
            'address' => 'required|string|max:255',
        ]);

        // Update user
        $user = $patient->user;
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        if ($request->filled('password')) {
            $user->password = bcrypt($validatedData['password']);
        }
        $user->save();

        // Update patient
        $patient->update([
            'dob' => $validatedData['dob'],
            'gender' => $validatedData['gender'],
            'contact' => $validatedData['contact'],
            'address' => $validatedData['address'],
        ]);

        return redirect()->route('patients.index')->with('success', 'Pasien berhasil diperbarui.');
    }

    public function destroy(Patient $patient)
    {
        $patient->user->delete(); // Menghapus user terkait
        $patient->delete(); // Menghapus data pasien
        return redirect()->route('patients.index')->with('success', 'Pasien dan User berhasil dihapus.');
    }

     // Menampilkan form edit untuk pemilik profil
     public function editProfile()
     {
         $patient = Auth::user()->patient;
     
         // Cek apakah objek patient ada
         if (!$patient) {
             // Anda bisa mengarahkan ke halaman error atau buat record patient baru jika perlu
             return redirect()->route('some.route')->withErrors('Profil pasien tidak ditemukan.');
         }
     
         return view('app.patient.additional-patient-information', compact('patient'));
     }
     
 
     // Mengupdate profil pasien
     public function updateProfile(Request $request)
     {
         $patient = Auth::user()->patient;
 
         // Validasi input dari form
         $request->validate([
             'name' => 'required|string|max:255',
             'dob' => 'required|date',
             'gender' => 'required|string|max:10',
             'contact' => 'required|string|max:15',
             'address' => 'required|string|max:255',
             'education_level' => 'nullable|string|max:255',
             'occupation' => 'nullable|string|max:255',
             'weight' => 'nullable|numeric',
             'height' => 'nullable|numeric',
             'years_with_diabetes' => 'nullable|numeric',
             'dm_therapy' => 'nullable|string|max:255',
             'gds' => 'nullable|numeric',
             'hba1c' => 'nullable|numeric',
             'diet_type' => 'nullable|string|max:255',
         ]);
 
         // Update data patient
         $patient->update($request->all());
 
         return redirect()->back()->with('success', 'Profil berhasil diperbarui');
     }
}
