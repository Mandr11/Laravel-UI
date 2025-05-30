<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'pekerjaan_id',
        'provinsi',
        'kota_kabupaten',
        'kecamatan',
        'desa',
        'jenis_kelamin',
        'foto_pasien'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date'
    ];

    public function pekerjaan()
    {
        return $this->belongsTo(Pekerjaan::class);
    }

    public function riwayatPenyakits()
    {
        return $this->hasMany(RiwayatPenyakit::class);
    }

    public function asuransis()
    {
        return $this->hasMany(Asuransi::class);
    }
}