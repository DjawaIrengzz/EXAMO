<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        "user_id","plan_name","status","started_at","expired_at"
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
