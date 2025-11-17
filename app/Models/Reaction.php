<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Reaction extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'reactions';
    protected $primaryKey = '_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'reactable_type', 'reactable_id', 'user_id', 'type', 'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function reactable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }
}
