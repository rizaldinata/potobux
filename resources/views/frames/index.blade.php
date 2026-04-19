@extends('layouts.app')
@section('title', 'Koleksi Bingkai — Potobux Studio')

@push('styles')
    <style>
        .frame-card {
            background: #190F16;
            border: 1px solid rgba(123, 31, 58, 0.2);
            transition: all 0.4s ease;
        }

        .frame-card:hover {
            border-color: rgba(201, 168, 76, 0.45);
            transform: translateY(-6px);
            box-shadow: 0 20px 50px rgba(123, 31, 58, 0.3);
        }

        .frame-card:hover .card-cta {
            opacity: 1;
        }

        .card-cta {
            opacity: 0;
            transition: opacity 0.3s;
            background: linear-gradient(to top, rgba(12, 10, 11, 0.97) 0%, rgba(12, 10, 11, 0.5) 60%, transparent 100%);
        }

        .section-line {
            width: 50px;
            height: 2px;
            background: linear-gradient(90deg, #9C2B4E, #C9A84C);
            border-radius: 2px;
        }

        .gold-text {
            background: linear-gradient(135deg, #E8C97A, #C9A84C, #A8813A);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .search-input {
            background: rgba(25, 15, 22, 0.8);
            border: 1px solid rgba(123, 31, 58, 0.3);
            color: #E8DFD8;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .search-input:focus {
            outline: none;
            border-color: rgba(156, 43, 78, 0.7);
            box-shadow: 0 0 0 3px rgba(123, 31, 58, 0.15);
        }

        .search-input::placeholder {
            color: #6B5B63;
        }

        .empty-state {
            background: rgba(25, 15, 22, 0.5);
            border: 1px dashed rgba(123, 31, 58, 0.3);
        }
    </style>
@endpush

@section('content')
    <div class="min-h-screen py-16 px-6" style="background:linear-gradient(180deg, #0C0A0B 0%, #151015 100%);">
        <div class="max-w-7xl mx-auto">

            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
                <div>
                    <div class="section-line mb-4"></div>
                    <h1 class="text-4xl md:text-5xl font-black text-white leading-tight">
                        Koleksi <span class="gold-text">Bingkai</span>
                    </h1>
                    <p class="text-gray-500 mt-3 text-sm">Semua bingkai dari komunitas Potobux — pilih favoritmu.</p>
                </div>
                <div class="flex gap-3">
                    <input type="text" id="search-input" placeholder="Cari bingkai..."
                        class="search-input px-4 py-2.5 rounded-xl text-sm w-48 md:w-64" oninput="filterFrames(this.value)">
                    @auth
                        <a href="{{ route('studio') }}"
                            class="px-5 py-2.5 rounded-xl font-bold text-sm text-white flex items-center gap-2 flex-shrink-0"
                            style="background:linear-gradient(135deg,#9C2B4E,#4D0E22);border:1px solid rgba(201,168,76,0.2);">
                            + Upload Bingkai
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Stats bar -->
            <div class="flex items-center gap-6 mb-10 pb-6" style="border-bottom:1px solid rgba(123,31,58,0.2);">
                <span class="text-gray-500 text-sm">
                    <span id="frame-count" class="font-bold text-white">{{ $frames->count() }}</span> bingkai tersedia
                </span>
                <span class="text-gray-600 text-xs">Diurutkan: Terbaru</span>
            </div>

            <!-- Grid -->
            @if ($frames->isEmpty())
                <div class="empty-state rounded-2xl py-24 text-center">
                    <div class="text-5xl mb-4 opacity-30">🖼</div>
                    <h3 class="text-xl font-bold text-gray-500 mb-2">Belum Ada Bingkai</h3>
                    <p class="text-gray-600 text-sm mb-6">Jadilah yang pertama upload bingkai aesthetic ke komunitas!</p>
                    @auth
                        <a href="{{ route('studio') }}" class="inline-block px-6 py-3 rounded-xl font-bold text-white text-sm"
                            style="background:linear-gradient(135deg,#9C2B4E,#4D0E22);">
                            Upload Bingkai Pertama
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-block px-6 py-3 rounded-xl font-bold text-white text-sm"
                            style="background:linear-gradient(135deg,#9C2B4E,#4D0E22);">
                            Daftar & Upload
                        </a>
                    @endauth
                </div>
            @else
                <div id="frames-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-5">
                    @foreach ($frames as $f)
                        <div class="frame-card rounded-2xl overflow-hidden group" data-name="{{ strtolower($f->name) }}">
                            <!-- Image -->
                            <div class="aspect-[3/4] relative overflow-hidden bg-gray-950">
                                <img src="{{ $f->image_path }}" alt="{{ $f->name }}"
                                    class="w-full h-full object-contain transition-transform duration-700 group-hover:scale-105">

                                <!-- CTA overlay -->
                                <div class="card-cta absolute inset-0 flex flex-col justify-end p-3">
                                    <a href="{{ route('studio') }}"
                                        class="w-full text-center py-2.5 rounded-xl font-bold text-xs text-white"
                                        style="background:linear-gradient(135deg,#9C2B4E,#4D0E22);border:1px solid rgba(201,168,76,0.15);">
                                        ✦ Gunakan di Studio
                                    </a>
                                </div>

                                <!-- Download badge -->
                                @if ($f->download_count > 0)
                                    <div class="absolute top-2.5 right-2.5">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                                            style="background:rgba(12,10,11,0.85);color:#C9A84C;border:1px solid rgba(201,168,76,0.25);">
                                            ↓ {{ $f->download_count }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Info -->
                            <div class="p-3.5">
                                <p class="font-bold text-white text-xs truncate mb-2">{{ $f->name }}</p>
                                <div class="flex items-center gap-2">
                                    <div class="w-5 h-5 rounded-full flex-shrink-0 flex items-center justify-center text-[9px] font-black text-white"
                                        style="background:linear-gradient(135deg,#9C2B4E,#4D0E22);">
                                        {{ strtoupper(substr($f->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <span
                                        class="text-gray-600 text-[10px] truncate">{{ $f->user->name ?? 'Anonim' }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- No results -->
                <div id="no-results" class="hidden text-center py-16 text-gray-600">
                    Tidak ada bingkai dengan nama tersebut.
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function filterFrames(query) {
            const q = query.toLowerCase().trim();
            const cards = document.querySelectorAll('#frames-grid .frame-card');
            let count = 0;
            cards.forEach(card => {
                const name = card.dataset.name || '';
                const show = !q || name.includes(q);
                card.style.display = show ? '' : 'none';
                if (show) count++;
            });
            document.getElementById('frame-count').innerText = count;
            const noResults = document.getElementById('no-results');
            if (noResults) noResults.classList.toggle('hidden', count > 0);
        }
    </script>
@endpush
