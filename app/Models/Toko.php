<?php

namespace App\Models;
use App\Models\Barang;
use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    public function barang(){
        return $this->hasMany(Barang::class );
    }
    public function review(){
        return $this->hasMany(Review::class);
    }
}
