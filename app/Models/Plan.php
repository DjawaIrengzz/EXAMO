<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use Illuminate\Support\Str;
class Plan extends Model
{
    /**
     * The primary key type.
     *
     * @var string
     */
    public $incrementing = false;
    protected $keyType = 'string';
    
    
    
    protected $fillable = ['type','duration_months','price'];
}
