<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kamu Dapat Kejutan!</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            /*overflow: hidden; Mencegah scroll di body saat balon fiesta aktif */
        }
        /* Tambahan CSS untuk Balon */
        #balloonFiesta {
            position: fixed; /* Mengisi seluruh layar */
            top: 0;
            left: 0;
            width: 100vw; /* Lebar viewport */
            height: 100vh; /* Tinggi viewport */
            background-color: #e0f7fa; /* Warna latar belakang untuk fiesta, bisa diubah */
            z-index: 900; /* Di atas konten lain, di bawah pop-up card */
            display: flex; /* Untuk centering judul jika mau */
            flex-direction: column;
            justify-content: flex-start; /* Judul di atas */
            align-items: center;
            padding-top: 20px; /* Padding untuk judul */
            box-sizing: border-box;
            overflow: hidden; /* Penting agar balon tidak keluar */
        }
        #balloonFiesta h2 { /* Styling judul di fiesta */
            color: #00796b;
            font-size: 1.5em; /* Ukuran judul bisa disesuaikan */
            margin-bottom: 10px; /* Jarak dari judul ke area balon */
            text-align: center;
            position: relative; /* Agar z-index bisa diterapkan jika perlu */
            z-index: 10; /* Pastikan judul di atas balon */
        }
        .balloon-wrapper { /* Wrapper untuk setiap balon dan nomornya */
            position: absolute;
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            /* Hapus transisi dari wrapper, kita terapkan di children */
            /* opacity akan dikontrol JavaScript untuk fade-in */
            opacity: 0; 
            transform: scale(0.5); /* Awalnya kecil untuk animasi masuk */
        }
        .balloon-wrapper:hover .balloon-img { /* Efek hover pada gambar balonnya */
            transform: scale(1.1);
        }
        .balloon-img {
            width: 105px; 
            height: auto;
            display: block;
            transition: transform 0.2s cubic-bezier(0.68, -0.55, 0.27, 1.55);
        }
        .balloon-number {
            font-size: 22px; /* Sesuaikan dengan ukuran balon baru */
            font-weight: bold;
            color: white;
            background-color: rgba(0, 0, 0, 0.6);
            padding: 3px 8px;
            border-radius: 50%;
            margin-top: -20px; /* Sesuaikan agar pas di balon yang lebih besar */
            position: relative;
            z-index: 1;
            user-select: none;
            transition: opacity 0.2s ease-out;
        }
        .balloon-wrapper.popped { /* Wrapper yang dianimasikan saat pecah */
            transition: transform 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55), opacity 0.3s 0.05s linear !important;
            transform: scale(1.6) rotate(15deg) !important; /* Efek membesar dan miring */
            opacity: 0 !important;
            pointer-events: none; /* Supaya tidak bisa diklik lagi setelah pop */
        }
        .balloon-wrapper.popped .balloon-img { /* Efek pecah pada gambar */
            transform: scale(1.8) rotate(20deg) !important;
            opacity: 0 !important;
        }
        .balloon-wrapper.popped .balloon-number { /* Sembunyikan nomor saat pecah */
            opacity: 0 !important;
        }
        .balloon-wrapper.disabled { /* Untuk balon yang belum boleh diklik */
            cursor: not-allowed;
            /* Jangan grayscale, cukup opacity agar nomor masih terbaca jelas */
            opacity: 0.3 !important; /* Opacity untuk disabled, pastikan tidak konflik dengan fade-in */
        }
        .balloon {
            width: 60px; /* Ukuran balon bisa sedikit lebih kecil agar muat banyak */
            height: auto;
            position: absolute;
            cursor: pointer;
            /* Transisi untuk efek pecah. Pisahkan transform dan opacity */
            transition: transform 0.2s cubic-bezier(0.68, -0.55, 0.27, 1.55), opacity 0.3s 0.05s linear; /* Opacity delay sedikit */
            will-change: transform, opacity; /* Optimasi performa untuk animasi */
        }
        .balloon:hover {
            transform: scale(1.1);
        }
        .popped {
            /* Efek pecah: membesar, sedikit berputar, lalu menghilang */
            transform: scale(1.8) rotate(20deg);
            opacity: 0 !important; /* Paksa opacity jadi 0 */
        }

        /* Style untuk Pop-up Card Ucapan */
        #birthdayCardPopup {
            display: none; 
            position: fixed; 
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%) scale(0.7); 
            background-color: white;
            padding: 25px; /* Sedikit kurangi padding default */
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
            z-index: 1000;
            transition: transform 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55), opacity 0.3s;
            opacity: 0;
            width: 80vw; /* Lebar awal, akan disesuaikan untuk desktop */
            max-width: 450px; /* Batas lebar maksimum untuk desktop agar tidak terlalu lebar */
            box-sizing: border-box; /* Penting agar padding tidak menambah total width */
        }
        #birthdayCardPopup.visible {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }
        #birthdayCardPopup h2 {
            color: #e84393;
            margin-top: 0; /* Hapus margin atas jika ada */
            margin-bottom: 15px;
            font-size: 1.8em; /* Ukuran font bisa disesuaikan di media query */
        }
        #birthdayCardPopup p {
            margin-bottom: 20px;
            font-size: 1em; /* Ukuran font bisa disesuaikan di media query */
            line-height: 1.5; /* Perbaiki keterbacaan */
        }
        #birthdayCardPopup img.birthday-photo {
            max-width: 120px; /* Ukuran foto bisa disesuaikan */
            border-radius: 50%;
            margin-bottom: 15px;
            border: 3px solid #e84393;
        }
        #openMainGiftButton { /* Tombol di dalam pop-up card */
            /* Menggunakan style dari .button atau style.css utama, tapi bisa di-override */
            padding: 10px 15px; /* Sesuaikan padding tombol */
            font-size: 0.95em; /* Sesuaikan font tombol */
            background-color: #f0f0f0; /* Warna tombol yang lebih netral */
            color: #333;
            border: 1px solid #ccc;
            border-radius: 8px; /* Radius lebih besar */
            cursor: pointer;
            transition: background-color 0.2s;
        }
        #openMainGiftButton:hover {
            background-color: #e0e0e0;
        }
        #giftContentSection {
            /* Setelah balon selesai, kita kembalikan styling container */
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            /*width: 100%;
            max-width: 600px;*/
            margin: 30px auto; /* Pusatkan kembali */
            display: none; /* Awalnya disembunyikan */
            box-sizing: border-box;
        }

        /* Sembunyikan .container asli saat fiesta balon aktif */
        body.balloon-fiesta-active .container-main-content {
            display: none; !important;
        }
        /* Media Query untuk layar yang lebih kecil (misalnya, mobile) */
@media (max-width: 600px) {
    #birthdayCardPopup {
        padding: 20px; /* Kurangi padding di mobile */
        width: 90vw; /* Buat lebih lebar di mobile */
        /* max-width: none; */ /* Hapus max-width agar bisa full 90vw */
    }

    #birthdayCardPopup h2 {
        font-size: 1.5em; /* Kecilkan font judul */
        margin-bottom: 10px;
    }

    #birthdayCardPopup p {
        font-size: 0.9em; /* Kecilkan font paragraf */
        margin-bottom: 15px;
    }

    #birthdayCardPopup img.birthday-photo {
        max-width: 100px; /* Kecilkan foto */
    }

    #openMainGiftButton {
        padding: 12px 15px; /* Buat tombol sedikit lebih besar agar mudah di-tap */
        font-size: 1em;
        width: 100%; /* Tombol full width di mobile */
        box-sizing: border-box;
    }
    #giftContentSection {
        margin: 20px 10px; /* Sesuaikan margin untuk mobile */
        padding: 15px;
    }
}

/* Media query untuk layar yang sangat kecil jika perlu */
@media (max-width: 380px) {
    #birthdayCardPopup h2 {
        font-size: 1.3em;
    }
    #birthdayCardPopup p {
        font-size: 0.85em;
    }
     #birthdayCardPopup img.birthday-photo {
        max-width: 80px;
    }
}
    </style>
</head>
<body class="balloon-fiesta-active"> {{-- Class awal untuk body --}}

    {{-- 1. Area Animasi Balon (SEKARANG DI LUAR .container utama) --}}
    <div id="balloonFiesta">
        <h2>Pecahkan Balon Sesuai Urutan!</h2>
        {{-- Balon akan ditambahkan di sini oleh JavaScript --}}
    </div>

    {{-- Wrapper untuk konten utama yang akan muncul setelah balon --}}
    {{-- .container asli dari Laravel atau style Anda bisa digunakan di sini --}}
    <div class="container container-main-content"> 
        {{-- 2. Pop-up Card Ucapan --}}
        <div id="birthdayCardPopup">
            <h2>Selamat Ulang Tahun{{ $gift->age ? ' ke-'.$gift->age : '' }}! 🥳</h2>
            <p>Semoga hari ini penuh kebahagiaan dan semua harapanmu tercapai!</p>
            <button id="openMainGiftButton">Lihat Kado Spesialmu!</button>
        </div>

        {{-- 3. Konten Hadiah Utama (Awalnya disembunyikan) --}}
        <div id="giftContentSection" style="display:none;">
            <h2>Taraaa! Ini Kejutan Untukmu:</h2>
            @if ($gift->message)
            <div id="giftMessage" class="gift-item">
                <p>{{ $gift->message }}</p>
            </div>
            @endif
            @if ($gift->image_url)
            <img id="giftImage" src="{{ $gift->image_url }}" alt="Gambar Kejutan" style="max-width: 100%; border-radius: 8px; margin-top:15px;">
            @endif
            @if ($gift->video_url)
            <div id="giftVideoContainer" style="margin-top:15px;"></div>
            @endif
            @if ($gift->other_link)
            <div id="giftOtherLink" class="gift-item" style="margin-top:15px;">
                <p>Ada link tambahan juga untukmu:</p>
                <a href="{{ $gift->other_link }}" target="_blank" id="actualOtherLink">{{ $gift->other_link }}</a>
            </div>
            @endif
        </div>
    </div>

    {{-- Elemen Audio (Preload agar siap dimainkan) --}}
    <audio id="popSound" src="https://gifftme-pull.b-cdn.net/audio/balloon-pop.mp3" preload="auto"></audio>
    <audio id="hbdSound" src="https://gifftme-pull.b-cdn.net/audio/happy-birthday.mp3" preload="auto"></audio>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
    const bodyElement = document.body;
    const balloonFiestaDiv = document.getElementById('balloonFiesta');
    const fiestaTitle = balloonFiestaDiv.querySelector('h2'); // Ambil elemen judul
    const birthdayCardPopup = document.getElementById('birthdayCardPopup');
    const openMainGiftButton = document.getElementById('openMainGiftButton');
    const giftContentSection = document.getElementById('giftContentSection');
    const mainContentContainer = document.querySelector('.container-main-content');
    
    const popAudio = document.getElementById('popSound');
    const hbdAudio = document.getElementById('hbdSound');
    
    const videoUrl = @json($gift->video_url);
    const giftAge = @json($gift->age);
    const numberOfBalloons = giftAge ? parseInt(giftAge) : 7;
    let balloonsPoppedCount = 0;
    let currentExpectedPopOrder = 1;
    const balloonImageSrc = "{{ asset('images/balloon.png') }}";
    const balloonsArray = [];

    function createBalloons() {
        if (!balloonFiestaDiv) return;

        const fiestaRect = balloonFiestaDiv.getBoundingClientRect();
        const containerWidth = fiestaRect.width;
        const containerHeight = fiestaRect.height;
        
        // Perkiraan ukuran wrapper balon (gambar + nomor) dengan balon lebih besar
        const balloonWrapperWidth = 105; // Sesuai .balloon-img width
        const balloonWrapperHeight = 140; // Perkiraan tinggi img + nomor

        // Area aman untuk judul (misalnya tinggi judul + margin)
        const titleHeight = fiestaTitle ? fiestaTitle.offsetHeight + 20 : 60; // 20px margin bawah judul

        // Tentukan batas penempatan balon
        const minX = 10, minY = titleHeight; // Mulai di bawah judul
        const maxX = containerWidth - balloonWrapperWidth - 10;
        const maxY = containerHeight - balloonWrapperHeight - 10;

        if (maxX <= minX || maxY <= minY) {
            console.warn("Area penempatan balon terlalu kecil. Periksa dimensi #balloonFiesta dan ukuran balon.");
            // Anda bisa fallback ke penempatan yang lebih sederhana jika area terlalu kecil
            // atau tampilkan pesan error
            if (balloonFiestaDiv) balloonFiestaDiv.innerHTML += '<p style="color:red; text-align:center;">Layar terlalu kecil untuk menampilkan balon.</p>';
            return;
        }

        // Sederhanakan algoritma penyebaran untuk sekarang, fokus ke fungsionalitas
        // Nanti bisa ditingkatkan ke grid atau pembagian zona
        for (let i = 0; i < numberOfBalloons; i++) {
            const balloonOrder = i + 1;
            const wrapper = document.createElement('div');
            wrapper.classList.add('balloon-wrapper');
            wrapper.dataset.order = balloonOrder;

            const balloonImg = document.createElement('img');
            balloonImg.src = balloonImageSrc;
            balloonImg.alt = "Balon";
            balloonImg.classList.add('balloon-img');

            const numberSpan = document.createElement('span');
            numberSpan.classList.add('balloon-number');
            numberSpan.textContent = balloonOrder;

            wrapper.appendChild(balloonImg);
            wrapper.appendChild(numberSpan);
            wrapper.style.zIndex = numberOfBalloons - i;

            // Penempatan Acak dengan Batas
            wrapper.style.left = Math.max(minX, Math.random() * maxX) + 'px';
            wrapper.style.top = Math.max(minY, Math.random() * maxY) + 'px';
            
            // Animasi masuk setelah posisi diatur
            // Beri sedikit delay berbeda untuk setiap balon agar tidak muncul serentak
            setTimeout(() => {
                wrapper.style.transition = 'opacity 0.4s ease, transform 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55)';
                wrapper.style.opacity = '1';
                wrapper.style.transform = 'scale(1)';
                // Setelah muncul, set opacity untuk class .disabled jika perlu
                if (balloonOrder !== currentExpectedPopOrder) {
                    wrapper.classList.add('disabled');
                } else {
                     wrapper.classList.remove('disabled'); // Pastikan balon pertama tidak disabled
                }
            }, 50 + (i * 30)); // Delay kecil bertahap


            wrapper.addEventListener('click', function handleBalloonClick() {
                const clickedOrder = parseInt(this.dataset.order);
                console.log(`Clicked: ${clickedOrder}, Expected: ${currentExpectedPopOrder}, Popped: ${this.classList.contains('popped')}`);

                if (this.classList.contains('popped') || clickedOrder !== currentExpectedPopOrder) {
                    if (clickedOrder !== currentExpectedPopOrder && !this.classList.contains('popped')) {
                        this.style.transform = 'translateX(-5px) scale(1)'; // Tambahkan scale(1) agar tidak konflik dengan transform awal
                        setTimeout(() => { this.style.transform = 'translateX(5px) scale(1)'; }, 70);
                        setTimeout(() => { this.style.transform = 'translateX(0px) scale(1)'; }, 140);
                    }
                    return;
                }

                this.classList.add('popped'); // Ini akan memicu animasi CSS untuk pecah
                // Class 'disabled' akan otomatis tidak relevan karena 'popped' men-override opacity & pointer-events

                if (popAudio) {
                    const currentPopSound = new Audio(popAudio.src); // Buat instance baru
                    currentPopSound.play().catch(e => console.warn("Pop sound play error:", e));
                }
                
                balloonsPoppedCount++;
                console.log("Balloons popped so far: " + balloonsPoppedCount);
                currentExpectedPopOrder++; 
                console.log("Next expected pop order: " + currentExpectedPopOrder);

                updateBalloonStates();

                if (balloonsPoppedCount >= numberOfBalloons) {
                    console.log("All balloons popped!");
                    setTimeout(() => {
                        // Sembunyikan semua wrapper balon yang mungkin masih terlihat (meskipun sudah popped)
                        balloonsArray.forEach(b => b.style.display = 'none');
                        
                        balloonFiestaDiv.style.display = 'none';
                        bodyElement.classList.remove('balloon-fiesta-active');
                        
                        if (mainContentContainer) mainContentContainer.style.display = 'block';
                        
                        birthdayCardPopup.style.display = 'block';
                        requestAnimationFrame(() => {
                            birthdayCardPopup.classList.add('visible');
                        });

                        if (hbdAudio) {
                            hbdAudio.play().catch(e => console.warn("HBD sound play error:", e));
                        }
                    }, 600); 
                }
            });
            balloonsArray.push(wrapper);
            balloonFiestaDiv.appendChild(wrapper);
        }
        updateBalloonStates(); // Panggil sekali di awal untuk set initial disabled states
    }

    function updateBalloonStates() {
        console.log("Updating balloon states, current expected: " + currentExpectedPopOrder);
        balloonsArray.forEach(bWrapper => {
            const order = parseInt(bWrapper.dataset.order);
            if (!bWrapper.classList.contains('popped')) {
                if (order === currentExpectedPopOrder) {
                    console.log(`Enabling balloon ${order}`);
                    bWrapper.classList.remove('disabled');
                    // Pastikan opacity kembali normal jika sebelumnya disabled
                    // Opacity akan dihandle oleh animasi masuk atau jika tidak .disabled
                     if (bWrapper.style.opacity !== '1') { // Hanya jika opacity bukan 1 karena animasi masuk
                        // Jika sudah pernah disabled, kembalikan opacity normal
                        // Ini bisa dihapus jika animasi masuk menangani opacity dengan baik
                        // bWrapper.style.opacity = '1'; 
                     }

                } else {
                    console.log(`Disabling balloon ${order}`);
                    bWrapper.classList.add('disabled');
                }
            }
        });
    }

        function updateBalloonStates() {
            balloonsArray.forEach(bWrapper => {
                const order = parseInt(bWrapper.dataset.order);
                if (!bWrapper.classList.contains('popped')) {
                    if (order === currentExpectedPopOrder) {
                        bWrapper.classList.remove('disabled');
                    } else {
                        bWrapper.classList.add('disabled');
                    }
                }
            });
        }

            if (openMainGiftButton) {
                openMainGiftButton.addEventListener('click', () => {
                    birthdayCardPopup.style.display = 'none'; // Sembunyikan card
                    birthdayCardPopup.classList.remove('visible'); // Hapus class animasi
                    if (hbdAudio) {
                        hbdAudio.pause();
                        hbdAudio.currentTime = 0;
                    }
                    giftContentSection.style.display = 'block'; // Tampilkan hadiah utama

                    if (videoUrl && giftVideoContainer) {
                        const embedUrl = convertToEmbedUrl(videoUrl);
                        if (embedUrl) {
                            giftVideoContainer.innerHTML = `
                                <iframe width="560" height="315" 
                                        src="${embedUrl}" 
                                        title="YouTube video player" 
                                        frameborder="0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                        allowfullscreen>
                                </iframe>`;
                        }
                    }
                });
            }

            function convertToEmbedUrl(youtubeUrl) {
                // ... (fungsi convertToEmbedUrl tetap sama) ...
                if (!youtubeUrl) return null;
                let videoId = null;
                const patterns = [
                    /(?:https?:\/\/)?(?:www\.)?youtube\.com\/watch\?v=([^&]+)/,
                    /(?:https?:\/\/)?(?:www\.)?youtu\.be\/([^?]+)/,
                    /(?:https?:\/\/)?(?:www\.)?youtube\.com\/embed\/([^?]+)/
                ];
                for (const pattern of patterns) {
                    const match = youtubeUrl.match(pattern);
                    if (match && match[1]) {
                        videoId = match[1];
                        break;
                    }
                }
                if (videoId) {
                    return `https://www.youtube.com/embed/${videoId}`;
                }
                return null;
            }

            // Cek apakah gambar balon ada sebelum membuat balon
            const testBalloonImg = new Image();
                testBalloonImg.onload = function() {
                    console.log("Gambar balon siap, membuat balon...");
                    createBalloons(); // Panggil createBalloons setelah gambar utama siap
                };
                testBalloonImg.onerror = function() {
                    console.error("GAGAL memuat gambar balon dari: " + balloonImageSrc);
                    if (balloonFiestaDiv) balloonFiestaDiv.innerHTML = '<p style="color:red;">Oops! Gambar balon tidak bisa dimuat.</p>';
                };
                testBalloonImg.src = balloonImageSrc;
            });
    </script>
</body>
</html>