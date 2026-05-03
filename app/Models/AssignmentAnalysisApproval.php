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
        'assignment_statuses_id',
        'note',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by_id');
    }

    public function assignmentStatus()
    {
        return $this->belongsTo(AssignmentStatusLog::class, 'assignment_statuses_id');
    }
}
