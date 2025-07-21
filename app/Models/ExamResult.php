<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    protected $fillable = [
        'exam_id',
        'user_id',
        'score',
        'submitted_at',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function exam(){
        return $this->belongsTo(Exam::class);
    }
}
