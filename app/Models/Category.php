<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entity_id',
        'name',
        'description',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('entity', function (Builder $query) {
            if (auth()->hasUser()) {
                $query->whereBelongsTo(auth()->user()->entity);
            }
        });

        static::creating(function ($post) {
            if (auth()->hasUser()) {
                $post->entity()->associate(auth()->user()->entity);
            }
        });
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }
}
