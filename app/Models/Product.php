<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entity_id',
        'category_id',
        'name',
        'description',
        'price',
        'image_path',
        'is_active',
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
            'price' => 'decimal:2',
            'is_active' => 'boolean',
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class);
    }
}
