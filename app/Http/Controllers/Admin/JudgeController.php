<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class JudgeController extends Controller
{
    public function index()
    {
        $judges = User::where('role', 'judge')->paginate(15);
        return view('admin.judges.index', compact('judges'));
    }

    public function create()
    {
        return view('admin.judges.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'judge',
        ]);

        return redirect()->route('admin.judges.index')
            ->with('success', "Judge \"{$request->name}\" created successfully.");
    }

    public function destroy(User $judge)
    {
        abort_if($judge->role !== 'judge', 403);
        $name = $judge->name;
        $judge->delete();
        return redirect()->route('admin.judges.index')
            ->with('success', "Judge \"{$name}\" deleted.");
    }
}
