<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asuransi extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'jenis_asuransi',
        'nomor_asuransi'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
