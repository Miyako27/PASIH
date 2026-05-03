<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionDisposition extends Model
{
    use HasFactory;

    protected $table = 'submission_dispositions';

    protected $fillable = [
        'submission_id',
        'user_id',
        'to_user_id',
        'disposition_note',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kanwilOperator()
    {
        return $this->user();
    }

    public function getNoteAttribute(): ?string
    {
        return $this->disposition_note;
    }

    public function getDisposedAtAttribute()
    {
        return $this->created_at;
    }
}
