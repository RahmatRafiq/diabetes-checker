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

    // Kategori Risiko Berdasarkan Usia
    public static function getRiskCategoryByAgeGroup()
    {
        return MedicalRecord::selectRaw("
            CASE 
                WHEN TIMESTAMPDIFF(YEAR, patients.dob, CURDATE()) < 30 THEN '<30'
                WHEN TIMESTAMPDIFF(YEAR, patients.dob, CURDATE()) BETWEEN 30 AND 50 THEN '30-50'
                ELSE '>50'
            END as age_group, 
            kategori_risiko, 
            COUNT(*) as count")
        ->join('patients', 'medical_records.patient_id', '=', 'patients.id')
        ->groupBy('age_group', 'kategori_risiko')
        ->get();
    }
    
    

    // Presentase Pasien Berdasarkan Terapi DM
    public static function getDmTherapyDistribution()
    {
        return Patient::selectRaw('dm_therapy, COUNT(*) as count')
            ->groupBy('dm_therapy')
            ->pluck('count', 'dm_therapy');
    }

    // BMI Analysis
    public static function getBmiDistribution()
    {
        return Patient::selectRaw("
            CASE
                WHEN (weight / (height / 100 * height / 100)) < 18.5 THEN 'Underweight'
                WHEN (weight / (height / 100 * height / 100)) BETWEEN 18.5 AND 24.9 THEN 'Normal'
                WHEN (weight / (height / 100 * height / 100)) BETWEEN 25 AND 29.9 THEN 'Overweight'
                ELSE 'Obese'
            END as bmi_category, COUNT(*) as count")
            ->whereNotNull('weight')
            ->whereNotNull('height')
            ->groupBy('bmi_category')
            ->pluck('count', 'bmi_category');
    }
    

    // Korelasi GDS dan HbA1c
    public static function getGdsHba1cCorrelation()
    {
        return Patient::select('gds', 'hba1c')
            ->whereNotNull('gds')
            ->whereNotNull('hba1c')
            ->get();
    }

}
