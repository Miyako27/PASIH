<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentPicUpdate extends Model
{
    use HasFactory;

    protected $table = 'assignment_pic_updates';

    protected $fillable = [
        'assignment_id',
        'pic_assigned_by_id',
        'analyst_id',
        'deadline_at',
    ];

    protected function casts(): array
    {
        return [
            'deadline_at' => 'date',
        ];
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function analyst()
    {
        return $this->belongsTo(User::class, 'analyst_id');
    }

    public function picAssignedBy()
    {
        return $this->belongsTo(User::class, 'pic_assigned_by_id');
    }
}
