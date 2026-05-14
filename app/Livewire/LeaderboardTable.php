<?php

namespace App\Livewire;

use App\Models\Question;
use App\Models\Submission;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class LeaderboardTable extends Component
{
    #[On('leaderboard-updated')]
    public function refresh(): void {}

    public function render()
    {
        return view('livewire.leaderboard-table', [
            'leaderboard'  => $this->buildLeaderboard(),
            'questions'    => Question::orderBy('order')->get(),
            'firstSolvers' => $this->getFirstSolvers(),
        ]);
    }

    private function buildLeaderboard()
    {
        $teams = Team::with('user')->get();

        $leaderboard = $teams->map(function (Team $team) {
            $accepted = Submission::where('team_id', $team->id)
                ->where('status', 'accepted')
                ->select('question_id', DB::raw('MIN(created_at) as first_solve_time'))
                ->groupBy('question_id')
                ->get();

            $solvedCount   = $accepted->count();
            $lastSolveTime = $accepted->max('first_solve_time');
            $totalAttempts = Submission::where('team_id', $team->id)->count();

            $lastQuestion = null;
            if ($solvedCount > 0 && $lastSolveTime) {
                $sub = Submission::where('team_id', $team->id)
                    ->where('status', 'accepted')
                    ->with('question')
                    ->orderByDesc('created_at')
                    ->first();
                $lastQuestion = $sub?->question?->display_title;
            }

            return [
                'id'                   => $team->id,
                'name'                 => $team->name,
                'solved_count'         => $solvedCount,
                'last_solve_time'      => $lastSolveTime,
                'last_solved_question' => $lastQuestion,
                'total_attempts'       => $totalAttempts,
            ];
        });

        return $leaderboard->sort(function ($a, $b) {
            if ($a['solved_count'] !== $b['solved_count']) {
                return $b['solved_count'] <=> $a['solved_count'];
            }
            if (!$a['last_solve_time']) return 1;
            if (!$b['last_solve_time']) return -1;
            return $a['last_solve_time'] <=> $b['last_solve_time'];
        })->values();
    }

    private function getFirstSolvers(): array
    {
        $firstSolvers = [];
        foreach (Question::all() as $question) {
            $first = Submission::where('question_id', $question->id)
                ->where('status', 'accepted')
                ->orderBy('created_at')
                ->with('team')
                ->first();
            if ($first) {
                $firstSolvers[$question->id] = $first->team->name;
            }
        }
        return $firstSolvers;
    }
}
