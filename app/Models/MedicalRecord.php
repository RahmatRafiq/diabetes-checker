<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 
        'angiopati', 
        'neuropati', 
        'deformitas', 
        'riwayat_luka', 
        'diet', 
        'kategori_risiko', 
        'hasil',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
