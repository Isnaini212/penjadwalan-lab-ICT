<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): \Illuminate\Http\RedirectResponse
    {
        // 1. Cek email & password
        $request->authenticate();

        // 2. Bikin session aman
        $request->session()->regenerate();

        // 3. Cek siapa yang login
        $user = $request->user();

        // 🌟 4. ARAHIN LANGSUNG SESUAI JABATAN (TANPA MAMPIR DASHBOARD)
        if ($user->role === 'spv') {
            // SPV langsung ke dashboard admin
            return redirect()->route('spv.dashboard');
            
        } elseif ($user->role === 'ormawa') {
            // Ormawa langsung ke halaman bookingnya
            return redirect()->route('ormawa.booking.index');
            
        } elseif ($user->role === 'dosen') {
            // Dosen langsung ke halaman booking dosen (Sesuaikan nama route di web.php lu ya!)
            return redirect()->route('dosen.booking.index');}
        
        return redirect()->route('/');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
