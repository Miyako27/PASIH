<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilAnalisis extends Model
{
    use HasFactory;

    protected $table = 'hasil_analisis';
    protected $primaryKey = 'id_hasil';

    protected $fillable = [
        'id_penugasan',
        'id_analis',
        'ringkasan_analisis',
        'hasil_evaluasi',
        'rekomendasi',
    ];
}
