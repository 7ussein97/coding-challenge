<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::orderBy('order')->paginate(20);
        return view('admin.questions.index', compact('questions'));
    }

    public function create()
    {
        $nextOrder = (Question::max('order') ?? 0) + 1;
        return view('admin.questions.create', compact('nextOrder'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'nullable|string|max:255',
            'description' => 'required|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,gif|max:4096',
            'order'       => 'required|integer|min:1',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('questions', 'public');
        }

        Question::create([
            'title'       => $request->title,
            'description' => $request->description,
            'image'       => $imagePath,
            'order'       => $request->order,
        ]);

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question created successfully.');
    }

    public function edit(Question $question)
    {
        return view('admin.questions.edit', compact('question'));
    }

    public function update(Request $request, Question $question)
    {
        $request->validate([
            'title'       => 'nullable|string|max:255',
            'description' => 'required|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,gif|max:4096',
            'order'       => 'required|integer|min:1',
        ]);

        $imagePath = $question->image;
        if ($request->hasFile('image')) {
            if ($imagePath) Storage::disk('public')->delete($imagePath);
            $imagePath = $request->file('image')->store('questions', 'public');
        }

        $question->update([
            'title'       => $request->title,
            'description' => $request->description,
            'image'       => $imagePath,
            'order'       => $request->order,
        ]);

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question updated successfully.');
    }

    public function destroy(Question $question)
    {
        if ($question->image) Storage::disk('public')->delete($question->image);
        $question->delete();
        return redirect()->route('admin.questions.index')
            ->with('success', 'Question deleted.');
    }
}
