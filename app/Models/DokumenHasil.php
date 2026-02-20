<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenHasil extends Model
{
    use HasFactory;

    protected $table = 'dokumen_hasil';
    protected $primaryKey = 'id_dokumen_hasil';
    public $timestamps = false;

    protected $fillable = [
        'id_hasil',
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
