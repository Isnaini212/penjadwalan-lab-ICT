<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AkunController extends Controller
{

    public function buat()
    {
        return view('spv.akun');
    }

    // Proses pembuatan akun oleh SPV
    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role'     => 'required|in:asisten,ormawa,dosen', // SPV gabisa bikin akun SPV lain (aman)
        ]);

        // 2. Simpan ke database
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password), // Password dienkripsi
            'role'     => $request->role,
        ]);

        return back()->with('success', 'Akun ' . strtoupper($request->role) . ' atas nama ' . $request->name . ' berhasil dibuat!');
    }
}