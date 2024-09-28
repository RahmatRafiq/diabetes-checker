<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function apiTest()
    {
        $patients = Patient::all();
        return response()->json($patients);
    }
    public function index()
    {
        $patient = Auth::user()->patient;
        return response()->json($patient);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'dob' => 'required|date',
            'gender' => 'required|string|max:10',
            'contact' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'education_level' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'years_with_diabetes' => 'nullable|integer',
            'dm_therapy' => 'nullable|string|max:255',
            'gds' => 'nullable|numeric',
            'hba1c' => 'nullable|numeric',
            'diet_type' => 'nullable|string|max:255',
        ]);

        $patient = Auth::user()->patient()->create($data);

        return response()->json($patient, 201);
    }

    public function show($id)
    {
        $patient = Patient::findOrFail($id);
        return response()->json($patient);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'dob' => 'required|date',
            'gender' => 'required|string|max:10',
            'contact' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'education_level' => 'nullable|string|max:255',
            'occupation' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'years_with_diabetes' => 'nullable|integer',
            'dm_therapy' => 'nullable|string|max:255',
            'gds' => 'nullable|numeric',
            'hba1c' => 'nullable|numeric',
            'diet_type' => 'nullable|string|max:255',
        ]);

        $patient = Patient::findOrFail($id);
        $patient->update($data);

        return response()->json($patient);
    }

    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();

        return response()->json(null, 204);
    }
}
