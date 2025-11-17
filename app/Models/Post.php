<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Carbon;

class Post extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'posts';
    protected $primaryKey = '_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'body',
        'author',
        'tags',
        'comments',
        'reactions',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Contar comentarios totales
     */
    public function getCommentsCountAttribute(): int
    {
        return count($this->comments ?? []);
    }

    /**
     * Contar reacciones totales
     */
    public function getReactionsCountAttribute(): int
    {
        return count($this->reactions ?? []);
    }

    /**
     * Calcular score basado en reacciones
     */
    public function getScoreAttribute(): int
    {
        $reactions = $this->reactions ?? [];
        $score = 0;
        
        foreach ($reactions as $reaction) {
            $score += match($reaction['type'] ?? 'like') {
                'like' => 1,
                'love' => 2,
                'insightful' => 3,
                'laugh' => 1,
                default => 1
            };
        }
        
        return $score;
    }

    /**
     * Agrupar reacciones por tipo
     */
    public function getReactionsGroupedAttribute(): array
    {
        $reactions = $this->reactions ?? [];
        $grouped = [];
        
        foreach ($reactions as $reaction) {
            $type = $reaction['type'] ?? 'like';
            $grouped[$type] = ($grouped[$type] ?? 0) + 1;
        }
        
        return $grouped;
    }

    /**
     * Agregar una reacción al post
     */
    public function addReaction(string $userId, string $userName, string $type): void
    {
        $reactions = $this->reactions ?? [];
        
        // Remover reacción anterior del mismo usuario si existe
        $reactions = array_filter($reactions, fn($r) => $r['user_id'] !== $userId);
        
        // Agregar nueva reacción
        $reactions[] = [
            'user_id' => $userId,
            'user_name' => $userName,
            'type' => $type,
            'created_at' => now()->toISOString(),
        ];
        
        $this->reactions = array_values($reactions);
        $this->save();
    }

    /**
     * Remover reacción del usuario
     */
    public function removeReaction(string $userId): void
    {
        $reactions = $this->reactions ?? [];
        $reactions = array_filter($reactions, fn($r) => $r['user_id'] !== $userId);
        
        $this->reactions = array_values($reactions);
        $this->save();
    }

    /**
     * Toggle reacción del usuario
     */
    public function toggleReaction(string $userId, string $userName, string $type): bool
    {
        $reactions = $this->reactions ?? [];
        $existing = collect($reactions)->firstWhere('user_id', $userId);
        
        if ($existing && $existing['type'] === $type) {
            // Remover si es la misma reacción
            $this->removeReaction($userId);
            return false;
        } else {
            // Agregar o cambiar reacción
            $this->addReaction($userId, $userName, $type);
            return true;
        }
    }

    /**
     * Agregar un comentario al post
     */
    public function addComment(string $userId, string $userName, string $body): array
    {
        $comments = $this->comments ?? [];
        
        // Generar un ID único para el comentario
        $commentId = bin2hex(random_bytes(12)); // Genera un ID hexadecimal de 24 caracteres similar a ObjectId
        
        $comment = [
            '_id' => $commentId,
            'user_id' => $userId,
            'user_name' => $userName,
            'body' => $body,
            'reactions' => [],
            'created_at' => now()->toISOString(),
        ];
        
        $comments[] = $comment;
        $this->comments = $comments;
        $this->save();
        
        return $comment;
    }

    /**
     * Agregar reacción a un comentario
     */
    public function addCommentReaction(string $commentId, string $userId, string $userName, string $type): void
    {
        $comments = $this->comments ?? [];
        
        foreach ($comments as $index => &$comment) {
            // Comparar usando _id si existe, o el índice si no
            $currentId = $comment['_id'] ?? (string)$index;
            
            if ($currentId === $commentId) {
                $reactions = $comment['reactions'] ?? [];
                
                // Remover reacción anterior del mismo usuario
                $reactions = array_filter($reactions, fn($r) => $r['user_id'] !== $userId);
                
                // Agregar nueva reacción
                $reactions[] = [
                    'user_id' => $userId,
                    'user_name' => $userName,
                    'type' => $type,
                    'created_at' => now()->toISOString(),
                ];
                
                $comment['reactions'] = array_values($reactions);
                break;
            }
        }
        
        $this->comments = $comments;
        $this->save();
    }

    /**
     * Toggle reacción de un comentario
     */
    public function toggleCommentReaction(string $commentId, string $userId, string $userName, string $type): bool
    {
        $comments = $this->comments ?? [];
        
        foreach ($comments as $index => &$comment) {
            // Comparar usando _id si existe, o el índice si no
            $currentId = $comment['_id'] ?? (string)$index;
            
            if ($currentId === $commentId) {
                $reactions = $comment['reactions'] ?? [];
                $existing = collect($reactions)->firstWhere('user_id', $userId);
                
                if ($existing && $existing['type'] === $type) {
                    // Remover si es la misma reacción
                    $reactions = array_filter($reactions, fn($r) => $r['user_id'] !== $userId);
                    $comment['reactions'] = array_values($reactions);
                    $this->comments = $comments;
                    $this->save();
                    return false;
                } else {
                    // Agregar o cambiar reacción
                    $reactions = array_filter($reactions, fn($r) => $r['user_id'] !== $userId);
                    $reactions[] = [
                        'user_id' => $userId,
                        'user_name' => $userName,
                        'type' => $type,
                        'created_at' => now()->toISOString(),
                    ];
                    $comment['reactions'] = array_values($reactions);
                    $this->comments = $comments;
                    $this->save();
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Obtener fecha de creación legible para humanos
     */
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
            return Carbon::instance($value)->diffForHumans();
        }
        if (is_string($value) && $value !== '') {
            try {
                return Carbon::parse($value)->diffForHumans();
            } catch (\Throwable $e) {
                return null;
            }
        }
        return null;
    }
}
