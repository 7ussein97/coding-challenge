<?php

namespace App\Events;

use App\Models\Submission;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubmissionLockChanged implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Submission $submission) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('judges')];
    }

    public function broadcastAs(): string
    {
        return 'lock-changed';
    }

    public function broadcastWith(): array
    {
        return ['submission_id' => $this->submission->id];
    }
}
