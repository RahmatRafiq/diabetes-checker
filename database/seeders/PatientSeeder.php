<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use Carbon\Carbon;

class PatientSeeder extends Seeder
{
    public function run()
    {
        for ($i = 1; $i <= 20; $i++) {
            Patient::create([
                'user_id' => rand(1, 100), // Mengisi user_id dengan nilai acak antara 1-100
                'name' => 'Patient ' . $i,
                'dob' => Carbon::create(rand(1950, 2000), rand(1, 12), rand(1, 28)),
                'gender' => rand(0, 1) ? 'male' : 'female',
                'contact' => '08' . rand(1000000000, 9999999999),
                'address' => 'Address ' . $i,
                'education_level' => 'Bachelor',
                'occupation' => 'Occupation ' . $i,
                'weight' => rand(50, 100),
                'height' => rand(150, 190),
                'years_with_diabetes' => rand(1, 20),
                'dm_therapy' => rand(0, 1) ? 'Insulin' : 'Obat',
                'gds' => rand(70, 200),
                'hba1c' => rand(50, 90) / 10,
                'diet_type' => rand(0, 1) ? 'Low Carb' : 'High Fiber',
            ]);
        }
    }
}
