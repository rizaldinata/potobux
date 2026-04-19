<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Potobux Studio</title>
    <meta name="description"
        content="Potobux Studio: self-photobooth online dengan bingkai aesthetic. Pilih bingkai, jepret foto, download seketika.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
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
            overflow-x: hidden;
        }

        h1,
        h2,
        h3 {
            font-family: 'Playfair Display', serif;
        }

        /* ── NAVBAR ── */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: rgba(12, 10, 11, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(123, 31, 58, 0.3);
        }

        /* ── BUTTONS ── */
        .btn-primary {
            background: linear-gradient(135deg, var(--burgundy-light), var(--burgundy-dark));
            color: #fff;
            border: 1px solid rgba(201, 168, 76, 0.3);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #B03258, #6D0E28);
            box-shadow: 0 0 24px rgba(123, 31, 58, 0.7);
            transform: translateY(-2px);
        }

        .btn-ghost {
            border: 1px solid rgba(201, 168, 76, 0.4);
            color: var(--gold);
            transition: all 0.3s;
        }

        .btn-ghost:hover {
            background: rgba(201, 168, 76, 0.1);
            border-color: var(--gold);
        }

        /* ── CARDS ── */
        .frame-card {
            background: var(--dark-card);
            border: 1px solid rgba(123, 31, 58, 0.2);
            transition: all 0.4s ease;
        }

        .frame-card:hover {
            border-color: rgba(201, 168, 76, 0.5);
            transform: translateY(-6px);
            box-shadow: 0 20px 50px rgba(123, 31, 58, 0.35);
        }

        .frame-card:hover .card-overlay {
            opacity: 1;
        }

        .card-overlay {
            opacity: 0;
            transition: opacity 0.3s;
            background: linear-gradient(to top, rgba(12, 10, 11, 0.95) 0%, rgba(12, 10, 11, 0.4) 60%, transparent 100%);
        }

        /* ── HERO ── */
        .hero-gradient {
            background: radial-gradient(ellipse 80% 60% at 50% -10%, rgba(123, 31, 58, 0.5) 0%, transparent 70%),
                radial-gradient(ellipse 40% 40% at 80% 80%, rgba(77, 14, 34, 0.3) 0%, transparent 60%),
                var(--dark-2);
        }

        /* ── FILM STRIP DECORATION ── */
        .film-holes::before,
        .film-holes::after {
            content: '';
            display: block;
            height: 100%;
            width: 28px;
            position: absolute;
            top: 0;
            background: repeating-linear-gradient(to bottom,
                    transparent 0px, transparent 8px,
                    rgba(255, 255, 255, 0.06) 8px, rgba(255, 255, 255, 0.06) 20px,
                    transparent 20px, transparent 30px);
        }

        .film-holes::before {
            left: 0;
        }

        .film-holes::after {
            right: 0;
        }

        /* ── SECTION DIVIDER ── */
        .section-line {
            width: 60px;
            height: 2px;
            background: linear-gradient(90deg, var(--burgundy-light), var(--gold));
            border-radius: 2px;
        }

        /* ── BADGE ── */
        .badge {
            background: rgba(123, 31, 58, 0.25);
            border: 1px solid rgba(123, 31, 58, 0.5);
            color: #D4758D;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        /* ── STEP CARDS ── */
        .step-card {
            background: linear-gradient(135deg, var(--dark-card), rgba(30, 21, 32, 0.8));
            border: 1px solid rgba(123, 31, 58, 0.2);
            transition: border-color 0.3s;
        }

        .step-card:hover {
            border-color: rgba(201, 168, 76, 0.4);
        }

        .step-number {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 900;
            color: rgba(123, 31, 58, 0.25);
            line-height: 1;
        }

        /* ── STAT CARDS ── */
        .stat-card {
            background: rgba(25, 15, 22, 0.8);
            border: 1px solid rgba(123, 31, 58, 0.25);
        }

        /* ── TESTIMONIAL ── */
        .testimonial-card {
            background: var(--dark-card);
            border: 1px solid rgba(123, 31, 58, 0.2);
        }

        .quote-mark {
            font-family: 'Playfair Display', serif;
            font-size: 5rem;
            line-height: 1;
            color: rgba(123, 31, 58, 0.3);
        }

        /* ── FOOTER ── */
        .footer-bg {
            background: linear-gradient(180deg, #0C0A0B 0%, #070507 100%);
            border-top: 1px solid rgba(123, 31, 58, 0.25);
        }

        /* ── SCROLLBAR ── */
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

        /* ── GOLD GRADIENT TEXT ── */
        .gold-text {
            background: linear-gradient(135deg, #E8C97A, #C9A84C, #A8813A);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ── CTA GLOW ── */
        .cta-glow {
            background: radial-gradient(ellipse 70% 50% at 50% 50%, rgba(123, 31, 58, 0.4) 0%, transparent 70%);
        }
    </style>
</head>

<body>

    <!-- ═══════════════════════════════════ NAVBAR ═══════════════════════════════════ -->
    <nav class="navbar px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5 group">
                <span class="text-2xl font-extrabold tracking-tight" style="font-family:'Playfair Display',serif;">
                    <span class="gold-text">Poto</span><span style="color:#E8DFD8;">bux.</span>
                </span>
            </a>
            <div class="flex items-center gap-6 text-sm font-semibold">
                <a href="{{ route('frames.index') }}" class="text-gray-400 hover:text-white transition">Koleksi</a>
                @auth
                    <a href="{{ route('studio') }}" class="btn-primary px-5 py-2.5 rounded-xl">
                        Masuk Studio
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button class="text-gray-500 hover:text-red-400 transition text-xs">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-400 hover:text-white transition">Login</a>
                    <a href="{{ route('studio') }}" class="btn-primary px-5 py-2.5 rounded-xl">Mulai Foto</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- ═══════════════════════════════════ HERO ═══════════════════════════════════ -->
    <section class="hero-gradient min-h-screen flex items-center justify-center pt-20 pb-24 relative overflow-hidden">

        <!-- Decorative film strip border -->
        <div class="absolute left-0 top-0 bottom-0 w-7 opacity-20 film-strip-left hidden lg:block"
            style="background:repeating-linear-gradient(to bottom,transparent 0,transparent 8px,rgba(255,255,255,.08) 8px,rgba(255,255,255,.08) 20px,transparent 20px,transparent 30px)">
        </div>
        <div class="absolute right-0 top-0 bottom-0 w-7 opacity-20 film-strip-right hidden lg:block"
            style="background:repeating-linear-gradient(to bottom,transparent 0,transparent 8px,rgba(255,255,255,.08) 8px,rgba(255,255,255,.08) 20px,transparent 20px,transparent 30px)">
        </div>

        <!-- Floating polaroid decorations -->
        <div class="absolute top-24 left-8 lg:left-24 opacity-15 rotate-[-15deg] pointer-events-none">
            <div class="w-20 h-24 bg-white rounded-sm shadow-2xl flex flex-col p-2">
                <div class="flex-1 bg-gray-300 rounded-sm mb-2"></div>
                <div class="h-3 w-12 bg-gray-200 mx-auto rounded"></div>
            </div>
        </div>
        <div class="absolute top-36 right-8 lg:right-32 opacity-15 rotate-[12deg] pointer-events-none">
            <div class="w-16 h-20 bg-white rounded-sm shadow-2xl flex flex-col p-1.5">
                <div class="flex-1 bg-gray-300 rounded-sm mb-1.5"></div>
                <div class="h-2 w-9 bg-gray-200 mx-auto rounded"></div>
            </div>
        </div>
        <div class="absolute bottom-24 left-12 lg:left-40 opacity-10 rotate-[8deg] pointer-events-none">
            <div class="w-14 h-18 bg-white rounded-sm shadow-2xl flex flex-col p-1.5" style="height:72px;">
                <div class="flex-1 bg-gray-300 rounded-sm mb-1.5"></div>
                <div class="h-2 w-8 bg-gray-200 mx-auto rounded"></div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
            <div class="badge inline-block px-4 py-2 rounded-full mb-8">✦ Self-Photobooth Digital</div>

            <h1 class="text-5xl md:text-7xl lg:text-8xl font-black leading-[0.9] tracking-tighter mb-8">
                <span class="block text-white">Abadikan</span>
                <span class="block gold-text">Momenmu</span>
                <span class="block text-white">Sendiri.</span>
            </h1>

            <p class="text-gray-400 text-lg md:text-xl max-w-2xl mx-auto mb-12 leading-relaxed font-light">
                Pilih bingkai aesthetic, aktifkan kamera, jepret foto ala <em>photobooth</em> — tanpa antri,
                tanpa keluar rumah. <span style="color:#D4758D;">Hasilnya langsung bisa kamu download.</span>
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('studio') }}"
                    class="btn-primary px-10 py-4 rounded-2xl font-bold text-lg inline-block">
                    ✦ Mulai Foto Sekarang
                </a>
                <a href="{{ route('frames.index') }}"
                    class="btn-ghost px-10 py-4 rounded-2xl font-semibold text-lg inline-block">
                    Lihat Koleksi Bingkai
                </a>
            </div>

            <!-- Stats -->
            <div class="mt-20 grid grid-cols-3 gap-6 max-w-lg mx-auto">
                <div class="text-center">
                    <div class="text-2xl font-black gold-text">{{ \App\Domain\Models\Frame::count() }}+</div>
                    <div class="text-gray-500 text-xs mt-1">Bingkai Tersedia</div>
                </div>
                <div class="text-center border-x" style="border-color:rgba(123,31,58,0.3)">
                    <div class="text-2xl font-black gold-text">{{ \App\Domain\Models\Frame::sum('download_count') }}+
                    </div>
                    <div class="text-gray-500 text-xs mt-1">Foto Didownload</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-black gold-text">{{ \App\Domain\Models\User::count() }}+</div>
                    <div class="text-gray-500 text-xs mt-1">Kreator Bingkai</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════ BINGKAI POPULER ══════════════════════════════ -->
    <section class="py-24 px-6" style="background: var(--dark-2);">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-end justify-between mb-12">
                <div>
                    <div class="section-line mb-4"></div>
                    <h2 class="text-4xl md:text-5xl font-black text-white leading-tight">
                        Bingkai <span class="gold-text">Populer</span>
                    </h2>
                    <p class="text-gray-500 mt-3 text-sm">Dipilih ribuan pengguna — terbukti aesthetic.</p>
                </div>
                <a href="{{ route('frames.index') }}"
                    class="hidden md:inline-flex btn-ghost px-5 py-2.5 rounded-xl text-sm font-semibold">
                    Lihat Semua →
                </a>
            </div>

            @if ($popular->isEmpty())
                <div class="text-center py-20 text-gray-600">Belum ada bingkai yang tersedia.</div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                    @foreach ($popular as $f)
                        <div class="frame-card rounded-2xl overflow-hidden relative group">
                            <!-- Frame Image -->
                            <div class="aspect-[3/4] relative overflow-hidden bg-gray-900">
                                <img src="{{ $f->image_path }}" alt="{{ $f->name }}"
                                    class="w-full h-full object-contain transition-transform duration-700 group-hover:scale-105">

                                <!-- Overlay with CTA -->
                                <div class="card-overlay absolute inset-0 flex flex-col justify-end p-4">
                                    <a href="{{ route('studio') }}"
                                        class="w-full text-center py-2.5 rounded-xl font-bold text-sm text-white"
                                        style="background:linear-gradient(135deg,#9C2B4E,#7B1F3A);">
                                        ✦ Coba Bingkai Ini
                                    </a>
                                </div>

                                <!-- Download badge -->
                                @if ($f->download_count > 0)
                                    <div class="absolute top-3 right-3">
                                        <span class="text-[10px] font-bold px-2 py-1 rounded-full"
                                            style="background:rgba(12,10,11,0.85);color:var(--gold);border:1px solid rgba(201,168,76,0.3);">
                                            ↓ {{ $f->download_count }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Card Info -->
                            <div class="p-4">
                                <p class="font-bold text-white truncate text-sm mb-2">{{ $f->name }}</p>
                                <div class="flex items-center gap-2">
                                    <!-- Avatar -->
                                    <div class="w-6 h-6 rounded-full flex-shrink-0 flex items-center justify-center text-[10px] font-black text-white"
                                        style="background:linear-gradient(135deg,#9C2B4E,#4D0E22);">
                                        {{ strtoupper(substr($f->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <span
                                        class="text-gray-500 text-xs truncate">{{ $f->user->name ?? 'Anonim' }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 text-center md:hidden">
                    <a href="{{ route('frames.index') }}"
                        class="btn-ghost px-6 py-2.5 rounded-xl text-sm font-semibold">
                        Lihat Semua Bingkai →
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- ════════════════════════════════ TERBARU ════════════════════════════════ -->
    <section class="py-24 px-6" style="background: var(--dark-3);">
        <div class="max-w-7xl mx-auto">
            <div class="mb-12">
                <div class="section-line mb-4"></div>
                <h2 class="text-4xl md:text-5xl font-black text-white">
                    Baru <span class="gold-text">Hadir</span>
                </h2>
                <p class="text-gray-500 mt-3 text-sm">Kreasi terbaru dari komunitas Potobux.</p>
            </div>

            @if ($recent->isEmpty())
                <div class="text-center py-20 text-gray-600">Belum ada bingkai baru.</div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                    @foreach ($recent as $f)
                        <div class="frame-card rounded-2xl overflow-hidden relative group">
                            <div class="aspect-[3/4] relative overflow-hidden bg-gray-900">
                                <img src="{{ $f->image_path }}" alt="{{ $f->name }}"
                                    class="w-full h-full object-contain transition-transform duration-700 group-hover:scale-105">

                                <div class="card-overlay absolute inset-0 flex flex-col justify-end p-4">
                                    <a href="{{ route('studio') }}"
                                        class="w-full text-center py-2.5 rounded-xl font-bold text-sm text-white"
                                        style="background:linear-gradient(135deg,#9C2B4E,#7B1F3A);">
                                        ✦ Pakai Ini
                                    </a>
                                </div>

                                <!-- New badge -->
                                <div class="absolute top-3 left-3">
                                    <span class="text-[10px] font-bold px-2 py-1 rounded-full"
                                        style="background:rgba(156,43,78,0.9);color:#fff;border:1px solid rgba(255,255,255,0.1);">
                                        NEW
                                    </span>
                                </div>
                            </div>

                            <div class="p-4">
                                <p class="font-bold text-white truncate text-sm mb-2">{{ $f->name }}</p>
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full flex-shrink-0 flex items-center justify-center text-[10px] font-black text-white"
                                        style="background:linear-gradient(135deg,#9C2B4E,#4D0E22);">
                                        {{ strtoupper(substr($f->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <span
                                        class="text-gray-500 text-xs truncate">{{ $f->user->name ?? 'Anonim' }}</span>
                                    <span class="text-gray-600 text-xs ml-auto flex-shrink-0">
                                        {{ $f->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- ══════════════════════════════ CARA KERJA ══════════════════════════════ -->
    <section class="py-24 px-6" style="background: var(--dark-2);">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-16">
                <div class="section-line mx-auto mb-4"></div>
                <h2 class="text-4xl md:text-5xl font-black text-white">
                    Cara <span class="gold-text">Kerjanya</span>
                </h2>
                <p class="text-gray-500 mt-4 max-w-xl mx-auto">Tiga langkah, hasilnya langsung siap bagikan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative">
                <!-- connector line on desktop -->
                <div class="hidden md:block absolute top-16 left-1/3 right-1/3 h-px"
                    style="background:linear-gradient(90deg,var(--burgundy-light),var(--gold),var(--burgundy-light));opacity:0.4;">
                </div>

                <!-- Step 1 -->
                <div class="step-card rounded-2xl p-8 text-center group">
                    <div class="step-number mb-2">01</div>
                    <div class="w-14 h-14 rounded-2xl mx-auto mb-5 flex items-center justify-center text-2xl"
                        style="background:rgba(123,31,58,0.2);border:1px solid rgba(123,31,58,0.4);">🖼</div>
                    <h3 class="text-xl font-black text-white mb-3">Pilih Bingkai</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Jelajahi koleksi bingkai dari komunitas. Pilih
                        yang paling cocok dengan vibe-mu hari ini.</p>
                </div>

                <!-- Step 2 -->
                <div class="step-card rounded-2xl p-8 text-center"
                    style="border-color:rgba(201,168,76,0.25);background:linear-gradient(135deg,rgba(25,15,22,0.8),rgba(50,15,25,0.5));">
                    <div class="step-number mb-2" style="color:rgba(201,168,76,0.3);">02</div>
                    <div class="w-14 h-14 rounded-2xl mx-auto mb-5 flex items-center justify-center text-2xl"
                        style="background:rgba(201,168,76,0.1);border:1px solid rgba(201,168,76,0.3);">📸</div>
                    <h3 class="text-xl font-black text-white mb-3">Jepret Foto</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Aktifkan kamera, pose sebanyak slot yang ada, dan
                        review setiap hasil foto sebelum lanjut.</p>
                </div>

                <!-- Step 3 -->
                <div class="step-card rounded-2xl p-8 text-center">
                    <div class="step-number mb-2">03</div>
                    <div class="w-14 h-14 rounded-2xl mx-auto mb-5 flex items-center justify-center text-2xl"
                        style="background:rgba(123,31,58,0.2);border:1px solid rgba(123,31,58,0.4);">✨</div>
                    <h3 class="text-xl font-black text-white mb-3">Download & Bagikan</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Foto finalmu langsung bisa didownload dalam
                        kualitas tinggi. Siap diposting ke Instagram atau BeReal!</p>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('studio') }}"
                    class="btn-primary px-10 py-4 rounded-2xl font-bold text-lg inline-block">
                    Coba Sekarang — Gratis ✦
                </a>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════ FITUR UNGGULAN ══════════════════════════════ -->
    <section class="py-24 px-6" style="background:linear-gradient(180deg, var(--dark-3) 0%, var(--dark) 100%);">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <div class="section-line mx-auto mb-4"></div>
                <h2 class="text-4xl md:text-5xl font-black text-white">
                    Kenapa <span class="gold-text">Potobux?</span>
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- Feature cards -->
                @php
                    $features = [
                        [
                            'icon' => '🎞',
                            'title' => 'Multi-Slot Bingkai',
                            'desc' => 'Bingkai dengan lebih dari satu slot foto — foto bareng teman dalam satu frame!',
                        ],
                        [
                            'icon' => '🔒',
                            'title' => 'Privasi Terjaga',
                            'desc' => 'Foto tidak disimpan di server kami. Semua diproses langsung di browser kamu.',
                        ],
                        [
                            'icon' => '⚡',
                            'title' => 'Instan & Ringan',
                            'desc' => 'Tanpa install aplikasi. Cukup buka browser, langsung jepret!',
                        ],
                        [
                            'icon' => '🎨',
                            'title' => 'Upload Bingkaimu',
                            'desc' => 'Punya desain sendiri? Upload bingkai PNG kamu dan bagikan ke komunitas.',
                        ],
                        [
                            'icon' => '📐',
                            'title' => 'Canvas Editor',
                            'desc' => 'Atur posisi, ukuran, dan rotasi slot kamera dengan editor visual intuitif.',
                        ],
                        [
                            'icon' => '💾',
                            'title' => 'Kualitas Tinggi',
                            'desc' => 'Hasil foto resolusi penuh, siap cetak atau langsung posting ke sosmed.',
                        ],
                    ];
                @endphp
                @foreach ($features as $f)
                    <div class="step-card rounded-2xl p-6 flex gap-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl flex-shrink-0"
                            style="background:rgba(123,31,58,0.15);border:1px solid rgba(123,31,58,0.3);">
                            {{ $f['icon'] }}
                        </div>
                        <div>
                            <h3 class="font-black text-white text-base mb-1">{{ $f['title'] }}</h3>
                            <p class="text-gray-500 text-sm leading-relaxed">{{ $f['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════ TESTIMONIAL ══════════════════════════════ -->
    <section class="py-24 px-6" style="background: var(--dark-2);">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <div class="section-line mx-auto mb-4"></div>
                <h2 class="text-4xl font-black text-white">
                    Kata <span class="gold-text">Mereka</span>
                </h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @php
                    $testimonials = [
                        [
                            'name' => 'Aurelia R.',
                            'handle' => '@aurel.feed',
                            'text' =>
                                '"Aku udah coba banyak apps photobooth, tapi Potobux literally the best. Hasilnya aesthetic banget dan bingkainya unik-unik!"',
                        ],
                        [
                            'name' => 'Bima K.',
                            'handle' => '@bima.clicks',
                            'text' =>
                                '"Gampang banget dipakenya. Buka browser, pilih bingkai favorit, langsung jepret. Gak perlu install apa-apa. 10/10!"',
                        ],
                        [
                            'name' => 'Nadira F.',
                            'handle' => '@nadira.life',
                            'text' =>
                                '"Suka banget fitur multi-slot-nya, bisa foto 4 pose dalam satu bingkai. Berasa kayak lagi di photobooth sungguhan!"',
                        ],
                    ];
                @endphp
                @foreach ($testimonials as $t)
                    <div class="testimonial-card rounded-2xl p-7 flex flex-col">
                        <div class="quote-mark leading-none mb-3">"</div>
                        <p class="text-gray-300 text-sm leading-relaxed flex-1 italic">{{ $t['text'] }}</p>
                        <div class="flex items-center gap-3 mt-6 pt-5"
                            style="border-top:1px solid rgba(123,31,58,0.2);">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center font-black text-sm"
                                style="background:linear-gradient(135deg,#9C2B4E,#4D0E22);color:#fff;">
                                {{ substr($t['name'], 0, 1) }}
                            </div>
                            <div>
                                <div class="font-bold text-white text-sm">{{ $t['name'] }}</div>
                                <div class="text-gray-600 text-xs">{{ $t['handle'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════ FINAL CTA ══════════════════════════════ -->
    <section class="py-28 px-6 relative overflow-hidden" style="background: var(--dark);">
        <div class="cta-glow absolute inset-0 pointer-events-none"></div>
        <div class="max-w-3xl mx-auto text-center relative z-10">
            <div class="section-line mx-auto mb-6"></div>
            <h2 class="text-5xl md:text-6xl font-black text-white leading-tight mb-6">
                Mulai Sesi <span class="gold-text">Foto</span><br>Pertamamu
            </h2>
            <p class="text-gray-500 mb-10 text-lg">Gratis. Tanpa install. Langsung bisa download.</p>
            <a href="{{ route('studio') }}"
                class="btn-primary px-12 py-5 rounded-2xl font-black text-xl inline-block"
                style="letter-spacing:-0.5px;">
                ✦ Buka Studio Sekarang
            </a>
            @guest
                <p class="text-gray-600 text-sm mt-5">
                    Belum punya akun?
                    <a href="{{ route('register') }}" style="color:#D4758D;"
                        class="hover:underline font-semibold">Daftar gratis di sini</a>
                    untuk bisa upload bingkaimu sendiri.
                </p>
            @endguest
        </div>
    </section>

    <!-- ═══════════════════════════════════ FOOTER ═══════════════════════════════════ -->
    <footer class="footer-bg pt-16 pb-8 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12"
                style="border-bottom:1px solid rgba(123,31,58,0.2);">
                <!-- Brand -->
                <div class="md:col-span-2">
                    <h3 class="text-2xl font-black mb-3" style="font-family:'Playfair Display',serif;">
                        <span class="gold-text">Poto</span><span class="text-white">bux.</span>
                    </h3>
                    <p class="text-gray-600 text-sm leading-relaxed max-w-xs">
                        Platform self-photobooth digital yang memungkinkan siapa pun mengabadikan momen dengan bingkai
                        aesthetic — tanpa kamera fisik, tanpa antri.
                    </p>
                    <div class="flex gap-4 mt-6">
                        <!-- Social icons -->
                        <a href="#"
                            class="w-9 h-9 rounded-lg flex items-center justify-center text-sm transition hover:scale-110"
                            style="background:rgba(123,31,58,0.2);border:1px solid rgba(123,31,58,0.3);color:#D4758D;">𝕀</a>
                        <a href="#"
                            class="w-9 h-9 rounded-lg flex items-center justify-center text-sm transition hover:scale-110"
                            style="background:rgba(123,31,58,0.2);border:1px solid rgba(123,31,58,0.3);color:#D4758D;">𝕏</a>
                        <a href="#"
                            class="w-9 h-9 rounded-lg flex items-center justify-center text-sm transition hover:scale-110"
                            style="background:rgba(123,31,58,0.2);border:1px solid rgba(123,31,58,0.3);color:#D4758D;">𝕋</a>
                    </div>
                </div>

                <!-- Menu -->
                <div>
                    <h4 class="font-black text-white text-sm mb-5 tracking-widest uppercase"
                        style="color:var(--gold);">Studio</h4>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li><a href="{{ route('studio') }}" class="hover:text-white transition">Buka Studio</a></li>
                        <li><a href="{{ route('frames.index') }}" class="hover:text-white transition">Koleksi
                                Bingkai</a></li>
                        @auth
                            <li><a href="{{ route('studio') }}" class="hover:text-white transition">Upload Bingkai</a>
                            </li>
                        @else
                            <li><a href="{{ route('register') }}" class="hover:text-white transition">Daftar Akun</a>
                            </li>
                        @endauth
                    </ul>
                </div>

                <!-- Info -->
                <div>
                    <h4 class="font-black text-sm mb-5 tracking-widest uppercase" style="color:var(--gold);">Info</h4>
                    <ul class="space-y-3 text-sm text-gray-600">
                        <li><span class="hover:text-white transition cursor-default">Tentang Kami</span></li>
                        <li><span class="hover:text-white transition cursor-default">Kebijakan Privasi</span></li>
                        <li><span class="hover:text-white transition cursor-default">Syarat & Ketentuan</span></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom bar -->
            <div class="pt-8 flex flex-col md:flex-row justify-between items-center gap-3 text-xs text-gray-700">
                <span>© {{ date('Y') }} Potobux Studio. All rights reserved.</span>
                <span>Dibuat dengan <span style="color:var(--burgundy-light);">♥</span> untuk para pecinta
                    self-photobooth.</span>
            </div>
        </div>
    </footer>

</body>

</html>
