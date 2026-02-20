<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disposition extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'from_user_id',
        'to_user_id',
        'note',
        'disposed_at',
    ];

    protected function casts(): array
    {
        return [
            'disposed_at' => 'datetime',
        ];
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
