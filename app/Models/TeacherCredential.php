<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherCredential extends Model
{
    protected $fillable = [
        'user_id',
        'teacher_key',
        'teacher_id',
    ];
    public function user() {
        return $this->belongsTo(User::class);
    }


}
