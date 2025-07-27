<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LDAP\Result;

class Exam extends Model
{
        public $incrementing = true;
    protected $fillable = [
    'titles', 'description', 'token', 'category_id', 'created_by',
    'start_time', 'end_time', 'duration_minutes', 'total_questions',
    'kkm_score', 'status', 'shuffle_question', 'shuffle_option',
    'show_result', 'max_attempts', 'instructions'

    ];

    public function bankQuestions()
{
    return $this->belongsToMany(
        Questions::class,
        'exam_question'
    )->withPivot('order')->withTimestamps();
}
    public function userExams(){
        return $this->hasMany(UserExam::class);
    }
    public function creator(){
        return $this->belongsTo(User::class);
    }
    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function questions(){
        return $this->hasMany(Questions::class);
    }
    public function usersTaken(){
        return $this->belongsToMany(User::class);
    }
    public function results(){
        return $this->hasMany(Result::class);
    }
}
