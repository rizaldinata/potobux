<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun — Potobux Studio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --burgundy: #7B1F3A;
            --burgundy-light: #9C2B4E;
            --burgundy-dark: #4D0E22;
            --gold: #C9A84C;
            --dark: #0C0A0B;
            --dark-2: #151015;
            --dark-card: #190F16;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--dark-2);
        }

        .gold-text {
            background: linear-gradient(135deg, #E8C97A, #C9A84C, #A8813A);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .auth-card {
            background: var(--dark-card);
            border: 1px solid rgba(123, 31, 58, 0.3);
        }

        .auth-input {
            background: rgba(12, 10, 11, 0.6);
            border: 1px solid rgba(123, 31, 58, 0.3);
            color: #E8DFD8;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .auth-input:focus {
            outline: none;
            border-color: rgba(156, 43, 78, 0.7);
            box-shadow: 0 0 0 3px rgba(123, 31, 58, 0.2);
        }

        .auth-input::placeholder {
            color: #6B5B63;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--burgundy-light), var(--burgundy-dark));
            color: #fff;
            border: 1px solid rgba(201, 168, 76, 0.2);
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #B03258, #6D0E28);
            box-shadow: 0 0 20px rgba(123, 31, 58, 0.5);
        }

        .hero-glow {
            background: radial-gradient(ellipse 80% 60% at 50% 20%, rgba(123, 31, 58, 0.3) 0%, transparent 70%);
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--dark);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--burgundy);
            border-radius: 3px;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center py-10 relative overflow-hidden">

    <div class="hero-glow absolute inset-0 pointer-events-none"></div>
    <div class="absolute inset-0 pointer-events-none opacity-5"
        style="background-image:repeating-linear-gradient(0deg,transparent,transparent 40px,rgba(255,255,255,0.03) 40px,rgba(255,255,255,0.03) 41px),repeating-linear-gradient(90deg,transparent,transparent 40px,rgba(255,255,255,0.03) 40px,rgba(255,255,255,0.03) 41px);">
    </div>

    <div class="w-full max-w-md px-6 relative z-10">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="{{ route('landing') }}" class="text-3xl font-black tracking-tight"
                style="font-family:'Playfair Display',serif;">
                <span class="gold-text">Poto</span><span style="color:#E8DFD8;">bux.</span>
            </a>
            <p class="text-gray-600 text-sm mt-2">Self-Photobooth Digital</p>
        </div>

        <!-- Card -->
        <div class="auth-card rounded-2xl p-8">
            <h2 class="text-2xl font-black text-white mb-1" style="font-family:'Playfair Display',serif;">
                Buat Akunmu
            </h2>
            <p class="text-gray-500 text-sm mb-7">Bergabung dengan komunitas Potobux — gratis!</p>

            @if ($errors->any())
                <div class="mb-5 px-4 py-3 rounded-xl text-sm font-medium"
                    style="background:rgba(123,31,58,0.2);border:1px solid rgba(156,43,78,0.4);color:#D4758D;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold mb-2 tracking-widest uppercase"
                        style="color:var(--gold);">Nama Lengkap</label>
                    <input type="text" name="name" required class="auth-input w-full rounded-xl px-4 py-3 text-sm"
                        placeholder="Nama kamu" value="{{ old('name') }}">
                </div>
                <div>
                    <label class="block text-xs font-bold mb-2 tracking-widest uppercase"
                        style="color:var(--gold);">Email</label>
                    <input type="email" name="email" required class="auth-input w-full rounded-xl px-4 py-3 text-sm"
                        placeholder="email@kamu.com" value="{{ old('email') }}">
                </div>
                <div>
                    <label class="block text-xs font-bold mb-2 tracking-widest uppercase"
                        style="color:var(--gold);">Password</label>
                    <input type="password" name="password" required minlength="8"
                        class="auth-input w-full rounded-xl px-4 py-3 text-sm" placeholder="Min. 8 karakter">
                </div>
                <div>
                    <label class="block text-xs font-bold mb-2 tracking-widest uppercase"
                        style="color:var(--gold);">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required minlength="8"
                        class="auth-input w-full rounded-xl px-4 py-3 text-sm" placeholder="Ulangi password">
                </div>
                <button type="submit" class="btn-primary w-full py-3.5 rounded-xl font-bold text-sm mt-2">
                    Daftar Sekarang →
                </button>
            </form>

            <div class="mt-6 pt-6 flex flex-col gap-2 text-center text-sm"
                style="border-top:1px solid rgba(123,31,58,0.2);">
                <p class="text-gray-600">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" style="color:#D4758D;" class="font-bold hover:underline">Login di
                        sini</a>
                </p>
                <a href="{{ route('landing') }}" class="text-gray-600 hover:text-white transition text-xs">
                    ← Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</body>

</html>
