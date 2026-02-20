<?php

namespace App\Models;

use App\Enums\AssignmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'assigned_by_id',
        'analyst_id',
        'instruction',
        'deadline_at',
        'status',
        'assigned_at',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => AssignmentStatus::class,
            'deadline_at' => 'date',
            'assigned_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function analyst()
    {
        return $this->belongsTo(User::class, 'analyst_id');
    }

    public function documents()
    {
        return $this->hasMany(AssignmentDocument::class);
    }

    public function latestAnalysisDocument()
    {
        return $this->hasOne(AssignmentDocument::class)
            ->where('document_type', 'hasil_analisis')
            ->latestOfMany('id');
    }
}
