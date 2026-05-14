<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'image', 'order'];

    public function submissions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    public function getDisplayTitleAttribute(): string
    {
        return $this->title ?: 'Problem #' . $this->order;
    }

    public function firstSolvedBy(): ?Team
    {
        $first = $this->submissions()
            ->where('status', 'accepted')
            ->orderBy('created_at')
            ->first();

        return $first?->team;
    }
}
