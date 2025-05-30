<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPenyakit extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'nama_penyakit',
        'tahun'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
