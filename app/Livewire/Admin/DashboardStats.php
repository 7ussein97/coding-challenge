<?php

namespace App\Livewire\Admin;

use App\Models\Question;
use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;

class DashboardStats extends Component
{
    #[On('submissions-changed')]
    public function refresh(): void {}

    public function render()
    {
        $stats = [
            'teams'       => Team::count(),
            'judges'      => User::where('role', 'judge')->count(),
            'questions'   => Question::count(),
            'submissions' => Submission::count(),
            'pending'     => Submission::where('status', 'pending')->count(),
            'accepted'    => Submission::where('status', 'accepted')->count(),
            'rejected'    => Submission::where('status', 'rejected')->count(),
        ];

        $recentSubmissions = Submission::with(['team', 'question'])
            ->latest()
            ->take(10)
            ->get();

        return view('livewire.admin.dashboard-stats', compact('stats', 'recentSubmissions'));
    }
}
