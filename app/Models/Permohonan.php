<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permohonan extends Model
{
    use HasFactory;

    protected $table = 'permohonan';
    protected $primaryKey = 'id_permohonan';

    protected $fillable = [
        'nomor_surat',
        'tanggal_pengajuan',
        'perihal',
        'deskripsi',
        'id_pengaju',
        'status_permohonan',
        'catatan_revisi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'date',
        ];
    }

    public function pengaju()
    {
        return $this->belongsTo(User::class, 'id_pengaju', 'id');
    }

    public function dokumenPermohonan()
    {
        return $this->hasMany(DokumenPermohonan::class, 'id_permohonan', 'id_permohonan');
    }
}
