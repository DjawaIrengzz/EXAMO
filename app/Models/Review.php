<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['ulasan', 'reviewable_id', 'reviewable_type', 'user_id', 'rating' ];
    public function barang(){
        return $this->belongsTo(Barang::class);
    }
    public function toko(){
        return $this->belongsTo(Toko::class);
    }
}
