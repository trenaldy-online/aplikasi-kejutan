<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Kejutan Hadiah Digital</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}"> {{-- Kita akan buat file CSS ini nanti --}}
</head>
<body>
    <div class="container">
        <h1>Buat Kejutan Hadiah Digital</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
                @if (session('giftUrl'))
                    <p>Bagikan link ini: <input type="text" value="{{ session('giftUrl') }}" readonly onclick="this.select(); document.execCommand('copy'); alert('Link disalin!');" style="width: 100%; margin-top: 5px;"></p>
                @endif
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('gift.store') }}" method="POST">
            @csrf {{-- Token keamanan Laravel --}}

            <label for="message">Pesan Spesial:</label>
            <textarea id="message" name="message" rows="4" placeholder="Tulis pesanmu di sini...">{{ old('message') }}</textarea>
            
            <label for="image_url">URL Gambar (opsional):</label>
            <input type="url" id="image_url" name="image_url" value="{{ old('image_url') }}" placeholder="https://contoh.com/gambar.jpg">
            
            <label for="video_url">URL Video YouTube (opsional):</label>
            <input type="url" id="video_url" name="video_url" value="{{ old('video_url') }}" placeholder="https://www.youtube.com/watch?v=dQw4w9WgXcQ">

            {{-- TAMBAHKAN INPUT USIA DI SINI --}}
            <label for="age">Usia Penerima (untuk jumlah balon, opsional):</label>
            <input type="number" id="age" name="age" value="{{ old('age') }}" min="1" placeholder="Contoh: 25">

            <label for="other_link">Link Lainnya (opsional):</label>
            <input type="url" id="other_link" name="other_link" value="{{ old('other_link') }}" placeholder="https://tokopedia.com/hadiahpilihan">

            <button type="submit">Buat Link Kejutan!</button>
        </form>
    </div>
</body>
</html>