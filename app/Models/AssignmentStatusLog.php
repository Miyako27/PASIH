<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentStatusLog extends Model
{
    use HasFactory;

    protected $table = 'assignment_statuses';

    protected $fillable = [
        'assignment_id',
        'user_id',
        'status',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

