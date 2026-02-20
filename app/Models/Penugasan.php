<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penugasan extends Model
{
    use HasFactory;

    protected $table = 'penugasan';
    protected $primaryKey = 'id_penugasan';

    protected $fillable = [
        'id_permohonan',
        'catatan_penugasan',
        'batas_waktu',
        'status_penugasan',
        'diambil_oleh',
        'ditugaskan_oleh',
        'tanggal_penugasan',
        'tanggal_diambil',
        'tanggal_selesai',
    ];

    protected function casts(): array
    {
        return [
            'batas_waktu' => 'date',
            'tanggal_penugasan' => 'datetime',
            'tanggal_diambil' => 'datetime',
            'tanggal_selesai' => 'datetime',
        ];
    }
}
