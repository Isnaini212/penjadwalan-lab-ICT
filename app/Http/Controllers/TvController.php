<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
//use App\Models\Booking;
use App\Models\Lab;
use Illuminate\Support\Facades\Log;

class TvController extends Controller
{
    public function tvSon()
        {
             $jadwal = Schedule::with('lab')
             ->whereHas('lab', function ($query) {
              $query->where('nama_lab', 'not like', '%RA%');
               })
             ->orderBy('jam_mulai', 'asc')
            ->get();


        return view('tv', compact('jadwal'));
        }


}
