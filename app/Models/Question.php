<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['exam_id', 'text', 'type', 'options', 'correct_answer'];

    public function exam(){
        return $this->belongsTo(Exam::class);
    }
    public function answers(){
        return $this->hasMany(UserAnswer::class);
    }
}
