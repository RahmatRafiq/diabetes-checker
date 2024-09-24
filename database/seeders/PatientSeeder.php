<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Membuat 20 data dummy pasien
        for ($i = 1; $i <= 20; $i++) {
            DB::table('patients')->insert([
                'user_id' => $i, // Sesuaikan ID user yang ada di tabel users
                'name' => 'Patient ' . $i,
                'dob' => now()->subYears(rand(20, 60))->format('Y-m-d'),
                'gender' => ['male', 'female'][rand(0, 1)], // Random gender
                'contact' => '08' . rand(1111111111, 9999999999),
                'address' => 'Address ' . $i,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
