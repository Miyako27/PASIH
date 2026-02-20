<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'uploaded_by',
        'document_type',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'notes',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}
