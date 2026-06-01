<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Booking; 
use App\Models\AssistantSchedule;
use App\Models\Lab;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TvController extends Controller
{
    public function tvcuy()
        {
             $jadwal = Schedule::with('lab')
             ->whereHas('lab', function ($query) {
              $query->where('nama_lab', 'not like', '%RA%');
               })
             ->orderBy('jam_mulai', 'asc')
            ->get();

        return view('spv.tv', compact('jadwal'));
        }

}