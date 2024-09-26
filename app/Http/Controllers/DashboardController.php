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
        } elseif ($user->hasRole('user')) {
            return $this->dashboardUser();
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
        $riskCategoryByAgeGroup = Dashboard::getRiskCategoryByAgeGroup()
            ->groupBy('age_group'); // Mengelompokkan hasil berdasarkan kelompok usia
    
        $dmTherapyDistribution = Dashboard::getDmTherapyDistribution();
        $bmiDistribution = Dashboard::getBmiDistribution();
        $gdsHba1cCorrelation = Dashboard::getGdsHba1cCorrelation();
    
        return view('admin.dashboard', compact(
            'totalPatients', 
            'genderDistribution', 
            'averageAge', 
            'riskCategoryDistribution',
            'riskCategoryByAgeGroup',
            'dmTherapyDistribution',
            'bmiDistribution',
            'gdsHba1cCorrelation'
        ));
    }
    

    public function dashboardUser()
    {
        return view('user.dashboard', [
        ]);
    }

}
