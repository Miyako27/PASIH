<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $with = [
        'latestStatus',
        'latestReviewStatus',
        'latestDisposition.toUser',
    ];

    protected $fillable = [
        'submitter_id',
        'nomor_surat',
        'perihal',
        'pemda_name',
        'pemda_title',
        'description',
    ];

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitter_id');
    }

    public function documents()
    {
        return $this->hasMany(SubmissionDocument::class);
    }

    public function statuses()
    {
        return $this->hasMany(SubmissionStatusLog::class);
    }

    public function latestStatus()
    {
        return $this->hasOne(SubmissionStatusLog::class)->latestOfMany('id');
    }

    public function latestReviewStatus()
    {
        return $this->hasOne(SubmissionStatusLog::class)
            ->whereIn('status', ['accepted', 'revised', 'rejected'])
            ->latestOfMany('id');
    }

    public function dispositions()
    {
        return $this->hasMany(SubmissionDisposition::class);
    }

    public function latestDisposition()
    {
        return $this->hasOne(SubmissionDisposition::class)->latestOfMany('id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function scopeWhereStatus($query, string $status)
    {
        return $query->whereHas('latestStatus', function ($statusQuery) use ($status): void {
            $statusQuery->where('status', $status);
        });
    }

    public function scopeWhereStatusIn($query, array $statuses)
    {
        return $query->whereHas('latestStatus', function ($statusQuery) use ($statuses): void {
            $statusQuery->whereIn('status', $statuses);
        });
    }

    public function getStatusAttribute(): SubmissionStatus
    {
        $statusValue = $this->latestStatus?->status ?? SubmissionStatus::Submitted->value;

        return SubmissionStatus::tryFrom((string) $statusValue) ?? SubmissionStatus::Submitted;
    }

    public function getSubmittedAtAttribute()
    {
        return $this->created_at;
    }

    public function getReviewedAtAttribute()
    {
        if ($this->latestReviewStatus?->created_at) {
            return $this->latestReviewStatus->created_at;
        }

        return $this->latestDisposition?->disposed_at;
    }

    public function getFinishedAtAttribute()
    {
        $latestStatus = $this->latestStatus;

        return $latestStatus?->status === SubmissionStatus::Completed->value
            ? $latestStatus->created_at
            : null;
    }

    public function getRevisionNoteAttribute()
    {
        return $this->latestStatus?->note;
    }

    public function getRejectionNoteAttribute()
    {
        return $this->latestStatus?->note;
    }

    public function getStatusNoteAttribute()
    {
        return $this->latestStatus?->note;
    }

    public function getKanwilOperatorIdAttribute()
    {
        return $this->latestStatus?->kanwil_operator_id;
    }

    public function getDivisionOperatorIdAttribute()
    {
        return $this->latestDisposition?->to_user_id;
    }

    public function getDivisionOperatorAttribute()
    {
        return $this->latestDisposition?->toUser;
    }

    public function getPemdaNameAttribute(): string
    {
        return trim((string) ($this->attributes['pemda_name'] ?? ''));
    }

    public function getPemdaTitleAttribute(): string
    {
        return trim((string) ($this->attributes['pemda_title'] ?? ''));
    }

    public function getPerdaTitleAttribute(): string
    {
        return $this->pemda_title;
    }

    public function setPemdaNameAttribute(string $value): void
    {
        $this->attributes['pemda_name'] = trim($value);
    }

    public function setPemdaTitleAttribute(string $value): void
    {
        $this->attributes['pemda_title'] = trim($value);
    }

    public function setPerdaTitleAttribute(string $value): void
    {
        $this->attributes['pemda_title'] = trim($value);
    }

    public function recordStatus(string $status, ?int $kanwilOperatorId = null, ?string $note = null): void
    {
        $this->statuses()->create([
            'kanwil_operator_id' => $kanwilOperatorId,
            'status' => $status,
            'note' => $note,
        ]);

        $this->unsetRelation('latestStatus');
        $this->unsetRelation('latestReviewStatus');
        $this->unsetRelation('statuses');
        $this->load(['latestStatus', 'latestReviewStatus']);
    }

}
