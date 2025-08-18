<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


use Illuminate\Support\Str;
class AuditLogs extends Model
{
    /**
     * The primary key type.
     *
     * @var string
     */
    public $incrementing = false;
    protected $keyType = 'string';
    
    
     protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id', 'data', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
