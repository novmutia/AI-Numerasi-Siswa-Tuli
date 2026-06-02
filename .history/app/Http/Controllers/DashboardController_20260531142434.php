<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_siswa'     => 0,
            'total_sekolah'   => 0,
            'asesmen_selesai' => 0,
            'rata_skor'       => '0%',
        ];

        return view('dashboard', compact('stats'));
    }
}
