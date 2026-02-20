<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenPermohonan extends Model
{
    use HasFactory;

    protected $table = 'dokumen_permohonan';
    protected $primaryKey = 'id_dokumen_permohonan';
    public $timestamps = false;

    protected $fillable = [
        'id_permohonan',
        'id_user_upload',
        'nama_file',
        'path_file',
        'tanggal_upload',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_upload' => 'datetime',
        ];
    }
}
