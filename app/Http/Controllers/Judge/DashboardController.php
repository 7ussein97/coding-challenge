<?php

namespace App\Http\Controllers\Judge;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('judge.dashboard');
    }
}
