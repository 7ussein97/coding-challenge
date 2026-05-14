<?php

namespace App\Livewire\Team;

use App\Models\Question;
use Livewire\Attributes\On;
use Livewire\Component;

class TeamDashboard extends Component
{
    #[On('my-submission-updated')]
    public function refresh(): void {}

    public function render()
    {
        $team = auth()->user()->team;

        $stats = [
            'total_submissions' => $team->submissions()->count(),
            'accepted'          => $team->submissions()->where('status', 'accepted')->count(),
            'pending'           => $team->submissions()->where('status', 'pending')->count(),
            'rejected'          => $team->submissions()->where('status', 'rejected')->count(),
            'solved'            => $team->submissions()
                                       ->where('status', 'accepted')
                                       ->distinct('question_id')
                                       ->count('question_id'),
            'total_questions'   => Question::count(),
        ];

        $recentSubmissions = $team->submissions()
            ->with('question')
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.team.team-dashboard', compact('stats', 'recentSubmissions', 'team'));
    }
}
