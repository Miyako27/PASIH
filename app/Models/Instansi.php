<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instansi extends Model
{
    use HasFactory;

    protected $table = 'instansi';
    protected $primaryKey = 'id_instansi';

    protected $fillable = [
        'nama_instansi',
        'jenis_instansi',
        'alamat',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id_instansi', 'id_instansi');
    }
}
