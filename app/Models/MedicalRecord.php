<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MedicalRecord extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

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
    protected $casts = [
        'angiopati' => 'array',
        'neuropati' => 'array',
        'deformitas' => 'array',
    ];
}
