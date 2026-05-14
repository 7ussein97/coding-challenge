<?php

namespace App\Livewire\Judge;

use App\Models\Question;
use App\Models\Submission;
use Livewire\Attributes\On;
use Livewire\Component;

class SubmissionsTable extends Component
{
    public string $statusFilter   = '';
    public string $questionFilter = '';

    #[On('submissions-changed')]
    public function refresh(): void {}

    public function updatedStatusFilter(): void   {}
    public function updatedQuestionFilter(): void {}

    public function clearFilters(): void
    {
        $this->statusFilter   = '';
        $this->questionFilter = '';
    }

    public function render()
    {
        $query = Submission::with(['team', 'question', 'judge']);

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->questionFilter) {
            $query->where('question_id', $this->questionFilter);
        }

        return view('livewire.judge.submissions-table', [
            'submissions' => $query->latest()->take(100)->get(),
            'questions'   => Question::orderBy('order')->get(),
        ]);
    }
}
