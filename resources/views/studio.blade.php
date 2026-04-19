@extends('layouts.app')

@push('styles')
    <style>
        .camera-container {
            position: relative;
            width: 100%;
            max-width: 560px;
            aspect-ratio: 3/4;
            background: #111;
            border-radius: 1.5rem;
            overflow: hidden;
        }

        #webcam {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }

        #overlay-frame {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 10;
        }

        .frame-item.active {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.4);
        }
    </style>
@endpush

@section('content')
    <div class="min-h-screen py-4 px-4" style="background:linear-gradient(180deg,#0C0A0B 0%,#151015 100%);">
        <main class="max-w-7xl mx-auto flex flex-col md:flex-row gap-6">

            {{-- LEFT: Camera & Controls --}}
            <div class="w-full md:w-2/3 flex flex-col items-center">
                <div class="flex items-center justify-between w-full max-w-lg mb-4">
                    <h2 id="capture-status" class="text-sm font-bold py-1.5 px-5 rounded-full"
                        style="background:rgba(123,31,58,0.15);border:1px solid rgba(123,31,58,0.35);color:#D4758D;">
                        Pilih Bingkai di Panel Kanan
                    </h2>
                    @auth
                        <button onclick="openUploadModal()"
                            class="text-white px-4 py-2 rounded-xl font-semibold text-xs shadow transition"
                            style="background:linear-gradient(135deg,#9C2B4E,#4D0E22);border:1px solid rgba(201,168,76,0.2);">
                            + Upload Bingkai
                        </button>
                    @endauth
                </div>

                <div class="camera-container shadow-2xl" style="box-shadow:0 25px 60px rgba(123,31,58,0.25);">
                    <video id="webcam" autoplay playsinline class="hidden"></video>
                    <img id="overlay-frame" src="" alt="" class="hidden">
                    <canvas id="live-studio-canvas" class="w-full h-full absolute inset-0"></canvas>
                </div>

                <div class="mt-6 flex flex-col items-center w-full max-w-xs">
                    <div class="flex items-center gap-6 mb-4">
                        <button id="prev-slot-btn" onclick="goToPreviousSlot()"
                            class="hidden bg-gray-100 hover:bg-gray-200 p-4 rounded-full shadow transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                        </button>

                        <button id="capture-btn"
                            class="bg-blue-600 hover:bg-blue-700 w-20 h-20 rounded-full flex items-center justify-center text-white shadow-xl hover:scale-105 transition">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </button>
                    </div>

                    <div id="review-controls" class="hidden w-full flex gap-4">
                        <button onclick="retakeCurrentSlot()"
                            class="flex-1 py-3 text-red-600 font-bold border-2 border-red-200 rounded-2xl hover:bg-red-50 transition">Ulangi
                            Pose</button>
                        <button onclick="acceptCurrentSlot()"
                            class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl shadow-lg transition flex items-center justify-center gap-2">
                            Bagus, Lanjut!
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Result Container --}}
                <div id="result-container"
                    class="mt-10 hidden w-full max-w-lg bg-white p-8 rounded-[2rem] shadow-2xl border border-gray-100 text-center">
                    <h3 class="text-2xl font-black mb-6">Hasil Potobux! 🎉</h3>
                    <img id="result-image" class="w-full rounded-xl mb-6 shadow-md">
                    <div class="flex gap-4">
                        <a id="download-btn"
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white py-4 rounded-2xl font-bold shadow-lg cursor-pointer transition">
                            ⬇ Download Foto
                        </a>
                        <button onclick="retake()"
                            class="flex-1 bg-gray-100 hover:bg-gray-200 py-4 rounded-2xl font-bold transition">
                            🔄 Sesi Baru
                        </button>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Frames List --}}
            <div class="w-full md:w-1/3 rounded-2xl h-fit"
                style="background:#190F16;border:1px solid rgba(123,31,58,0.25);">
                <div class="p-5 border-b" style="border-color:rgba(123,31,58,0.2);">
                    <h3 class="font-black text-white text-base" style="font-family:'Playfair Display',serif;">Pilih Bingkai
                    </h3>
                    <p class="text-xs mt-1" style="color:#6B5B63;">Klik bingkai untuk mulai</p>
                </div>
                <div class="p-3">
                    <div id="frames-list" class="grid grid-cols-2 gap-3 max-h-[60vh] overflow-y-auto pr-1 pb-2"></div>
                </div>
            </div>
        </main>
    </div>

    {{-- Hidden canvas for final render --}}
    <canvas id="hidden-canvas" style="display:none;"></canvas>

    {{-- Upload Modal (auth users only) --}}
    @auth
        <div id="upload-modal" class="fixed inset-0 bg-black/80 hidden z-50 overflow-y-auto backdrop-blur-sm">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-2xl w-full max-w-4xl p-8 shadow-2xl flex flex-col md:flex-row gap-8">
                    {{-- Form --}}
                    <div class="w-full md:w-1/3 flex flex-col gap-4">
                        <h3 class="text-2xl font-black text-gray-800 border-b pb-2">Desainer Bingkai</h3>
                        <form id="upload-form" class="flex flex-col gap-4 flex-grow">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Judul / Tema Bingkai</label>
                                <input type="text" id="frame-name" required
                                    class="w-full border-2 rounded-lg p-2 focus:border-blue-500 outline-none"
                                    placeholder="e.g. Frame Cetak Miring">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Pilih Gambar (PNG)</label>
                                <input type="file" id="frame-file" accept="image/png" onchange="previewUpload(this)" required
                                    class="w-full text-sm p-1 border rounded-lg bg-gray-50 cursor-pointer">
                                <p class="text-[10px] text-gray-500 mt-1">PNG transparan di lubang tempat foto.</p>
                            </div>

                            <div class="border-2 border-indigo-100 rounded-lg p-4 bg-indigo-50/30 flex-grow">
                                <h4 class="font-extrabold text-sm text-indigo-900 mb-1">Peta Sensor Wajah</h4>
                                <p class="text-xs text-indigo-700/80 mb-4">Tarik, kecilkan, dan putar kotak tangkapan kamera di
                                    editor sebelah.</p>
                                <button type="button" onclick="addSlot()"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white shadow-md font-bold py-2.5 rounded-lg mb-2 transition text-sm flex justify-center items-center gap-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tambah Kotak Sensor
                                </button>
                                <button type="button" onclick="removeActiveSlot()"
                                    class="w-full text-red-500 hover:bg-red-50 font-semibold py-2 rounded border border-red-200 transition text-sm">
                                    Hapus Kotak Terpilih
                                </button>
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t">
                                <button type="button" onclick="closeUploadModal()"
                                    class="px-5 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600 transition">Batal</button>
                                <button type="submit" id="upload-submit"
                                    class="px-8 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-lg transition">Simpan
                                    Cloud</button>
                            </div>
                        </form>
                    </div>

                    {{-- Canvas Editor --}}
                    <div
                        class="w-full md:w-2/3 flex flex-col items-center justify-center bg-gray-900 rounded-xl p-4 shadow-inner relative">
                        <p class="text-sm font-bold text-indigo-300 mb-4 tracking-widest">
                            <span class="animate-pulse mr-1">🔵</span> VISUAL CANVAS EDITOR
                        </p>
                        <div id="editor-wrapper"
                            class="relative bg-white shadow-2xl rounded flex items-center justify-center overflow-hidden"
                            style="width:320px;height:400px;background-image:repeating-linear-gradient(45deg,#e5e5e5 25%,transparent 25%,transparent 75%,#e5e5e5 75%,#e5e5e5),repeating-linear-gradient(45deg,#e5e5e5 25%,transparent 25%,transparent 75%,#e5e5e5 75%,#e5e5e5);background-position:0 0,10px 10px;background-size:20px 20px;">
                            <canvas id="editor-canvas"></canvas>
                        </div>
                        <p class="mt-4 text-xs text-gray-400 max-w-xs text-center">
                            Klik "Tambah Kotak Scanner", lalu geser dan putar balok biru menyesuaikan lubang bingkai Anda.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endauth
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    <script>
        // ============================================================
        // REFERENCES
        // ============================================================
        const video = document.getElementById('webcam');
        const overlayFrame = document.getElementById('overlay-frame');
        const captureBtn = document.getElementById('capture-btn');
        const prevSlotBtn = document.getElementById('prev-slot-btn');
        const reviewControls = document.getElementById('review-controls');
        const resultContainer = document.getElementById('result-container');
        const framesList = document.getElementById('frames-list');
        const statusHeader = document.getElementById('capture-status');
        const liveCanvas = document.getElementById('live-studio-canvas');
        const liveCtx = liveCanvas ? liveCanvas.getContext('2d') : null;

        let allFrames = [];
        let currentFrame = null;
        let currentFrameId = null;
        let multiTakeIndex = 0;
        let takenImages = [];
        let isLiveCanvasRendering = false;

        // ============================================================
        // 1. WEBCAM SETUP
        // ============================================================
        async function setupWebcam() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user',
                        width: {
                            ideal: 1280
                        },
                        height: {
                            ideal: 720
                        }
                    }
                });
                video.srcObject = stream;
            } catch (err) {
                console.warn('Camera error:', err);
                statusHeader.innerText = '⚠ Aktifkan izin kamera di browser!';
            }
        }

        // ============================================================
        // 2. LOAD FRAMES FROM API
        // ============================================================
        async function loadFrames() {
            try {
                const res = await fetch('/api/frames');
                const json = await res.json();
                framesList.innerHTML = '';

                if (json.data && json.data.length > 0) {
                    allFrames = json.data;
                    allFrames.forEach((frame, idx) => {
                        const div = document.createElement('div');
                        div.className =
                            'frame-item group cursor-pointer rounded-xl overflow-hidden border-4 border-transparent hover:border-blue-400 transition-all duration-300 shadow-md bg-white hover:shadow-xl hover:-translate-y-1';
                        div.innerHTML =
                            `
                            <div class="bg-gray-100 relative pt-[100%] w-full">
                                <img src="${frame.image_path}" class="absolute inset-0 w-full h-full object-contain p-2 group-hover:scale-105 transition-transform duration-500">
                            </div>
                            <p class="text-center text-[11px] font-bold py-2 text-gray-800 whitespace-nowrap overflow-hidden text-ellipsis border-t px-2">${frame.name}</p>`;
                        div.onclick = () => selectFrame(idx, div);
                        framesList.appendChild(div);
                    });
                } else {
                    framesList.innerHTML =
                        '<p class="text-sm text-gray-400 col-span-2 text-center mt-6">Belum ada bingkai. Upload yang pertama!</p>';
                }
            } catch (err) {
                console.error('Load frames error:', err);
            }
        }

        // ============================================================
        // 3. SELECT FRAME
        // ============================================================
        function selectFrame(index, element) {
            document.querySelectorAll('.frame-item').forEach(el => el.classList.remove('active'));
            element.classList.add('active');

            currentFrame = allFrames[index];
            currentFrameId = currentFrame.id;
            multiTakeIndex = 0;
            takenImages = [];

            captureBtn.classList.remove('hidden');
            reviewControls.classList.add('hidden');
            resultContainer.classList.add('hidden');

            overlayFrame.onload = () => {
                if (overlayFrame.naturalWidth > 0) {
                    const ratio = overlayFrame.naturalWidth / overlayFrame.naturalHeight;
                    document.querySelector('.camera-container').style.aspectRatio = ratio;
                }
            };
            overlayFrame.src = currentFrame.image_path;

            updateCaptureUI();

            if (!isLiveCanvasRendering) {
                isLiveCanvasRendering = true;
                requestAnimationFrame(renderLiveStudio);
            }
        }

        // ============================================================
        // 4. STATUS UI
        // ============================================================
        function updateCaptureUI() {
            const totalSlots = (currentFrame && Array.isArray(currentFrame.slots) && currentFrame.slots.length > 0) ?
                currentFrame.slots.length : 1;
            prevSlotBtn.classList.toggle('hidden', multiTakeIndex === 0);
            statusHeader.innerText = `Pose ${multiTakeIndex + 1} dari ${totalSlots}`;
        }

        // ============================================================
        // 5. LIVE CANVAS RENDER
        // ============================================================
        function drawCoverImage(ctx, source, cw, ch) {
            const sourceW = source.videoWidth || source.width;
            const sourceH = source.videoHeight || source.height;
            if (!sourceW || !sourceH) return;

            const sourceAspect = sourceW / sourceH;
            const boxAspect = cw / ch;
            let sx = 0,
                sy = 0,
                sw = sourceW,
                sh = sourceH;

            if (sourceAspect > boxAspect) {
                sw = sh * boxAspect;
                sx = (sourceW - sw) / 2;
            } else {
                sh = sw / boxAspect;
                sy = (sourceH - sh) / 2;
            }

            if (sw > 0 && sh > 0) ctx.drawImage(source, sx, sy, sw, sh, -cw / 2, -ch / 2, cw, ch);
        }

        function renderLiveStudio() {
            if (!currentFrame || !isLiveCanvasRendering) return;

            const SW = overlayFrame.naturalWidth || 1500;
            const SH = overlayFrame.naturalHeight || 2000;
            if (SW === 0 || SH === 0) {
                requestAnimationFrame(renderLiveStudio);
                return;
            }

            if (liveCanvas.width !== SW || liveCanvas.height !== SH) {
                liveCanvas.width = SW;
                liveCanvas.height = SH;
            }

            liveCtx.clearRect(0, 0, SW, SH);
            liveCtx.fillStyle = '#f3f4f6';
            liveCtx.fillRect(0, 0, SW, SH);

            let slots = currentFrame.slots;
            if (!slots || !Array.isArray(slots) || slots.length === 0) {
                slots = [{
                    cx: 0.5,
                    cy: 0.5,
                    w: 1,
                    h: 1,
                    r: 0
                }];
            }

            slots.forEach((slot, idx) => {
                let cx, cy, cw, ch, angle;
                if (slot.cx !== undefined) {
                    cx = slot.cx * SW;
                    cy = slot.cy * SH;
                    cw = slot.w * SW;
                    ch = slot.h * SH;
                    angle = slot.r || 0;
                } else {
                    cw = (slot.w / 100) * SW;
                    ch = (slot.h / 100) * SH;
                    cx = ((slot.x / 100) * SW) + (cw / 2);
                    cy = ((slot.y / 100) * SH) + (ch / 2);
                    angle = 0;
                }

                liveCtx.save();
                liveCtx.translate(cx, cy);
                liveCtx.rotate((angle * Math.PI) / 180);

                if (takenImages[idx]) {
                    const photo = new Image();
                    photo.src = takenImages[idx];
                    if (photo.complete && photo.naturalHeight > 0) drawCoverImage(liveCtx, photo, cw, ch);
                } else if (idx === multiTakeIndex && video.readyState >= 2) {
                    liveCtx.scale(-1, 1);
                    drawCoverImage(liveCtx, video, cw, ch);
                } else {
                    liveCtx.fillStyle = '#e5e7eb';
                    liveCtx.fillRect(-cw / 2, -ch / 2, cw, ch);
                }
                liveCtx.restore();
            });

            if (overlayFrame.complete && overlayFrame.naturalHeight !== 0) {
                liveCtx.drawImage(overlayFrame, 0, 0, SW, SH);
            }

            if (isLiveCanvasRendering) requestAnimationFrame(renderLiveStudio);
        }

        // ============================================================
        // 6. CAPTURE FLOW
        // ============================================================
        captureBtn.addEventListener('click', () => {
            if (!video.srcObject) return alert('Kamera mati. Izinkan akses kamera!');
            if (!currentFrame) return alert('Pilih bingkai di panel kanan terlebih dahulu!');

            const cvs = document.createElement('canvas');
            cvs.width = video.videoWidth;
            cvs.height = video.videoHeight;
            const c = cvs.getContext('2d');
            c.translate(cvs.width, 0);
            c.scale(-1, 1);
            c.drawImage(video, 0, 0);

            takenImages[multiTakeIndex] = cvs.toDataURL('image/png', 0.9);

            // Flash effect
            const flash = document.createElement('div');
            flash.style.cssText = 'position:absolute;inset:0;background:#fff;z-index:60;transition:opacity 0.5s';
            video.parentElement.appendChild(flash);
            requestAnimationFrame(() => {
                flash.style.opacity = '0';
                setTimeout(() => flash.remove(), 500);
            });

            captureBtn.classList.add('hidden');
            prevSlotBtn.classList.add('hidden');
            reviewControls.classList.remove('hidden');
            statusHeader.innerText = 'Cek Pose Ini — Memuaskan?';
        });

        function retakeCurrentSlot() {
            takenImages[multiTakeIndex] = null;
            reviewControls.classList.add('hidden');
            captureBtn.classList.remove('hidden');
            updateCaptureUI();
        }

        function goToPreviousSlot() {
            if (multiTakeIndex > 0) {
                multiTakeIndex--;
                takenImages[multiTakeIndex] = null;
                reviewControls.classList.add('hidden');
                captureBtn.classList.remove('hidden');
                updateCaptureUI();
            }
        }

        function acceptCurrentSlot() {
            multiTakeIndex++;
            const totalSlots = (currentFrame.slots && Array.isArray(currentFrame.slots) && currentFrame.slots.length > 0) ?
                currentFrame.slots.length : 1;

            reviewControls.classList.add('hidden');

            if (multiTakeIndex < totalSlots) {
                captureBtn.classList.remove('hidden');
                updateCaptureUI();
            } else {
                statusHeader.innerText = 'Menyusun Masterpiece...';
                captureBtn.classList.add('hidden');
                prevSlotBtn.classList.add('hidden');
                compileAndRenderResult();
            }
        }

        // ============================================================
        // 7. COMPILE FINAL IMAGE
        // ============================================================
        function compileAndRenderResult() {
            isLiveCanvasRendering = false;
            const cvs = document.getElementById('hidden-canvas');
            const W = overlayFrame.naturalWidth || 1500;
            const H = overlayFrame.naturalHeight || 2000;
            cvs.width = W;
            cvs.height = H;
            const ctx = cvs.getContext('2d');

            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, W, H);

            let slots = currentFrame.slots;
            if (!slots || !Array.isArray(slots) || slots.length === 0) {
                slots = [{
                    cx: 0.5,
                    cy: 0.5,
                    w: 1,
                    h: 1,
                    r: 0
                }];
            }

            let loadedCount = 0;
            slots.forEach((slot, idx) => {
                const img = new Image();
                img.src = takenImages[idx];
                img.onload = () => {
                    let cx, cy, cw, ch, angle;
                    if (slot.cx !== undefined) {
                        cx = slot.cx * W;
                        cy = slot.cy * H;
                        cw = slot.w * W;
                        ch = slot.h * H;
                        angle = slot.r || 0;
                    } else {
                        cw = (slot.w / 100) * W;
                        ch = (slot.h / 100) * H;
                        cx = ((slot.x / 100) * W) + (cw / 2);
                        cy = ((slot.y / 100) * H) + (ch / 2);
                        angle = 0;
                    }

                    ctx.save();
                    ctx.translate(cx, cy);
                    ctx.rotate((angle * Math.PI) / 180);

                    const imgAspect = img.width / img.height;
                    const boxAspect = cw / ch;
                    let sx = 0,
                        sy = 0,
                        sw = img.width,
                        sh = img.height;
                    if (imgAspect > boxAspect) {
                        sw = img.height * boxAspect;
                        sx = (img.width - sw) / 2;
                    } else {
                        sh = img.width / boxAspect;
                        sy = (img.height - sh) / 2;
                    }

                    ctx.drawImage(img, sx, sy, sw, sh, -cw / 2, -ch / 2, cw, ch);
                    ctx.restore();

                    loadedCount++;
                    if (loadedCount === slots.length) drawOverlayAndFinish(ctx, W, H);
                };
            });
        }

        function drawOverlayAndFinish(ctx, W, H) {
            const overlay = new Image();
            overlay.crossOrigin = 'anonymous';
            overlay.src = currentFrame.image_path;
            overlay.onload = () => {
                ctx.drawImage(overlay, 0, 0, W, H);
                const dataURI = document.getElementById('hidden-canvas').toDataURL('image/png', 0.95);

                document.getElementById('result-image').src = dataURI;

                const dl = document.getElementById('download-btn');
                dl.setAttribute('href', dataURI);
                dl.setAttribute('download', `potobux-${Date.now()}.png`);

                resultContainer.classList.remove('hidden');
                resultContainer.scrollIntoView({
                    behavior: 'smooth'
                });
                statusHeader.innerText = 'Mantap! Mau Foto Lagi? 🎉';
            };
        }

        // ============================================================
        // 8. DOWNLOAD — increment download_count in backend
        // ============================================================
        document.getElementById('download-btn').addEventListener('click', async function() {
            if (!currentFrameId) return;
            try {
                await fetch(`/api/frames/${currentFrameId}/download`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
            } catch (e) {
                /* silent fail — don't block user from downloading */
            }
        });

        // ============================================================
        // 9. RETAKE SESSION
        // ============================================================
        function retake() {
            resultContainer.classList.add('hidden');
            multiTakeIndex = 0;
            takenImages = [];

            captureBtn.classList.remove('hidden');
            reviewControls.classList.add('hidden');
            updateCaptureUI();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });

            if (!isLiveCanvasRendering) {
                isLiveCanvasRendering = true;
                requestAnimationFrame(renderLiveStudio);
            }
        }

        // ============================================================
        // 10. UPLOAD MODAL & FABRIC EDITOR
        // ============================================================
        let editorCanvas = null;

        function openUploadModal() {
            document.getElementById('upload-modal').classList.remove('hidden');
        }

        function closeUploadModal() {
            document.getElementById('upload-modal').classList.add('hidden');
        }

        function initEditor() {
            if (!editorCanvas) {
                editorCanvas = new fabric.Canvas('editor-canvas', {
                    width: 320,
                    height: 400,
                    selection: false
                });
            }
        }

        function previewUpload(input) {
            if (!input.files || !input.files[0]) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                initEditor();
                editorCanvas.clear();
                fabric.Image.fromURL(e.target.result, function(img) {
                    const MAX_W = 320,
                        MAX_H = 400;
                    const scale = Math.min(MAX_W / img.width, MAX_H / img.height);
                    const actualW = img.width * scale,
                        actualH = img.height * scale;

                    editorCanvas.setWidth(actualW);
                    editorCanvas.setHeight(actualH);
                    img.set({
                        originX: 'center',
                        originY: 'center',
                        left: actualW / 2,
                        top: actualH / 2,
                        scaleX: scale,
                        scaleY: scale
                    });
                    editorCanvas.setOverlayImage(img, editorCanvas.renderAll.bind(editorCanvas));
                    editorCanvas.actualW = actualW;
                    editorCanvas.actualH = actualH;
                });
            };
            reader.readAsDataURL(input.files[0]);
        }

        function addSlot() {
            if (!editorCanvas) return alert('Pilih gambar bingkai (PNG) terlebih dahulu!');
            const cw = editorCanvas.actualW || 320,
                ch = editorCanvas.actualH || 400;
            const rect = new fabric.Rect({
                left: cw / 2,
                top: ch / 2,
                fill: 'rgba(59,130,246,0.45)',
                stroke: '#1e3a8a',
                strokeWidth: 2,
                width: 120,
                height: 160,
                originX: 'center',
                originY: 'center',
                cornerColor: '#2563eb',
                cornerStrokeColor: 'white',
                transparentCorners: false,
                cornerSize: 14,
                padding: 10,
                borderColor: '#1e40af'
            });
            editorCanvas.add(rect);
            editorCanvas.setActiveObject(rect);
        }

        function removeActiveSlot() {
            if (!editorCanvas) return;
            const active = editorCanvas.getActiveObject();
            if (active) editorCanvas.remove(active);
        }

        function compressImage(file, maxW, maxH) {
            return new Promise((resolve) => {
                const img = new Image();
                img.src = URL.createObjectURL(file);
                img.onload = () => {
                    let w = img.width,
                        h = img.height;
                    if (w <= maxW && h <= maxH) return resolve(file);
                    if (w > h) {
                        h = h * (maxW / w);
                        w = maxW;
                    } else {
                        w = w * (maxH / h);
                        h = maxH;
                    }
                    const cvs = document.createElement('canvas');
                    cvs.width = w;
                    cvs.height = h;
                    cvs.getContext('2d').drawImage(img, 0, 0, w, h);
                    cvs.toBlob(blob => resolve(new File([blob], file.name, {
                        type: 'image/png'
                    })), 'image/png');
                };
                img.onerror = () => resolve(file);
            });
        }

        document.getElementById('upload-form') && document.getElementById('upload-form').addEventListener('submit', async (
            e) => {
            e.preventDefault();
            const btn = document.getElementById('upload-submit');
            const ogText = btn.innerText;
            btn.innerText = 'Menyesuaikan Ukuran...';
            btn.disabled = true;

            let uploadSlots = [];
            if (editorCanvas) {
                const rects = editorCanvas.getObjects('rect');
                rects.forEach(rect => {
                    const cw = editorCanvas.actualW || 320,
                        ch = editorCanvas.actualH || 400;
                    uploadSlots.push({
                        cx: rect.left / cw,
                        cy: rect.top / ch,
                        w: (rect.width * rect.scaleX) / cw,
                        h: (rect.height * rect.scaleY) / ch,
                        r: rect.angle || 0
                    });
                });
            }

            let file = document.getElementById('frame-file').files[0];
            if (file && file.size > 1.5 * 1024 * 1024) {
                file = await compressImage(file, 1500, 2000);
            }
            btn.innerText = 'Menyimpan...';

            const formData = new FormData();
            formData.append('name', document.getElementById('frame-name').value);
            formData.append('file', file);
            if (uploadSlots.length > 0) formData.append('slots', JSON.stringify(uploadSlots));

            try {
                const res = await fetch('/api/frames', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (res.ok) {
                    alert('Sukses! Bingkai baru telah tersedia.');
                    closeUploadModal();
                    document.getElementById('upload-form').reset();
                    if (editorCanvas) editorCanvas.clear();
                    loadFrames();
                } else if (res.status === 401) {
                    alert('Sesi login habis, silakan login ulang.');
                    window.location.href = '/login';
                } else {
                    const error = await res.json();
                    alert(error.message || 'Gagal menyimpan bingkai.');
                }
            } catch (err) {
                alert('Gagal terhubung ke server.');
            } finally {
                btn.innerText = ogText;
                btn.disabled = false;
            }
        });

        // ============================================================
        // INIT
        // ============================================================
        setupWebcam();
        loadFrames();
    </script>
@endpush
