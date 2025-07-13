@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<div class="max-w-3xl mx-auto py-16 px-4">
    <h1 class="text-4xl font-bold text-slate-700 mb-6">About Us</h1>
    <p class="text-lg text-slate-600 mb-4">
        Alphalva adalah platform e-commerce yang berfokus pada penjualan tas kamera, dengan mengutamakan kemudahan berbelanja online, keamanan transaksi, serta pelayanan terbaik bagi pelanggan di seluruh Indonesia.
    </p>
    <p class="text-md text-slate-500 mb-2">
        Kami menyediakan berbagai produk berkualitas, pengiriman cepat, serta integrasi pembayaran yang aman melalui Midtrans. Tim kami berkomitmen untuk memberikan pengalaman belanja yang nyaman dan memuaskan.
    </p>
    <p class="text-md text-slate-500 mb-6">
        Hubungi kami untuk pertanyaan, kerjasama, atau saran melalui email: 
        <a href="mailto:alphalva.01@gmail.com" class="text-blue-600 underline">alphalva.01@gmail.com</a>
    </p>

    <!-- Tombol kembali -->
    <a href="{{ url('/') }}" class="inline-block bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition">
        ← Kembali ke Beranda
    </a>
</div>
@endsection
