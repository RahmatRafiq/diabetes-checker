<?php

namespace App\Models;

use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
    use HasFactory;

    public static function getTotalPatients()
    {
        return Patient::count();
    }

    public static function getGenderDistribution()
    {
        return Patient::selectRaw('gender, COUNT(*) as count')
            ->groupBy('gender')
            ->pluck('count', 'gender');
    }

    public static function getAverageAge()
    {
        return Patient::selectRaw('AVG(TIMESTAMPDIFF(YEAR, dob, CURDATE())) as average_age')
            ->first()
            ->average_age;
    }

    public static function getRiskCategoryDistribution()
    {
        return MedicalRecord::selectRaw('kategori_risiko, COUNT(*) as count')
            ->groupBy('kategori_risiko')
            ->pluck('count', 'kategori_risiko');
    }
}
