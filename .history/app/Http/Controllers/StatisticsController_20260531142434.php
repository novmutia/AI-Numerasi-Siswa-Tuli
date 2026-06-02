<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class StatisticsController extends Controller
{
    public function index()
    {
        return redirect()->route('dashboard')
            ->with('error', 'Halaman Statistik sedang dalam pengembangan.');
    }
}
