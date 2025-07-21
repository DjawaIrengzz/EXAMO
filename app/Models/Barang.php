<?php

namespace App\Models;
use App\Models\Toko;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $fillable =['toko_id', 'review_id', 'harga' , 'nama' , 'stock', 'deskripsi', 'terjual'];

    public function toko(){
        return $this->belongsTo(Toko::class, 'barang_toko');
    }
    public function review(){
        return $this->hasMany(Review::class);
    }
}
