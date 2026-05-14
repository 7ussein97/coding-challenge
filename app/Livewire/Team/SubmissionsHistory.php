<?php

namespace App\Livewire\Team;

use Livewire\Attributes\On;
use Livewire\Component;

class SubmissionsHistory extends Component
{
    #[On('my-submission-updated')]
    public function refresh(): void {}

    public function render()
    {
        $team        = auth()->user()->team;
        $submissions = $team->submissions()
            ->with('question')
            ->latest()
            ->take(50)
            ->get();

        // Compute chronological attempt number per question (1-based)
        $allSubs = $team->submissions()
            ->select('id', 'question_id')
            ->orderBy('id')
            ->get()
            ->groupBy('question_id');

        $attemptNumberMap = [];
        foreach ($allSubs as $qSubs) {
            foreach ($qSubs->values() as $idx => $s) {
                $attemptNumberMap[$s->id] = $idx + 1;
            }
        }

        return view('livewire.team.submissions-history', compact('submissions', 'team', 'attemptNumberMap'));
    }
}
