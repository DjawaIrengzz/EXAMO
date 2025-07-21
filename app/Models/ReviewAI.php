<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'file_id'
        ,'ai_score'
        ,'ai_feedback'
    ];
    public function file(){
        return $this->belongsTo(File::class);
    }
}
