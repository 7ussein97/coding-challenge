<?php

namespace App\Livewire\Judge;

use App\Models\Submission;
use Livewire\Attributes\On;
use Livewire\Component;

class JudgeDashboard extends Component
{
    #[On('submissions-changed')]
    public function refresh(): void {}

    public function render()
    {
        $stats = [
            'pending'   => Submission::where('status', 'pending')->count(),
            'accepted'  => Submission::where('status', 'accepted')->count(),
            'rejected'  => Submission::where('status', 'rejected')->count(),
            'my_locked' => Submission::where('locked_by', auth()->id())->count(),
        ];

        $pendingSubmissions = Submission::with(['team', 'question'])
            ->where('status', 'pending')
            ->latest()
            ->take(15)
            ->get();

        return view('livewire.judge.judge-dashboard', compact('stats', 'pendingSubmissions'));
    }
}
