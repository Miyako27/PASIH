<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentKemenkumReplyDocument extends Model
{
    use HasFactory;

    protected $table = 'suratbalasan_documents';

    protected $fillable = [
        'assignment_id',
        'uploaded_by',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }
}
