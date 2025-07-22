<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LDAP\Result;

class Exam extends Model
{
    protected $fillable = [
    'titles', 'description', 'token', 'category_id', 'created_by',
    'start_time', 'end_time', 'duration_minutes', 'total_questions',
    'kkm_score', 'status', 'shuffle_question', 'shuffle_option',
    'show_result', 'max_attempts', 'instructions'

    ];
    public function creator(){
        return $this->belongsTo(User::class);
    }
    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function question(){
        return $this->hasMany(Question::class);
    }
    public function usersTaken(){
        return $this->belongsToMany(User::class);
    }
    public function results(){
        return $this->hasMany(Result::class);
    }
}
