<?php
namespace App\Http\Controllers;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TvController extends Controller
{

public function manageTv()
{
    $announcement = DB::table('pengunguman')->first();
    $slides = DB::table('slide_tv')->get();

    return view('spv.tv', compact('announcement', 'slides'));
}


    public function updateTvText(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'schedule_delay' => 'required|integer|min:3|max:300'
        ]);

        $exists = DB::table('pengunguman')->first();

        if ($exists) {
            DB::table('pengunguman')->where('id', $exists->id)->update([
                'message' => $request->message,
                'schedule_delay' => $request->schedule_delay,
                'updated_at' => now()
            ]);
        } else {
            DB::table('pengunguman')->insert([
                'message' => $request->message,
                'schedule_delay' => $request->schedule_delay,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return redirect()->back()->with('success', 'Teks agenda & durasi jadwal TV berhasil diperbarui.');
    }


    public function uploadTvSlide(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png|max:3072',
            'delay' => 'nullable|integer|min:3|max:300'
        ]);

        $delay = $request->input('delay', 15) ?: 15;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('slide_tv', 'public');

            DB::table('slide_tv')->insert([
                'image_path' => $path,
                'delay' => $delay,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return redirect()->back()->with('success', 'Slide gambar baru berhasil ditambahkan.');
    }

    public function updateTvSlideDelay(Request $request, $id)
    {
        $request->validate([
            'delay' => 'required|integer|min:3|max:300'
        ]);

        DB::table('slide_tv')->where('id', $id)->update([
            'delay' => $request->delay,
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Delay slide berhasil diperbarui.');
    }


public function deleteTvSlide($id)
{
    $slide = DB::table('slide_tv')->where('id', $id)->first();

    if ($slide) {
        Storage::disk('public')->delete($slide->image_path);
        DB::table('slide_tv')->where('id', $id)->delete();
    }

    return redirect()->back()->with('success', 'Slide gambar berhasil dihapus.');
}


    public function tvSon()
    {
        $jadwal = Schedule::with('lab')
            ->whereDate('tanggal', now()->toDateString())
            ->whereHas('lab', function ($query) {
                $query->where('nama_lab', 'not like', '%RA%');
            })
            ->orderBy('jam_mulai', 'asc')
            ->get();

        $announcement = DB::table('pengunguman')->first();
        $runningText = $announcement ? $announcement->message : 'Selamat Datang di Laboratorium Komputer Universitas Budi Luhur.';
        $scheduleDelay = $announcement ? ($announcement->schedule_delay ?? 15) : 15;
        
        $slides = DB::table('slide_tv')->get();

        return view('tv', compact('jadwal', 'runningText', 'slides', 'scheduleDelay'));
    }
}