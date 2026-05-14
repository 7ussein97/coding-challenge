<?php

namespace App\Http\Controllers\Judge;

use App\Events\SubmissionLockChanged;
use App\Events\SubmissionUpdated;
use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubmissionController extends Controller
{
    public function index()
    {
        return view('judge.submissions.index');
    }

    public function show(Submission $submission)
    {
        $judgeId = auth()->id();

        // Already judged — just view, no locking
        if ($submission->status !== 'pending') {
            $submission->load(['team', 'question', 'judge']);
            $filename = $this->buildFilename($submission);
            return view('judge.submissions.show', compact('submission', 'filename'));
        }

        // Locked by someone else
        if ($submission->isLocked() && !$submission->isLockedBy($judgeId)) {
            return redirect()->route('judge.submissions.index')
                ->with('error', "This submission is currently locked by judge: {$submission->judge->name}.");
        }

        // Acquire lock if not already held
        if (!$submission->isLockedBy($judgeId)) {
            $submission->lock($judgeId);
            broadcast(new SubmissionLockChanged($submission))->toOthers();
        }

        $submission->load(['team', 'question', 'judge']);
        $filename = $this->buildFilename($submission);
        return view('judge.submissions.show', compact('submission', 'filename'));
    }

    public function download(Submission $submission)
    {
        $submission->load(['team', 'question']);
        $filename = $this->buildFilename($submission);

        return response($submission->code, 200, [
            'Content-Type'        => 'text/x-python; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function buildFilename(Submission $submission): string
    {
        $teamSlug     = str_replace('-', '_', Str::slug($submission->team->name));
        $questionSlug = 'Q' . $submission->question->order;
        $attemptNum   = Submission::where('team_id', $submission->team_id)
                            ->where('question_id', $submission->question_id)
                            ->where('id', '<=', $submission->id)
                            ->count();
        return "{$teamSlug}_{$questionSlug}_attempt{$attemptNum}.py";
    }

    public function unlock(Submission $submission)
    {
        if ((int) $submission->locked_by === auth()->id()) {
            $submission->unlock();
            broadcast(new SubmissionLockChanged($submission))->toOthers();
        }

        return redirect()->route('judge.submissions.index')
            ->with('success', 'Submission lock released.');
    }

    public function review(Request $request, Submission $submission)
    {
        if (!$submission->isLockedBy(auth()->id())) {
            return redirect()->route('judge.submissions.index')
                ->with('error', 'You do not hold the lock for this submission.');
        }

        $request->validate([
            'verdict'       => 'required|in:accepted,rejected',
            'judge_comment' => 'nullable|string|max:2000',
        ]);

        $submission->update([
            'status'        => $request->verdict,
            'judge_comment' => $request->judge_comment,
            'locked_by'     => null,
            'locked_at'     => null,
        ]);

        broadcast(new SubmissionUpdated($submission))->toOthers();
        broadcast(new SubmissionLockChanged($submission))->toOthers();

        return redirect()->route('judge.submissions.index')
            ->with('success', "Submission marked as <strong>{$request->verdict}</strong>.");
    }
}
