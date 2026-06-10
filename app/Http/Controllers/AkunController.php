<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
}