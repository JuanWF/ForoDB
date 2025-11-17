<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Post extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'posts';
    protected $primaryKey = '_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'title', 'body', 'author_id', 'author_name', 'score', 'comments_count', 'tags', 'created_at', 'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'score' => 'int',
        'comments_count' => 'int',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', '_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id', '_id');
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
