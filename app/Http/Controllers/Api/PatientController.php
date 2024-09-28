<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    public function showProfile()
    {
        $patient = Auth::user()->patient;

        if (!$patient) {
            return response()->json(['message' => 'Patient profile not found'], 404);
        }

        return response()->json($patient);
    }

    public function updateProfile(Request $request)
    {
        $patient = Auth::user()->patient;

        $request->validate([
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

        $patient->update($request->all());

        return response()->json(['message' => 'Profile updated successfully']);
    }
}
