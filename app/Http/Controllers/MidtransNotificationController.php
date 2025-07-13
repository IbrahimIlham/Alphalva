<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransNotificationController extends Controller
{
    public function handle(Request $request)
    {
        // Konfigurasi Midtrans
        \Midtrans\Config::$serverKey = config('midtrans.serverKey');
        \Midtrans\Config::$isProduction = config('midtrans.isProduction');
        \Midtrans\Config::$isSanitized = config('midtrans.isSanitized');
        \Midtrans\Config::$is3ds = config('midtrans.is3ds');

        $notif = new \Midtrans\Notification();
        $orderId = $notif->order_id;
        $transactionStatus = $notif->transaction_status;
        $paymentType = $notif->payment_type;
        $fraudStatus = $notif->fraud_status;

        $order = Order::where('id', $orderId)->first();
        if (!$order) {
            Log::warning('Order not found for Midtrans notification: ' . $orderId);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Update status order sesuai status dari Midtrans
        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            $order->payment_status = 'paid';
            $order->status = 'paid';
        } elseif ($transactionStatus == 'pending') {
            $order->payment_status = 'pending';
            $order->status = 'pending';
        } elseif ($transactionStatus == 'expire') {
            $order->payment_status = 'expired';
            $order->status = 'expired';
        } elseif ($transactionStatus == 'cancel' || $transactionStatus == 'deny') {
            $order->payment_status = 'failed';
            $order->status = 'failed';
        }
        $order->save();

        return response()->json(['message' => 'Notification processed']);
    }
}
