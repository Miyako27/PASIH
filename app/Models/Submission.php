<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'submitter_id',
        'kanwil_operator_id',
        'division_operator_id',
        'assigned_by_id',
        'nomor_surat',
        'perihal',
        'pemda_name',
        'perda_title',
        'description',
        'status',
        'revision_note',
        'rejection_note',
        'submitted_at',
        'reviewed_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => SubmissionStatus::class,
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitter_id');
    }

    public function documents()
    {
        return $this->hasMany(SubmissionDocument::class);
    }

    public function dispositions()
    {
        return $this->hasMany(Disposition::class);
    }

    public function latestDisposition()
    {
        return $this->hasOne(Disposition::class)->latestOfMany('id');
    }

    public function divisionOperator()
    {
        return $this->belongsTo(User::class, 'division_operator_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}
