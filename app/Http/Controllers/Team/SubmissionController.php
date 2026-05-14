<?php

namespace App\Http\Controllers\Team;

use App\Events\NewSubmission;
use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubmissionController extends Controller
{
    public function questions()
    {
        return view('team.questions.index');
    }

    public function create(Question $question)
    {
        $team = auth()->user()->team;

        $previousSubmissions = $team->submissions()
            ->where('question_id', $question->id)
            ->latest()
            ->take(5)
            ->get();

        return view('team.submissions.create', compact('question', 'previousSubmissions'));
    }

    public function store(Request $request, Question $question)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $team = auth()->user()->team;

        $submission = Submission::create([
            'team_id'     => $team->id,
            'question_id' => $question->id,
            'code'        => $request->code,
            'status'      => 'pending',
        ]);

        broadcast(new NewSubmission($submission))->toOthers();

        return redirect()->route('team.questions.index')
            ->with('success', 'Code submitted! Waiting for judge review.');
    }

    public function history()
    {
        return view('team.submissions.history');
    }

    public function download(Submission $submission)
    {
        $team = auth()->user()->team;
        abort_if((int) $submission->team_id !== (int) $team->id, 403);

        $submission->load(['team', 'question']);
        $teamSlug     = str_replace('-', '_', Str::slug($submission->team->name));
        $questionSlug = 'Q' . $submission->question->order;
        $attemptNum   = Submission::where('team_id', $submission->team_id)
                            ->where('question_id', $submission->question_id)
                            ->where('id', '<=', $submission->id)
                            ->count();
        $filename = "{$teamSlug}_{$questionSlug}_attempt{$attemptNum}.py";

        return response($submission->code, 200, [
            'Content-Type'        => 'text/x-python; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
