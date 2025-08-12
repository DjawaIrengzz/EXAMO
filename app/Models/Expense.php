<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use Illuminate\Support\Str;
class Expense extends Model
{
    /**
     * The primary key type.
     *
     * @var string
     */
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
    
    protected $fillable = [
        'name',
        'amount',
        'date',
        'category_id',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'float',
    ];

    
}
