<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

    
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password), 
            'role'     => $request->role,
        ]);

        return back()->with('success', 'Akun ' . strtoupper($request->role) . ' atas nama ' . $request->name . ' berhasil dibuat!');
    }

    
public function buat()
{
    $users = User::whereIn('role', ['asisten', 'ormawa', 'dosen'])->latest()->get();
    
    return view('spv.akun', compact('users'));
}
}