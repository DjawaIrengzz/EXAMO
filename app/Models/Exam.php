<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LDAP\Result;

class Exam extends Model
{
    protected $fillable = [
        "title","category_id","created_by","description","start_time","end_time"
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
