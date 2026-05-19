<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'creativity_type_id',
        'leader_id',
        'title',
        'description',
        'date',
        'time_slot',
        'maxPeople',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'price' => 'decimal:2',
            'maxPeople' => 'integer',
        ];
    }

    public function creativityType(): BelongsTo
    {
        return $this->belongsTo(CreativityType::class);
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function enrolledCount(): int
    {
        return $this->enrollments()->count();
    }

    public function freeSpots(): int
    {
        return $this->maxPeople - $this->enrolledCount();
    }

    public function isFull(): bool
    {
        return $this->freeSpots() <= 0;
    }

    public function isEnrolled(User $user): bool
    {
        return $this->enrollments()->where('user_id', $user->id)->exists();
    }
}
