<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::with('user')->get();
        return view('app.patient.index', compact('patients'));
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
}
