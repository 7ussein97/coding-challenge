<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'question_id',
        'code',
        'status',
        'judge_comment',
        'locked_by',
        'locked_at',
    ];

    protected $casts = [
        'locked_at' => 'datetime',
    ];

    const LOCK_TIMEOUT = 30; // minutes

    public function team(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function question(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function judge(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function isLocked(): bool
    {
        if (!$this->locked_by) return false;
        if ($this->locked_at && $this->locked_at->addMinutes(self::LOCK_TIMEOUT)->isPast()) return false;
        return true;
    }

    public function isLockedBy(int $userId): bool
    {
        return $this->isLocked() && (int) $this->locked_by === $userId;
    }

    public function lock(int $userId): void
    {
        $this->update(['locked_by' => $userId, 'locked_at' => now()]);
    }

    public function unlock(): void
    {
        $this->update(['locked_by' => null, 'locked_at' => null]);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'accepted' => 'success',
            'rejected' => 'danger',
            default    => 'warning',
        };
    }
}
