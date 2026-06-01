<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Booking; 
use App\Models\Lab;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class TvController extends Controller
{
    public function tvSon()
{
    // Mengambil data jadwal khusus hari ini saja dan mengabaikan Ruang RA
    $jadwal = Schedule::with('lab')
        ->whereDate('tanggal', now()->toDateString())
        ->whereHas('lab', function ($query) {
            $query->where('nama_lab', 'not like', '%RA%');
        })
        ->orderBy('jam_mulai', 'asc')
        ->get();

    return view('tv', compact('jadwal'));
}

}