<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class MidtransCheckoutController extends Controller
{
    public function show($orderId)
    {
        $order = Order::findOrFail($orderId);
        // Pastikan hanya order milik user yang sedang login
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        return view('midtrans-checkout', [
            'order' => $order,
            'snapToken' => $order->snap_token,
        ]);
    }
}
