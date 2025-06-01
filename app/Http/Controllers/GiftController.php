<?php

namespace App\Http\Controllers;

use App\Models\Gift;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Kita akan menggunakan Str::random
use Illuminate\Support\Facades\Storage;

class GiftController extends Controller
{
    // Menampilkan form untuk membuat hadiah baru
    public function create()
    {
        return view('gifts.create');
    }

    // Menyimpan hadiah baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string|max:5000',
            // Validasi untuk input file gambar baru
            'image_upload' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // max 2MB
            'video_url' => 'nullable|url|max:2048',
            'other_link' => 'nullable|url|max:2048',
            'age' => 'nullable|integer|min:1|max:150',
        ]);

        // Pastikan setidaknya satu field diisi (custom validation logic)
        // Anda mungkin ingin memperbarui logika ini jika image_upload adalah satu-satunya konten
        if (empty($request->message) && !$request->hasFile('image_upload') && empty($request->video_url) && empty($request->other_link)) {
            return back()->withErrors(['general' => 'Isi setidaknya satu field untuk membuat kejutan!'])->withInput();
        }
        
        $imageUrl = null; // Inisialisasi variabel untuk menyimpan URL gambar

        // Proses upload gambar jika ada
        if ($request->hasFile('image_upload')) {
            $file = $request->file('image_upload');
            // Buat nama file yang unik
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            // Simpan file ke storage/app/public/gift_images
            // Pastikan disk 'public' sudah dikonfigurasi di config/filesystems.php
            $path = $file->storeAs('gift_images', $fileName, 'public');
            // Dapatkan URL publik dari file yang disimpan
            $imageUrl = Storage::disk('public')->url($path);
        }

        $slug = Str::random(10);
        while (Gift::where('slug', $slug)->exists()) {
            $slug = Str::random(10);
        }

        $gift = Gift::create([
            'slug' => $slug,
            'message' => $request->message,
            'image_url' => $imageUrl, // Simpan URL gambar yang diupload (atau null jika tidak ada)
            'video_url' => $request->video_url,
            'other_link' => $request->other_link,
            'age' => $request->age,
        ]);

        $giftUrl = route('gift.show', ['slug' => $gift->slug]);

        return redirect()->route('gift.create')
                         ->with('success', 'Link kejutan berhasil dibuat!')
                         ->with('giftUrl', $giftUrl);
    }

    // Menampilkan halaman kejutan untuk penerima
    public function show($slug)
    {
        $gift = Gift::where('slug', $slug)->firstOrFail(); // firstOrFail akan menghasilkan 404 jika tidak ditemukan
        return view('gifts.show', compact('gift'));
    }
}