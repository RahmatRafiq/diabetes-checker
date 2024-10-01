<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'dob',
        'gender',
        'contact',
        'address',
        'education_level',
        'occupation',
        'weight',
        'height',
        'years_with_diabetes',
        'dm_therapy',
        'gds',
        'hba1c',
        'diet_type',
    ];

    protected $casts = [
        'dob' => 'date:Y-m-d',
    ];

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
