<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Comment extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'comments';
    protected $primaryKey = '_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'post_id', 'user_id', 'user_name', 'body', 'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id', '_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', '_id');
    }

    public function reactions()
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function getCreatedAtHumanAttribute(): ?string
    {
        $value = $this->getRawOriginal('created_at');
        if ($value === null) {
            $value = $this->attributes['created_at'] ?? null;
        }
        if (is_object($value) && method_exists($value, 'toDateTime')) {
            $value = $value->toDateTime();
        }
        if ($value instanceof \DateTimeInterface) {
            return \Illuminate\Support\Carbon::instance($value)->diffForHumans();
        }
        if (is_string($value) && $value !== '') {
            try {
                return \Illuminate\Support\Carbon::parse($value)->diffForHumans();
            } catch (\Throwable $e) {
                return null;
            }
        }
        return null;
    }
}
