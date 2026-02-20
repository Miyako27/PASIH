<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disposisi extends Model
{
    use HasFactory;

    protected $table = 'disposisi';
    protected $primaryKey = 'id_disposisi';
    public $timestamps = false;

    protected $fillable = [
        'id_permohonan',
        'dari_user',
        'ke_instansi',
        'catatan',
        'tanggal_disposisi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_disposisi' => 'datetime',
        ];
    }
}
