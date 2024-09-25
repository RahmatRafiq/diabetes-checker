<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Patient extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

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
        'dob' => 'date',
    ];

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('kaki-kiri')->useDisk('public')->singleFile();
        $this->addMediaCollection('kaki-kanan')->useDisk('public')->singleFile();
    }

    public function getCustomMediaPath($folder)
    {
        return 'patient/' . $this->id . '/' . $folder;
    }
}
