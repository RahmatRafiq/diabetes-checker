<?php

namespace App\Http\Controllers;

use App\Models\Dashboard;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return $this->dashboardAdmin();
        } elseif ($user->hasRole('pasien')) {
            return $this->dashboardPasien();
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    public function dashboardAdmin()
    {
        $totalPatients = Dashboard::getTotalPatients();
        $genderDistribution = Dashboard::getGenderDistribution();
        $averageAge = Dashboard::getAverageAge();
        $riskCategoryDistribution = Dashboard::getRiskCategoryDistribution();
        $riskCategoryByAgeGroup = Dashboard::getRiskCategoryByAgeGroup();
        $under30 = $riskCategoryByAgeGroup->filter(function ($item) {
            return $item->age_group == '<30';
        });

        $between30and50 = $riskCategoryByAgeGroup->filter(function ($item) {
            return $item->age_group == '30-50';
        });

        $over50 = $riskCategoryByAgeGroup->filter(function ($item) {
            return $item->age_group == '>50';
        });

        $dmTherapyDistribution = Dashboard::getDmTherapyDistribution();
        $bmiDistribution = Dashboard::getBmiDistribution();
        $gdsHba1cCorrelation = Dashboard::getGdsHba1cCorrelation();
        $dmTherapyNames = $dmTherapyDistribution->pluck('dm_therapy')->toArray();
        $dmTherapyCounts = $dmTherapyDistribution->pluck('count')->toArray();
        $genderLabels = $genderDistribution->pluck('gender')->toArray();
        $genderCounts = $genderDistribution->pluck('count')->toArray();

        return view('admin.dashboard', compact(
            'totalPatients',
            'genderDistribution',
            'averageAge',
            'riskCategoryDistribution',
            'riskCategoryByAgeGroup',
            'under30', // Data usia <30
            'between30and50', // Data usia 30-50
            'over50', // Data usia >50
            'dmTherapyDistribution',
            'bmiDistribution',
            'gdsHba1cCorrelation',
            'dmTherapyNames', // Nama terapi DM
            'dmTherapyCounts', // Jumlah terapi DM
            'genderLabels', // Tambahkan label gender ke view
            'genderCounts'
        ));
    }
public function dashboardPasien()
    {
        $patient = Auth::user()->patient;
        $medicalRecords = $patient->medicalRecords;
        $latestMedicalRecord = $medicalRecords->first();
        $latestMedicalRecord->load('patient');

        return view('app.medical-record.create', compact('patient', 'latestMedicalRecord'));
    }

}
