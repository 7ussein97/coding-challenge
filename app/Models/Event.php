<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'start_time', 'end_time'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    public function isActive(): bool
    {
        $now = now();
        return $this->start_time <= $now && (!$this->end_time || $this->end_time >= $now);
    }
}
