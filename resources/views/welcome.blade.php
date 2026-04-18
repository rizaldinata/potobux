<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Potobux Studio</title>
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Fabric.js for Interactive Upload Editor -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>

    <style>
        .camera-container {
            position: relative;
            width: 100%;
            max-width: 600px;
            aspect-ratio: 3/4;
            margin: 0 auto;
            background-color: #000;
            overflow: hidden;
            border-radius: 12px;
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
            object-fit: fill; 
            pointer-events: none;
            z-index: 10;
        }
        .frame-item.active {
            border: 4px solid #3b82f6; 
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 font-sans antialiased min-h-screen flex flex-col">
    
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-blue-600">📷 Potobux Studio</h1>
            <div class="flex items-center gap-4">
                @auth
                    <span class="text-sm font-medium text-gray-700 hidden md:inline">Halo, {{ Auth::user()->name }}!</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm font-semibold text-red-500 hover:underline">Logout</button>
                    </form>
                    <button onclick="openUploadModal()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded font-semibold transition shadow-sm">Upload Bingkai +</button>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:underline">Login</a>
                    <a href="{{ route('login') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded font-semibold transition shadow-sm">Upload Bingkai +</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="flex-grow max-w-7xl mx-auto px-4 py-8 w-full flex flex-col md:flex-row gap-8">
        
        <!-- Left: Studio -->
        <div class="w-full md:w-2/3 flex flex-col items-center">
            <h2 id="capture-status" class="text-xl font-bold text-blue-600 mb-2 py-1 px-4 bg-blue-100 rounded-full shadow-inner">Pilih Bingkai & Bersiaplah!</h2>
            
            <div class="camera-container shadow-2xl ring-4 ring-white">
                <video id="webcam" autoplay playsinline></video>
                <img id="overlay-frame" src="" alt="" style="display:none;">
            </div>

            <div class="mt-6 flex flex-col items-center">
                <button id="capture-btn" class="bg-blue-600 hover:bg-blue-700 text-white rounded-full w-20 h-20 flex items-center justify-center shadow-lg transition transform hover:scale-105 hover:animate-pulse focus:outline-none">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </button>
            </div>
            
            <canvas id="hidden-canvas" style="display:none;"></canvas>
            
            <!-- Result Container -->
            <div id="result-container" class="mt-8 hidden flex flex-col items-center bg-white p-8 rounded-xl shadow-2xl w-full max-w-md border-t-8 border-green-500">
                <h3 class="text-2xl font-black mb-4 text-gray-800 text-center">Gambar Final Potobux! 🎉</h3>
                <img id="result-image" class="w-full h-auto rounded-lg shadow-md object-contain mb-6 border">
                <div class="flex flex-col sm:flex-row gap-4 w-full">
                    <a id="download-btn" class="flex-1 text-center bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg cursor-pointer font-bold transition shadow-lg text-lg">📥 Download Foto</a>
                    <button onclick="retake()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 py-3 rounded-lg font-bold transition shadow">📸 Ulangi Sesi</button>
                </div>
            </div>
        </div>

        <!-- Right: Frame Selection Carousel -->
        <div class="w-full md:w-1/3 bg-white p-6 rounded-xl shadow-lg h-fit border border-gray-100">
            <h2 class="text-xl font-bold border-b pb-3 mb-4 text-gray-800">Koleksi Bingkai Web</h2>
            <div id="frames-list" class="grid grid-cols-2 gap-4 max-h-[600px] overflow-y-auto pr-2 pb-4">
                <!-- Data populated here -->
            </div>
        </div>
    </main>

    <!-- Upload Modal -->
    <div id="upload-modal" class="fixed inset-0 bg-black/80 hidden z-50 overflow-y-auto backdrop-blur-sm">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl w-full max-w-4xl p-8 shadow-2xl flex flex-col md:flex-row gap-8">
                <!-- Form & Controls -->
                <div class="w-full md:w-1/3 flex flex-col gap-4">
                    <h3 class="text-2xl font-black text-gray-800 border-b pb-2">Desainer Bingkai</h3>
                    <form id="upload-form" class="flex flex-col gap-4 flex-grow">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Judul / Tema Bingkai</label>
                            <input type="text" id="frame-name" required class="w-full border-2 rounded-lg p-2 focus:border-blue-500 outline-none" placeholder="e.g. Frame Cetak Miring">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1 flex justify-between">
                                Pilih Gambar (PNG) 
                            </label>
                            <input type="file" id="frame-file" accept="image/png" onchange="previewUpload(this)" required class="w-full text-sm p-1 border rounded-lg bg-gray-50 cursor-pointer">
                            <p class="text-[10px] text-gray-500 mt-1">Pastikan berpori-pori/transparan di lubang tempat foto.</p>
                        </div>
                        
                        <div class="border-2 border-indigo-100 rounded-lg p-4 bg-indigo-50/30 mt-2 flex-grow">
                            <h4 class="font-extrabold text-sm text-indigo-900 mb-1">Peta Sensor Wajah</h4>
                            <p class="text-xs text-indigo-700/80 mb-4">Gunakan layar visual di samping untuk menarik, mengecilkan, dan memutar (rotasi) posisi kotak tangkapan kamera.</p>
                            
                            <button type="button" onclick="addSlot()" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white shadow-md font-bold py-2.5 rounded-lg mb-2 transition text-sm flex justify-center items-center gap-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Tambah Kotak Sensor
                            </button>
                            <button type="button" onclick="removeActiveSlot()" class="w-full text-red-500 hover:bg-red-50 font-semibold py-2 rounded border border-red-200 transition text-sm mt-1">Hapus Kotak Terpilih</button>
                        </div>
                        
                        <div class="mt-auto flex justify-end gap-3 pt-4 border-t">
                            <button type="button" onclick="closeUploadModal()" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg font-bold text-gray-600 transition">Batal</button>
                            <button type="submit" id="upload-submit" class="px-8 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow-lg transition">Simpan Cloud</button>
                        </div>
                    </form>
                </div>

                <!-- Preview Box with Fabric Canvas -->
                <div class="w-full md:w-2/3 flex flex-col items-center justify-center bg-gray-900 rounded-xl p-4 overflow-hidden shadow-inner relative">
                    <p class="text-sm font-bold text-indigo-300 mb-4 tracking-widest"><span class="animate-pulse mr-1">🔴</span> VISUAL CANVAS EDITOR</p>
                    
                    <div id="editor-wrapper" class="relative bg-white shadow-2xl rounded" style="width: 300px; height: 400px; background-image: repeating-linear-gradient(45deg, #e5e5e5 25%, transparent 25%, transparent 75%, #e5e5e5 75%, #e5e5e5), repeating-linear-gradient(45deg, #e5e5e5 25%, transparent 25%, transparent 75%, #e5e5e5 75%, #e5e5e5); background-position: 0 0, 10px 10px; background-size: 20px 20px;">
                        <canvas id="editor-canvas"></canvas>
                    </div>
                    
                    <div class="mt-6 flex justify-center text-xs text-gray-400 max-w-sm text-center">
                        Jika Anda mengeklik "Tambah Kotak Scanner", geser balok biru yang muncul di layar, lalu putar tangkainya menyesuaikan jajaran giring kemiringan *Polaroid* Anda.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN SCRIPT -->
    <script>
        const video = document.getElementById('webcam');
        const overlayFrame = document.getElementById('overlay-frame');
        const captureBtn = document.getElementById('capture-btn');
        const resultContainer = document.getElementById('result-container');
        const framesList = document.getElementById('frames-list');
        const statusHeader = document.getElementById('capture-status');
        
        let allFrames = [];
        let currentFrame = null;
        let multiTakeIndex = 0;
        let takenImages = []; 

        // ==========================================
        // 1. Core Studio Visualizer
        // ==========================================
        async function setupWebcam() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } } 
                });
                video.srcObject = stream;
            } catch (err) { alert("Izinkan akses kamera di browser Anda!"); }
        }

        async function loadFrames() {
            try {
                const res = await fetch('/api/frames');
                const json = await res.json();
                framesList.innerHTML = '';
                
                if (json.data && json.data.length > 0) {
                    allFrames = json.data;
                    allFrames.forEach((frame, idx) => {
                        const div = document.createElement('div');
                        // Tilted hover aesthetic
                        div.className = 'frame-item group cursor-pointer rounded-xl overflow-hidden border-4 border-transparent hover:border-blue-400 transition-all duration-300 shadow-md bg-white hover:shadow-xl hover:-translate-y-1';
                        div.innerHTML = `
                            <div class="bg-gray-100 relative pt-[100%] w-full">
                                <img src="${frame.image_path}" class="absolute inset-0 w-full h-full object-contain p-2 group-hover:scale-105 transition-transform duration-500">
                            </div>
                            <p class="text-center text-[11px] font-bold py-3 text-gray-800 whitespace-nowrap overflow-hidden text-ellipsis border-t px-2">${frame.name}</p>`;
                        
                        div.onclick = () => selectFrame(idx, div);
                        framesList.appendChild(div);
                    });
                } else {
                    framesList.innerHTML = '<p class="text-sm text-gray-500 col-span-2 text-center mt-4">Belum ada bingkai studio.</p>';
                }
            } catch (err) { console.error(err); }
        }

        function selectFrame(index, element) {
            document.querySelectorAll('.frame-item').forEach(el => el.classList.remove('active', 'ring-4', 'ring-blue-300'));
            element.classList.add('active', 'ring-4', 'ring-blue-300');
            
            currentFrame = allFrames[index];
            multiTakeIndex = 0; takenImages = [];
            
            overlayFrame.src = currentFrame.image_path;
            overlayFrame.style.display = 'block';
            
            // Periksa format Array json dari Backend (Laravel casting ensures that JSON converts to array safely)
            let slotCount = (currentFrame.slots && Array.isArray(currentFrame.slots) && currentFrame.slots.length > 0) 
                            ? currentFrame.slots.length : 1;
            statusHeader.innerText = `Pose 1 dari ${slotCount}`;
        }

        // ==========================================
        // 2. Burst Photography & Final Compilation
        // ==========================================
        captureBtn.addEventListener('click', () => {
            if (!video.srcObject) return alert("Kamera mati.");
            if (!currentFrame) return alert("Pilih bingkai web di menu kanan terlebih dahulu!");
            
            const cvs = document.createElement('canvas');
            cvs.width = video.videoWidth; cvs.height = video.videoHeight;
            const c = cvs.getContext('2d');
            c.translate(cvs.width, 0); c.scale(-1, 1);
            c.drawImage(video, 0, 0, cvs.width, cvs.height);
            
            takenImages.push(cvs.toDataURL('image/png', 0.9));
            
            let totalSlots = (currentFrame.slots && Array.isArray(currentFrame.slots) && currentFrame.slots.length > 0) ? currentFrame.slots.length : 1;
            multiTakeIndex++;
            
            // Screen Flash
            const flash = document.createElement('div');
            flash.className = 'absolute inset-0 bg-white z-[60] transition-opacity duration-500';
            video.parentElement.appendChild(flash);
            requestAnimationFrame(() => { flash.style.opacity = '0'; setTimeout(()=>flash.remove(), 500); });

            if (multiTakeIndex < totalSlots) {
                statusHeader.innerText = `Pose ${multiTakeIndex + 1} dari ${totalSlots}`;
            } else {
                statusHeader.innerText = `Menyusun Masterpiece...`;
                captureBtn.disabled = true;
                compileAndRenderResult();
            }
        });

        function compileAndRenderResult() {
             const cvs = document.getElementById('hidden-canvas');
             const W = 1500; const H = 2000;
             cvs.width = W; cvs.height = H;
             const ctx = cvs.getContext('2d');
             
             ctx.fillStyle = "#ffffff";
             ctx.fillRect(0,0,W,H);
             
             let slots = currentFrame.slots;
             // Validasi fallback
             if (!slots || !Array.isArray(slots) || slots.length === 0) {
                 slots = [{cx: 0.5, cy: 0.5, w: 1, h: 1, r: 0}]; 
             }
             
             let loadedCount = 0;
             slots.forEach((slot, idx) => {
                 const img = new Image();
                 img.src = takenImages[idx];
                 img.onload = () => {
                     let cx, cy, cw, ch, angle;
                     
                     // Handle both New Array Ratio Logic {cx, cy...} & Old logic percentage
                     if (slot.cx !== undefined) {
                         cx = slot.cx * W; cy = slot.cy * H;
                         cw = slot.w * W; ch = slot.h * H;
                         angle = slot.r || 0;
                     } else {
                         // Safe compatibility just in case there's old format {x, y, w, h} from 0-100
                         cw = (slot.w / 100) * W; ch = (slot.h / 100) * H;
                         cx = ((slot.x / 100) * W) + (cw / 2); cy = ((slot.y / 100) * H) + (ch / 2);
                         angle = 0;
                     }
                     
                     // Transformasi Rotasi
                     ctx.save();
                     ctx.translate(cx, cy);
                     ctx.rotate((angle * Math.PI) / 180);
                     
                     // Object-Fit: Cover Simulation on Canvas
                     const imgAspect = img.width / img.height;
                     const boxAspect = cw / ch;
                     let sx = 0, sy = 0, sw = img.width, sh = img.height;
                     
                     if (imgAspect > boxAspect) {
                         sw = img.height * boxAspect;
                         sx = (img.width - sw) / 2;
                     } else {
                         sh = img.width / boxAspect;
                         sy = (img.height - sh) / 2;
                     }
                     
                     // Gambar dimasukin persis di pusat poros (karena canvas ditarik / translate ke tengah)
                     ctx.drawImage(img, sx, sy, sw, sh, -cw/2, -ch/2, cw, ch);
                     ctx.restore();
                     
                     loadedCount++;
                     if (loadedCount === slots.length) { drawOverlayAndFinish(ctx, W, H); }
                 };
             });
        }
        
        function drawOverlayAndFinish(ctx, W, H) {
            const overlay = new Image();
            overlay.crossOrigin = "anonymous";
            overlay.src = currentFrame.image_path;
            
            overlay.onload = () => {
                ctx.drawImage(overlay, 0, 0, W, H);
                const dataURI = document.getElementById('hidden-canvas').toDataURL('image/png', 0.95);
                
                document.getElementById('result-image').src = dataURI;
                document.getElementById('download-btn').href = dataURI;
                document.getElementById('download-btn').download = `potobux-${Date.now()}.png`;
                
                document.getElementById('result-container').classList.remove('hidden');
                document.getElementById('result-container').scrollIntoView({ behavior: 'smooth' });
                
                statusHeader.innerText = `Mantap! Mau Foto Lagi?`;
                captureBtn.disabled = false;
            };
        }

        function retake() {
            document.getElementById('result-container').classList.add('hidden');
            multiTakeIndex = 0; takenImages = [];
            let totalSlots = (currentFrame && currentFrame.slots && Array.isArray(currentFrame.slots) && currentFrame.slots.length > 0) ? currentFrame.slots.length : 1;
            statusHeader.innerText = `Pose 1 dari ${totalSlots}`;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }


        // ==========================================
        // 3. INTERACTIVE UPLOAD MODAL & FABRIC.JS
        // ==========================================
        let editorCanvas = null;
        
        function openUploadModal() { document.getElementById('upload-modal').classList.remove('hidden'); }
        function closeUploadModal() { document.getElementById('upload-modal').classList.add('hidden'); }
        
        function initEditor() {
            if(!editorCanvas) {
                editorCanvas = new fabric.Canvas('editor-canvas', {
                    width: 300, height: 400,
                    selection: false
                });
            }
        }
        
        function previewUpload(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    initEditor();
                    editorCanvas.clear();
                    
                    fabric.Image.fromURL(e.target.result, function(img) {
                        const scale = Math.min(300 / img.width, 400 / img.height);
                        img.set({
                            originX: 'center', originY: 'center',
                            left: 150, top: 200,
                            scaleX: scale, scaleY: scale
                        });
                        editorCanvas.setOverlayImage(img, editorCanvas.renderAll.bind(editorCanvas));
                    });
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        function addSlot() {
            if(!editorCanvas) return alert('Pilih gambar bingkai (PNG) terlebih dahulu!');
            
            const rect = new fabric.Rect({
                left: 150, top: 200,
                fill: 'rgba(59, 130, 246, 0.45)', // Blue semi-transparent
                stroke: '#1e3a8a', strokeWidth: 2,
                width: 120, height: 160,
                originX: 'center', originY: 'center',
                cornerColor: '#2563eb', cornerStrokeColor: 'white',
                transparentCorners: false, cornerSize: 14,
                padding: 10, borderColor: '#1e40af'
            });
            
            editorCanvas.add(rect);
            editorCanvas.setActiveObject(rect);
        }

        function removeActiveSlot() {
            if(!editorCanvas) return;
            const activeObject = editorCanvas.getActiveObject();
            if(activeObject) { editorCanvas.remove(activeObject); }
        }

        function compressImage(file, maxW, maxH) {
            return new Promise((resolve) => {
                const img = new Image();
                img.src = URL.createObjectURL(file);
                img.onload = () => {
                    let w = img.width, h = img.height;
                    if (w > maxW || h > maxH) {
                        if (w > h) { h = h * (maxW / w); w = maxW; }
                        else { w = w * (maxH / h); h = maxH; }
                    } else {
                        return resolve(file);
                    }
                    const cvs = document.createElement('canvas');
                    cvs.width = w; cvs.height = h;
                    const ctx = cvs.getContext('2d');
                    ctx.drawImage(img, 0, 0, w, h);
                    cvs.toBlob(blob => {
                        resolve(new File([blob], file.name, {type: 'image/png'}));
                    }, 'image/png');
                };
                img.onerror = () => resolve(file);
            });
        }

        document.getElementById('upload-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const btn = document.getElementById('upload-submit');
            const ogText = btn.innerText;
            btn.innerText = 'Menyesuaikan Ukuran...'; btn.disabled = true;
            
            let uploadSlots = [];
            if(editorCanvas) {
                const rects = editorCanvas.getObjects('rect');
                if(rects.length > 0) {
                    rects.forEach(rect => {
                        uploadSlots.push({
                            cx: rect.left / 300,
                            cy: rect.top / 400,
                            w: (rect.width * rect.scaleX) / 300,
                            h: (rect.height * rect.scaleY) / 400,
                            r: rect.angle || 0
                        });
                    });
                }
            }
            
            let file = document.getElementById('frame-file').files[0];
            
            // Rahasia utama: Mengecilkan gambar yang melebihi 1.5MB tanpa menyentuh pengaturan rumit PHP asli.
            if (file && file.size > 1.5 * 1024 * 1024) {
                file = await compressImage(file, 1500, 2000);
            }
            
            btn.innerText = 'Menyimpan...';
            
            const formData = new FormData();
            formData.append('name', document.getElementById('frame-name').value);
            formData.append('file', file);
            if (uploadSlots.length > 0) {
                formData.append('slots', JSON.stringify(uploadSlots));
            }
            
            try {
                const res = await fetch('/api/frames', {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                if (res.ok) {
                    await res.json();
                    alert('Sukses! Bingkai Polaroid baru telah tersedia dan siap dipakai pengunjung.');
                    closeUploadModal();
                    document.getElementById('upload-form').reset();
                    if(editorCanvas) editorCanvas.clear();
                    loadFrames(); 
                } else {
                    if (res.status === 401) {
                        alert('Oops! Sesi Login Anda telah terputus di Server akibat Restart/Refresh Database. Halaman akan dimuat ulang sekarang.');
                        window.location.reload();
                    } else {
                        const error = await res.json(); 
                        alert(error.message || 'Gagal menyimpan bingkai. Periksa kembali file Anda.');
                    }
                }
            } catch (err) { console.error(err); alert('Gagal terhubung ke server.'); } 
            finally { btn.innerText = ogText; btn.disabled = false; }
        });

        // Start App
        setupWebcam(); loadFrames();
    </script>
</body>
</html>