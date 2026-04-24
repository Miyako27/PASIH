<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentAnalysisApproval extends Model
{
    use HasFactory;

    protected $table = 'assignment_analysis_approvals';

    protected $fillable = [
        'assignment_id',
        'assigned_by_id',
        'revision_note',
        'approved_by_kadiv_at',
        'approved_by_kakanwil_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_by_kadiv_at' => 'datetime',
            'approved_by_kakanwil_at' => 'datetime',
        ];
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by_id');
    }
}
