<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return Order::with(['customer', 'orderItems'])
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Order Number',
            'Customer',
            'Order Date',
            'Status',
            'Payment Status',
            'Payment Method',
            'Items Count',
            'Subtotal',
            'Discount',
            'Shipping',
            'Tax',
            'Total',
            'Billing Address',
            'Shipping Address'
        ];
    }

    public function map($order): array
    {
        $billingAddress = implode(', ', array_filter([
            $order->billing_address_line_1,
            $order->billing_address_line_2,
            $order->billing_city,
            $order->billing_state,
            $order->billing_postcode,
            $order->billing_country
        ]));

        $shippingAddress = implode(', ', array_filter([
            $order->shipping_address_line1,
            $order->shipping_address_line2,
            $order->shipping_city,
            $order->shipping_state,
            $order->shipping_postcode,
            $order->shipping_country
        ]));

        return [
            $order->order_number,
            $order->customer ? $order->customer->name : 'Guest',
            $order->created_at->format('Y-m-d H:i:s'),
            ucfirst($order->order_status),
            ucfirst($order->payment_status),
            $order->payment_method,
            $order->orderItems->sum('quantity'),
            $order->subtotal_amount,
            $order->discount_amount,
            $order->shipping_cost,
            $order->tax_amount ?? 0,
            $order->total_amount,
            $billingAddress,
            $shippingAddress
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A1:N1' => ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D9D9D9']]],
        ];
    }
}
