<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Questions extends Model
{
    protected $table = 'questions';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;
    protected $fillable = [
        "exam_id",
        "question",
        "type",
        "options",
        "correct_answer",
        "explanation",
        "image",
        "order",
        "is_active"
    ];

    public function exam(){
        return $this->belongsTo(Exam::class, 'exam_id');
    }

}
