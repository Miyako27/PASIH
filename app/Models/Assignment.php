<?php

namespace App\Models;

use App\Enums\AssignmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $with = [
        'latestPicUpdate.analyst',
        'latestPicUpdate.picAssignedBy',
        'latestApproval',
    ];

    protected $fillable = [
        'submission_id',
        'assigned_by_id',
        'instruction',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => AssignmentStatus::class,
        ];
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by_id');
    }

    public function picUpdates()
    {
        return $this->hasMany(AssignmentPicUpdate::class);
    }

    public function latestPicUpdate()
    {
        return $this->hasOne(AssignmentPicUpdate::class)->latestOfMany('id');
    }

    public function firstPicUpdate()
    {
        return $this->hasOne(AssignmentPicUpdate::class)->oldestOfMany('id');
    }

    public function analysisApprovals()
    {
        return $this->hasMany(AssignmentAnalysisApproval::class);
    }

    public function latestApproval()
    {
        return $this->hasOne(AssignmentAnalysisApproval::class)->latestOfMany('id');
    }

    public function documents()
    {
        return $this->hasMany(AssignmentDocument::class);
    }

    public function kemenkumReplyDocument()
    {
        return $this->hasOne(AssignmentKemenkumReplyDocument::class)->latestOfMany('id');
    }

    public function latestAnalysisDocument()
    {
        return $this->hasOne(AssignmentDocument::class)
            ->where('document_type', 'hasil_analisis')
            ->latestOfMany('id');
    }

    public function scopeWhereAnalyst($query, int $analystId)
    {
        return $query->whereHas('latestPicUpdate', function ($builder) use ($analystId): void {
            $builder->where('analyst_id', $analystId);
        });
    }

    public function getAnalystIdAttribute(): ?int
    {
        return $this->latestPicUpdate?->analyst_id;
    }

    public function getAnalystAttribute()
    {
        return $this->latestPicUpdate?->analyst;
    }

    public function getPicAssignedByIdAttribute(): ?int
    {
        return $this->latestPicUpdate?->pic_assigned_by_id;
    }

    public function getPicAssignedByAttribute()
    {
        return $this->latestPicUpdate?->picAssignedBy;
    }

    public function getDeadlineAtAttribute()
    {
        return $this->latestPicUpdate?->deadline_at;
    }

    public function getAssignedAtAttribute()
    {
        return $this->created_at;
    }

    public function getPicAssignedAtAttribute()
    {
        return $this->latestPicUpdate?->created_at;
    }

    public function getStartedAtAttribute()
    {
        return $this->firstPicUpdate?->created_at;
    }

    public function getSubmittedForReviewAtAttribute()
    {
        if (! in_array($this->status->value, ['pending_kadiv_approval', 'pending_kakanwil_approval', 'completed'], true)) {
            return null;
        }

        return $this->updated_at;
    }

    public function getRevisionNoteAttribute(): ?string
    {
        return $this->status->value === 'revision_by_pic'
            ? $this->latestApproval?->revision_note
            : null;
    }

    public function getApprovedByKadivAtAttribute()
    {
        return in_array($this->status->value, ['pending_kakanwil_approval', 'completed'], true)
            ? $this->latestApproval?->approved_by_kadiv_at
            : null;
    }

    public function getApprovedByKakanwilAtAttribute()
    {
        return $this->status->value === 'completed'
            ? $this->latestApproval?->approved_by_kakanwil_at
            : null;
    }

    public function getCompletedAtAttribute()
    {
        return $this->status->value === 'completed'
            ? $this->latestApproval?->approved_by_kakanwil_at
            : null;
    }
}
