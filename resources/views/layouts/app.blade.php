<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Potobux Studio')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --burgundy: #7B1F3A;
            --burgundy-light: #9C2B4E;
            --burgundy-dark: #4D0E22;
            --gold: #C9A84C;
            --cream: #F5EDD8;
            --dark: #0C0A0B;
            --dark-2: #151015;
            --dark-3: #1E1520;
            --dark-card: #190F16;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--dark);
            color: #E8DFD8;
            min-height: 100vh;
        }

        h1,
        h2,
        h3,
        .font-serif {
            font-family: 'Playfair Display', serif;
        }

        /* Navbar */
        .app-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: rgba(12, 10, 11, 0.9);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(123, 31, 58, 0.3);
        }

        .gold-text {
            background: linear-gradient(135deg, #E8C97A, #C9A84C, #A8813A);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--burgundy-light), var(--burgundy-dark));
            color: #fff;
            border: 1px solid rgba(201, 168, 76, 0.25);
            transition: all 0.3s;
            display: inline-block;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #B03258, #6D0E28);
            box-shadow: 0 0 20px rgba(123, 31, 58, 0.6);
            transform: translateY(-1px);
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
    @stack('styles')
</head>

<body>
    <!-- NAVBAR -->
    <nav class="app-navbar px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="{{ route('landing') }}" class="flex items-center gap-2">
                <span class="text-2xl font-black tracking-tight" style="font-family:'Playfair Display',serif;">
                    <span class="gold-text">Poto</span><span style="color:#E8DFD8;">bux.</span>
                </span>
            </a>
            <div class="flex items-center gap-6 text-sm font-semibold">
                <a href="{{ route('frames.index') }}"
                    class="{{ request()->routeIs('frames.index') ? 'text-white' : 'text-gray-500 hover:text-white' }} transition">
                    Koleksi
                </a>
                @auth
                    <a href="{{ route('studio') }}"
                        class="{{ request()->routeIs('studio') ? 'text-white' : 'text-gray-500 hover:text-white' }} transition">
                        Studio
                    </a>
                    <span class="text-gray-600 text-xs hidden md:inline">{{ Auth::user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button class="text-gray-600 hover:text-red-400 text-xs transition">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-500 hover:text-white transition">Login</a>
                    <a href="{{ route('studio') }}" class="btn-primary px-5 py-2.5 rounded-xl">Mulai Foto</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- CONTENT -->
    <div class="pt-16">
        @yield('content')
    </div>

    @stack('scripts')
</body>

</html>
