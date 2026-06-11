<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AssistantSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use App\Mail\AkunBaruMail;

class AkunController extends Controller
{



    
    public function simpen(Request $request)
    {
        
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role'     => 'required|in:asisten,ormawa,dosen', 
        ]);

        $passwordAsli = $request->password;
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password), 
            'role'     => $request->role,
        ]);
        $dataEmail = [
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $passwordAsli, // Password belum dienkripsi
            'role'     => $request->role,
        ];
        try {
            Mail::to($request->email)->send(new AkunBaruMail($dataEmail));
        } catch (\Exception $e) {
            // Kalau misal gagal ngirim email (karena internet putus/salah setting gmail),
            // Akun tetap terbuat tapi keluar notif error emailnya
            return back()->with('error', 'Akun berhasil dibuat, TAPI email gagal dikirim. Pastikan settingan Gmail benar! Error: ' . $e->getMessage());
        }

        // Kalau sukses semua
        return back()->with('success', 'Akun ' . strtoupper($request->role) . ' atas nama ' . $request->name . ' berhasil dibuat & Email berisi password telah dikirim!');
    }

       

    
public function buat()
{
    $users = User::whereIn('role', ['asisten', 'ormawa', 'dosen'])->latest()->get();
    
    return view('spv.akun', compact('users'));
}

public function update(Request $request, User $user)
{
    if (! in_array($user->role, ['asisten', 'ormawa', 'dosen'], true)) {
        abort(403, 'Akun ini tidak dapat diubah melalui halaman manajemen akun.');
    }

    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => [
            'required',
            'string',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($user->id),
        ],
    ]);

    $namaLama = $user->name;

    $user->update([
        'name' => $validated['name'],
        'email' => $validated['email'],
    ]);

    if ($user->role === 'asisten' && $namaLama !== $validated['name']) {
        AssistantSchedule::where('nama_asisten', $namaLama)
            ->update(['nama_asisten' => $validated['name']]);
    }

    return back()->with('success', 'Data akun ' . $validated['name'] . ' berhasil diperbarui.');
}
}
