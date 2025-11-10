<?php

namespace App\Http\Controllers;

use App\Models\MovingQuote;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {   
        $quotes = MovingQuote::all();

        return view('dashboard', compact('quotes'));
    }
}
