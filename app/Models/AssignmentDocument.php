<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'uploaded_by',
        'document_type',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'notes',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }
}
