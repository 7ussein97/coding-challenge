<?php

namespace App\Events;

use App\Models\Submission;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubmissionUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Submission $submission) {}

    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel('judges'),
            new Channel('leaderboard'),
        ];

        if ($this->submission->team_id) {
            $channels[] = new PrivateChannel("team.{$this->submission->team_id}");
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'submission-updated';
    }

    public function broadcastWith(): array
    {
        return ['submission_id' => $this->submission->id];
    }
}
