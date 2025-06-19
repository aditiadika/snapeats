<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Table extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entity_id',
        'branch_id',
        'table_number',
        'qr_code',
        'capacity',
        'is_available',
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
            'is_available' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('entity', function (Builder $query) {
            if (auth()->hasUser()) {
                $query->whereBelongsTo(auth()->user()->entity);
            }
        });

        static::creating(function ($table) {
            if (auth()->hasUser()) {
                $table->entity()->associate(auth()->user()->entity);
                $table->qr_code = Str::uuid(); // Atau Str::random(10)
            }
        });
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
