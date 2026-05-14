<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $team = auth()->user()->team;

        abort_if(!$team, 403, 'No team is linked to this account. Contact an administrator.');

        return view('team.dashboard');
    }
}
