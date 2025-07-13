@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-screen">
    <div class="bg-white rounded-xl shadow p-8 max-w-lg w-full">
        <h2 class="text-2xl font-bold mb-4 text-center">Pembayaran Midtrans</h2>
        <div class="mb-4 text-center">
            <div class="text-lg">Order ID: <span class="font-semibold">{{ $order->id }}</span></div>
            <div class="text-lg">Total: <span class="font-semibold">{{ Number::currency($order->grand_total, 'IDR') }}</span></div>
        </div>
        <button id="pay-button" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-lg hidden">Bayar Sekarang</button>
        <div id="payment-status" class="mt-4 text-center text-gray-700"></div>
    </div>
</div>
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.clientKey') }}"></script>
<script>
    // Auto trigger Snap popup saat halaman dibuka
    window.onload = function() {
        window.snap.pay(@json($snapToken), {
            onSuccess: function(result) {
                document.getElementById('payment-status').innerHTML = '<span class="text-green-600 font-bold">Pembayaran berhasil!</span>';
                window.location.href = '/success';
            },
            onPending: function(result) {
                document.getElementById('payment-status').innerHTML = '<span class="text-yellow-600 font-bold">Menunggu pembayaran...</span>';
                window.location.href = '/success';
            },
            onError: function(result) {
                document.getElementById('payment-status').innerHTML = '<span class="text-red-600 font-bold">Pembayaran gagal!</span>';
            },
            onClose: function() {
                document.getElementById('payment-status').innerHTML = '<span class="text-gray-600">Anda menutup popup pembayaran.</span>';
            }
        });
    }
</script>
@endsection
