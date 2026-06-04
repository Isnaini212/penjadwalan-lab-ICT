<?php
namespace App\Http\Controllers;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TvController extends Controller
{
// Halaman Manajemen Konten TV untuk SPV
public function manageTv()
{
    $announcement = DB::table('pengunguman')->first();
    $slides = DB::table('slide_tv')->get();

    return view('spv.tv', compact('announcement', 'slides'));
}

// Menyimpan atau memperbarui running text
public function updateTvText(Request $request)
{
    $request->validate(['message' => 'required|string']);

    $exists = DB::table('pengunguman')->first();

    if ($exists) {
        DB::table('pengunguman')->where('id', $exists->id)->update([
            'message' => $request->message,
            'updated_at' => now()
        ]);
    } else {
        DB::table('pengunguman')->insert([
            'message' => $request->message,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    return redirect()->back()->with('success', 'Teks agenda TV berhasil diperbarui.');
}

// Menambah gambar slide baru (Otomatis menambah jumlah slide di TV)
public function uploadTvSlide(Request $request)
{
    $request->validate([
        'image' => 'required|image|mimes:jpeg,jpg,png|max:3072'
    ]);

    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('slide_tv', 'public');

        DB::table('slide_tv')->insert([
            'image_path' => $path,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    return redirect()->back()->with('success', 'Slide gambar baru berhasil ditambahkan.');
}

// Menghapus gambar slide (Otomatis mengurangi jumlah slide di TV)
public function deleteTvSlide($id)
{
    $slide = DB::table('slide_tv')->where('id', $id)->first();

    if ($slide) {
        Storage::disk('public')->delete($slide->image_path);
        DB::table('slide_tv')->where('id', $id)->delete();
    }

    return redirect()->back()->with('success', 'Slide gambar berhasil dihapus.');
}

// UPDATE FUNGSI TV LAMA: Sekarang melempar data jadwal, teks, dan gambar sekaligus
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
    
    $slides = DB::table('slide_tv')->get();

    return view('tv', compact('jadwal', 'runningText', 'slides'));
}}