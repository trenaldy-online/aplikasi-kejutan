<?php

namespace App\Http\Controllers;

use App\Models\Gift;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Kita akan menggunakan Str::random

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
            'image_url' => 'nullable|url|max:2048',
            'video_url' => 'nullable|url|max:2048', // Bisa divalidasi lebih spesifik untuk YouTube
            'other_link' => 'nullable|url|max:2048',
            'age' => 'nullable|integer|min:1|max:150', // Validasi untuk usia
        ]);

        // Pastikan setidaknya satu field diisi (custom validation logic)
        if (empty($request->message) && empty($request->image_url) && empty($request->video_url) && empty($request->other_link)) {
            return back()->withErrors(['general' => 'Isi setidaknya satu field untuk membuat kejutan!'])->withInput();
        }
        
        // Generate slug unik
        $slug = Str::random(10); // Ganti dengan Str::orderedUuid() atau cara lain jika mau
        while (Gift::where('slug', $slug)->exists()) {
            $slug = Str::random(10); // Regenerate jika sudah ada (sangat jarang terjadi dengan random 10 char)
        }

        $gift = Gift::create([
            'slug' => $slug,
            'message' => $request->message,
            'image_url' => $request->image_url,
            'video_url' => $request->video_url,
            'other_link' => $request->other_link,
            'age' => $request->age, // Simpan usia
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