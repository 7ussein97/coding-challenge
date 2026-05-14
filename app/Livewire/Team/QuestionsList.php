<?php

namespace App\Livewire\Team;

use App\Models\Question;
use Livewire\Attributes\On;
use Livewire\Component;

class QuestionsList extends Component
{
    #[On('my-submission-updated')]
    public function refresh(): void {}

    public function render()
    {
        $team      = auth()->user()->team;
        $questions = Question::orderBy('order')->get();

        $statusMap  = [];
        $attemptMap = [];
        $commentMap = [];

        foreach ($questions as $question) {
            $subs = $team->submissions()
                ->where('question_id', $question->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $attemptMap[$question->id] = $subs->count();

            $accepted = $subs->firstWhere('status', 'accepted');
            if ($accepted) {
                $statusMap[$question->id] = 'accepted';
            } else {
                $latest = $subs->first();
                $statusMap[$question->id] = $latest?->status;
            }

            $latestWithComment = $subs->first(fn($s) => !empty($s->judge_comment));
            $commentMap[$question->id] = $latestWithComment?->judge_comment;
        }

        return view('livewire.team.questions-list', compact('questions', 'statusMap', 'attemptMap', 'commentMap'));
    }
}
