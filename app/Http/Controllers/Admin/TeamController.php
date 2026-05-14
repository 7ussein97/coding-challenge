<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::with('user')->paginate(15);
        return view('admin.teams.index', compact('teams'));
    }

    public function create()
    {
        return view('admin.teams.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255|unique:teams,name',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'team',
        ]);

        Team::create([
            'name'    => $request->name,
            'user_id' => $user->id,
        ]);

        return redirect()->route('admin.teams.index')
            ->with('success', "Team \"{$request->name}\" created successfully.");
    }

    public function destroy(Team $team)
    {
        $userName = $team->name;
        $team->user()->delete();
        return redirect()->route('admin.teams.index')
            ->with('success', "Team \"{$userName}\" deleted.");
    }
}
