<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Size;
use App\Models\Color;
use Midtrans\Snap;
use Midtrans\Config;
use Midtrans\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function showCheckoutForm()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Anda harus login untuk melanjutkan checkout.');
        }

        $user = Auth::user();

        $cartItems = Cart::where('user_id', $user->id)
                             ->with(['product', 'productVariant.size', 'productVariant.color'])
                             ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong. Silakan tambahkan produk terlebih dahulu.');
        }

        $checkoutItems = [];
        $cartSubtotal = 0;

        foreach ($cartItems as $item) {
            $productName = $item->product->name ?? 'Produk Tidak Dikenal';
            $variantSize = $item->productVariant->size->name ?? null;
            $variantColor = $item->productVariant->color->name ?? null;

            if ($variantSize && $variantColor) {
                $productName .= ' (' . $variantSize . ', ' . $variantColor . ')';
            } elseif ($variantSize) {
                $productName .= ' (' . $variantSize . ')';
            } elseif ($variantColor) {
                $productName .= ' (' . $variantColor . ')';
            }

            $itemTotalPrice = $item->quantity * $item->price;

            $cartSubtotal += $itemTotalPrice;

            $checkoutItems[] = [
                'name' => $productName,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'total_price' => $itemTotalPrice,
            ];
        }

        return view('checkout', compact('user', 'checkoutItems', 'cartSubtotal'));
    }

    public function process(Request $request)
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10',
            'shipping_method' => 'required|in:Pengiriman Standar,Pengiriman Ekspres',
            'payment_method' => 'required|in:midtrans',
            'shipping_address_same_as_billing' => 'boolean',
        ];

        if (!$request->input('shipping_address_same_as_billing')) {
            $rules['shipping_first_name'] = 'required|string|max:255';
            $rules['shipping_last_name'] = 'nullable|string|max:255';
            $rules['shipping_phone_number'] = 'required|string|max:20';
            $rules['shipping_address_line1'] = 'required|string|max:255';
            $rules['shipping_address_line2'] = 'nullable|string|max:255';
            $rules['shipping_province'] = 'required|string|max:255';
            $rules['shipping_city'] = 'required|string|max:255';
            $rules['shipping_zip_code'] = 'required|string|max:10';
        }

        $validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $user = Auth::user();
            $cartItems = Cart::where('user_id', $user->id)
                             ->with(['product', 'productVariant.size', 'productVariant.color'])
                             ->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Keranjang belanja Anda kosong.'], 400);
            }

            $orderTotal = 0;
            $cartSubtotal = 0;
            $itemDetails = [];
            $midtransItems = [];

            foreach ($cartItems as $item) {
                $productName = $item->product->name ?? 'Produk Tidak Dikenal';
                $variantSize = $item->productVariant->size->name ?? null;
                $variantColor = $item->productVariant->color->name ?? null;

                if ($variantSize && $variantColor) {
                    $productName .= ' (' . $variantSize . ', ' . $variantColor . ')';
                } elseif ($variantSize) {
                    $productName .= ' (' . $variantSize . ')';
                } elseif ($variantColor) {
                    $productName .= ' (' . $variantColor . ')';
                }

                $itemTotalPrice = $item->quantity * $item->price;
                $cartSubtotal += $itemTotalPrice;
                $orderTotal += $itemTotalPrice;

                $itemDetails[] = [
                    'product_id' => $item->product_id,
                    'product_name' => $productName,
                    'selected_size' => $variantSize,
                    'selected_color' => $variantColor,
                    'quantity' => $item->quantity,
                    'price_per_item' => $item->price,
                    'total_price' => $itemTotalPrice,
                ];

                $midtransItems[] = [
                    'id' => $item->product_id,
                    'price' => (int) $item->price,
                    'quantity' => (int) $item->quantity,
                    'name' => $productName,
                ];
            }

            $shippingCost = 0;
            if ($request->shipping_method == 'Pengiriman Ekspres') {
                $shippingCost = 10000;
            }
            $orderTotal += $shippingCost;

            if ($shippingCost > 0) {
                $midtransItems[] = [
                    'id' => 'SHIPPING_FEE',
                    'price' => (int) $shippingCost,
                    'quantity' => 1,
                    'name' => 'Biaya Pengiriman',
                ];
            }

            DB::beginTransaction();

            $order = new Order();
            $order->user_id = $user->id;
            $order->order_number = 'ORD-' . time() . '-' . uniqid();
            $order->total_amount = $orderTotal;
            $order->subtotal_amount = $cartSubtotal;
            $order->discount_amount = 0.00;
            $order->shipping_method = $request->shipping_method;
            $order->shipping_cost = $shippingCost;
            $order->payment_method = $request->payment_method;
            $order->order_status = 'initiated';
            $order->payment_status = 'unpaid';
            $order->midtrans_transaction_id = null;
            $order->midtrans_payment_type = null;
            $order->midtrans_gross_amount = null;
            $order->midtrans_masked_card = null;
            $order->midtrans_bank = null;
            $order->midtrans_va_numbers = null;
            $order->payment_redirect_url = null;
            $order->midtrans_snap_token = null;

            $order->billing_first_name = $request->first_name;
            $order->billing_last_name = $request->last_name;
            $order->billing_email = $request->email;
            $order->billing_phone = $request->phone;
            $order->billing_address_line_1 = $request->address_line1;
            $order->billing_address_line_2 = $request->address_line2; // Corrected from address_address2
            $order->billing_zip_code = $request->zip_code;

            if ($request->shipping_address_same_as_billing) {
                $order->shipping_first_name = $request->first_name;
                $order->shipping_last_name = $request->last_name;
                $order->shipping_email = $request->email;
                $order->shipping_phone_number = $request->phone;
                $order->shipping_address_line_1 = $request->address_line1;
                $order->shipping_address_line_2 = $request->address_line2;
                $order->shipping_zip_code = $request->zip_code;
            } else {
                $order->shipping_first_name = $request->shipping_first_name;
                $order->shipping_last_name = $request->shipping_last_name;
                $order->shipping_email = $request->email;
                $order->shipping_phone_number = $request->shipping_phone_number;
                $order->shipping_address_line_1 = $request->shipping_address_line1;
                $order->shipping_address_line_2 = $request->shipping_address_line2;
                $order->shipping_zip_code = $request->shipping_zip_code;
            }

            $order->save();

            foreach ($itemDetails as $itemData) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $itemData['product_id'];
                $orderItem->product_name = $itemData['product_name'];
                $orderItem->selected_size = $itemData['selected_size'];
                $orderItem->selected_color = $itemData['selected_color'];
                $orderItem->quantity = $itemData['quantity'];
                $orderItem->price_per_item = $itemData['price_per_item'];
                $orderItem->total_price = $itemData['total_price'];
                $orderItem->save();
            }


            DB::commit();

            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_number,
                    'gross_amount' => (int) $orderTotal,
                ],
                'customer_details' => [
                    'first_name' => $order->billing_first_name,
                    'last_name' => $order->billing_last_name,
                    'email' => $order->billing_email,
                    'phone' => $order->billing_phone,
                    'billing_address' => [
                        'first_name' => $order->billing_first_name,
                        'last_name' => $order->billing_last_name,
                        'email' => $order->billing_email,
                        'phone' => $order->billing_phone,
                        'address' => $order->billing_address_line_1 . ($order->billing_address_line_2 ? ', ' . $order->billing_address_line_2 : ''),
                        'city' => $request->city,
                        'postal_code' => $order->billing_zip_code,
                        'country_code' => 'IDN',
                    ],
                    'shipping_address' => [
                        'first_name' => $order->shipping_first_name,
                        'last_name' => $order->shipping_last_name,
                        'email' => $order->shipping_email,
                        'phone' => $order->shipping_phone_number,
                        'address' => $order->shipping_address_line_1 . ($order->shipping_address_line_2 ? ', ' . $order->shipping_address_line_2 : ''),
                        'city' => $request->shipping_city,
                        'postal_code' => $order->shipping_zip_code,
                        'country_code' => 'IDN',
                    ],
                ],
                'item_details' => $midtransItems,
                'callbacks' => [
                    'finish' => route('checkout.success', ['order_id' => $order->id]),
                    'error' => route('checkout.error', ['order_id' => $order->id]),
                    'pending' => route('checkout.pending', ['order_id' => $order->id]),
                ],
            ];

            $snapToken = Snap::getSnapToken($params);
            $order->midtrans_snap_token = $snapToken;
            $order->payment_redirect_url = $snapToken;
            $order->save();

            return response()->json(['success' => true, 'snap_token' => $snapToken, 'order_id' => $order->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout processing error: ' . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memproses pesanan Anda. Silakan coba lagi. Error: ' . $e->getMessage()], 500);
        }
    }

    public function midtransCallback(Request $request)
    {
        $notif = new Notification();

        $transactionStatus = $notif->transaction_status;
        $fraudStatus = $notif->fraud_status;
        $orderId = $notif->order_id;
        $statusCode = $notif->status_code;

        $order = Order::where('order_number', $orderId)->first();

        if (!$order) {
            Log::error("Order not found for order_id: " . $orderId);
            return response()->json(['message' => 'Order not found'], 404);
        }

        $previousPaymentStatus = $order->payment_status;

        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                $order->payment_status = 'challenge';
                $order->order_status = 'pending_challenge';
            } else if ($fraudStatus == 'accept') {
                $order->payment_status = 'success';
                $order->order_status = 'processing';
            }
        } else if ($transactionStatus == 'settlement') {
            $order->payment_status = 'success';
            $order->order_status = 'processing';
        } else if ($transactionStatus == 'pending') {
            $order->payment_status = 'pending';
            $order->order_status = 'waiting_payment';
        } else if ($transactionStatus == 'deny') {
            $order->payment_status = 'denied';
            $order->order_status = 'cancelled';
        } else if ($transactionStatus == 'expire') {
            $order->payment_status = 'expired';
            $order->order_status = 'cancelled';
        } else if ($transactionStatus == 'cancel') {
            $order->payment_status = 'cancelled';
            $order->order_status = 'cancelled';
        } else if ($transactionStatus == 'refund' || $transactionStatus == 'partial_refund') {
            $order->payment_status = $transactionStatus;
            $order->order_status = 'refunded';
        }

        $order->midtrans_transaction_id = $notif->transaction_id;
        $order->midtrans_payment_type = $notif->payment_type;
        $order->midtrans_gross_amount = $notif->gross_amount;
        $order->midtrans_bank = $notif->va_numbers[0]->bank ?? $notif->bank ?? null;
        $order->midtrans_va_numbers = json_encode($notif->va_numbers ?? null);
        $order->payment_redirect_url = $notif->finish_redirect_url ?? null;
        $order->midtrans_masked_card = $notif->masked_card ?? null;

        $order->save();

        if ($order->payment_status === 'success' && ($previousPaymentStatus !== 'success' && $previousPaymentStatus !== 'processing')) {
            $this->updateStock($order);
            // Hapus keranjang belanja di sini, setelah pembayaran dikonfirmasi sukses
            Cart::where('user_id', $order->user_id)->delete();
            Log::info("Cart cleared for user " . $order->user_id . " after successful payment for order " . $order->order_number);
        }

        return response()->json(['message' => 'Notification processed successfully'], 200);
    }

    protected function updateStock(Order $order)
    {
        foreach ($order->items as $item) {
            $sizeId = null;
            if ($item->selected_size) {
                $size = Size::where('name', $item->selected_size)->first();
                $sizeId = $size ? $size->id : null;
            }

            $colorId = null;
            if ($item->selected_color) {
                $color = Color::where('name', $item->selected_color)->first();
                $colorId = $color ? $color->id : null;
            }

            $productVariantQuery = ProductVariant::where('product_id', $item->product_id);

            if ($sizeId !== null) {
                $productVariantQuery->where('size_id', $sizeId);
            } else {
                $productVariantQuery->whereNull('size_id');
            }

            if ($colorId !== null) {
                $productVariantQuery->where('color_id', $colorId);
            } else {
                $productVariantQuery->whereNull('color_id');
            }

            $productVariant = $productVariantQuery->first();

            if ($productVariant) {
                if ($productVariant->stock >= $item->quantity) {
                    $productVariant->stock -= $item->quantity;
                    $productVariant->save();
                    Log::info("Stock updated for variant ID: " . $productVariant->id . " by " . $item->quantity);
                } else {
                    Log::warning("Insufficient stock for product variant ID: " . $productVariant->id . ". Ordered: " . $item->quantity . ", Available: " . $productVariant->stock . ". Order " . $order->order_number . " may have issues.");
                }
            } else {
                Log::error("Product variant not found for item ID: " . $item->id . " (Product ID: " . $item->product_id . ", Size: " . $item->selected_size . ", Color: " . $item->selected_color . "). Order " . $order->order_number);
            }
        }
    }

    public function success(Request $request)
    {
        $orderId = $request->query('order_id');
        $order = null;
        if ($orderId) {
            $order = Order::find($orderId);
        }
        return view('success', compact('order'));
    }

    public function pending(Request $request)
    {
        $orderId = $request->query('order_id');
        $order = null;
        if ($orderId) {
            $order = Order::find($orderId);
        }
        return view('checkout.pending', compact('order'));
    }

    public function error(Request $request)
    {
        $orderId = $request->query('order_id');
        $order = null;
        if ($orderId) {
            $order = Order::find($orderId);
        }
        return view('checkout.error', compact('order'));
    }

    public function listOrders()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
                         ->orderBy('created_at', 'desc')
                         ->paginate(10);

        return view('order', compact('orders'));
    }

    public function showOrder(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            return redirect()->route('orders.index')->with('error', 'Pesanan tidak ditemukan atau Anda tidak memiliki akses.');
        }

        $order->load('orderItems');

        return view('orderdetail', compact('order'));
    }
}
