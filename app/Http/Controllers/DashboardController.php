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
    
        return view('admin.dashboard', compact(
            'totalPatients', 
            'genderDistribution', 
            'averageAge', 
            'riskCategoryDistribution'
        ));
    }
    

    public function dashboardUser()
    {
        return view('user.dashboard', [
        ]);
    }

}
