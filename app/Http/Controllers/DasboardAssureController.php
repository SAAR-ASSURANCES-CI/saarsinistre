<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DasboardAssureController extends Controller
{
    public function index()
    {
        return view('assures.dashboard');
    }
}
