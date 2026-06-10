<!DOCTYPE html>
<html>
<head>
    <title>Akun Baru</title>
</head>
{{-- 🌟 BLOK NOTIFIKASI SUCCESS & ERROR --}}
@if(session('success'))
    <div class="p-4 mb-4 text-sm font-bold text-emerald-700 bg-emerald-100 rounded-xl border border-emerald-200">
        ✅ {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="p-4 mb-4 text-sm font-bold text-red-700 bg-red-100 rounded-xl border border-red-200">
        ❌ {{ session('error') }}
    </div>
@endif
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <div style="max-w-xl; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        <h2 style="color: #2563eb;">Halo, {{ $dataEmail['name'] }}!</h2>
        <p>Akun Anda telah berhasil dibuat oleh SPV sebagai <strong>{{ strtoupper($dataEmail['role']) }}</strong>.</p>
        
        <p>Berikut adalah informasi rahasia untuk login ke dalam sistem kami:</p>
        <div style="background: #f8fafc; padding: 15px; border-left: 4px solid #2563eb; margin: 15px 0;">
            <p style="margin: 0;"><strong>Email:</strong> {{ $dataEmail['email'] }}</p>
            <p style="margin: 0;"><strong>Password:</strong> {{ $dataEmail['password'] }}</p>
        </div>

        <p>Silakan klik tombol di bawah ini untuk mengakses sistem:</p>
        <a href="{{ url('/login') }}" style="display: inline-block; padding: 10px 20px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold;">Login Sekarang</a>

        <p style="margin-top: 20px; font-size: 12px; color: #666;">
            *Harap segera ubah password Anda di menu Edit Profil setelah berhasil login demi keamanan akun Anda.
        </p>
    </div>
</body>
</html>