<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionStatusLog extends Model
{
    use HasFactory;

    protected $table = 'submission_statuses';

    protected $fillable = [
        'submission_id',
        'kanwil_operator_id',
        'status',
        'note',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function kanwilOperator()
    {
        return $this->belongsTo(User::class, 'kanwil_operator_id');
    }
}
