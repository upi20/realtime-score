<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;

class DisplayController extends Controller
{
    public function index()
    {
        $matches = GameMatch::with('scores')->where('status', 'ongoing')->get();
        return view('display.index', compact('matches'));
    }
}
