<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


use Illuminate\Support\Str;
class UserExam extends Model
{
    /**
     * The primary key type.
     *
     * @var string
     */
    public $incrementing = false;
    protected $keyType = 'string';
    
    
    
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exam_id',
        'started_at',
        'finished_at',
        'deadline',
        'score',
        'correct_answers',
        'wrong_answers',
        'unanswered',
        'answers',
        'status',
        'attempt_number',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'deadline' => 'datetime',
        'answers' => 'array',
    ];

    // RELATIONS
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
