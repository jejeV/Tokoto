<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $invoiceNo }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.5; color: #333; }
        .header { margin-bottom: 30px; }
        .invoice-title { font-size: 24px; font-weight: bold; margin-bottom: 20px; }
        .invoice-info { margin-bottom: 20px; }
        .address-box { display: inline-block; vertical-align: top; width: 32%; margin-right: 1%; }
        .address-title { font-weight: bold; margin-bottom: 5px; }
        .divider { border-top: 1px solid #ddd; margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { text-align: left; padding: 8px; background-color: #f5f5f5; border-bottom: 1px solid #ddd; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .notes { margin-top: 20px; padding: 10px; background-color: #f9f9f9; border-left: 3px solid #ddd; }
        .total-table { width: 300px; float: right; }
        .total-table td { border: none; }
        .total-table tr:last-child td { border-top: 1px solid #ddd; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="invoice-title">Shoebaru Invoice</div>
        <div class="invoice-info">
            Invoice no.: {{ $invoiceNo }}<br>
            Invoice date: {{ $invoiceDate }}<br>
            Due: {{ $dueDate }}
        </div>
    </div>

    <div>
        <div class="address-box">
            <div class="address-title">From</div>
            {{ $from['company'] }}<br>
            {{ $from['name'] }}<br>
            {{ $from['email'] }}<br>
            {{ $from['phone'] }}<br>
            {{ $from['website'] }}<br>
            {{ $from['address'] }}
        </div>
        <div class="address-box">
            <div class="address-title">Bill to</div>
            {{ $billTo['name'] }}<br>
            @if($billTo['email'])<span>{{ $billTo['email'] }}</span><br>@endif
            {{ $billTo['phone'] }}<br>
            {{ $billTo['address'] }}
        </div>
        <div class="address-box">
            <div class="address-title">Ship to</div>
            {{ $shipTo['name'] }}<br>
            {{ $shipTo['phone'] }}<br>
            {{ $shipTo['address'] }}<br>
            Track #: {{ $trackingNo }}
        </div>
    </div>

    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Variant</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item['product_name'] }}</td>
                <td>{{ $item['variant'] }}</td>
                <td class="text-center">{{ $item['quantity'] }}</td>
                <td class="text-right">RP {{ number_format($item['price'], 0) }}</td>
                <td class="text-right">RP {{ number_format($item['total'], 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <div style="clear: both;">
        <div style="float: left; width: 50%;">
            <h3>Payment Information</h3>
            <p>
                <strong>Method:</strong> {{ $paymentMethod }}<br>
                <strong>Status:</strong> {{ ucfirst($paymentStatus) }}
            </p>

            <div class="notes">
                <strong>Notes</strong><br>
                {{ $notes }}
            </div>
        </div>

        <table class="total-table">
            <tr>
                <td>Subtotal:</td>
                <td class="text-right">RP {{ number_format($subtotal, 0) }}</td>
            </tr>
            @if($discount > 0)
            <tr>
                <td>Discount:</td>
                <td class="text-right">RP {{ number_format($discount, 0) }}</td>
            </tr>
            @endif
            @if($shipping > 0)
            <tr>
                <td>Shipping:</td>
                <td class="text-right">RP {{ number_format($shipping, 0) }}</td>
            </tr>
            @endif
            <tr>
                <td><strong>Total:</strong></td>
                <td class="text-right"><strong>RP {{ number_format($total, 0) }}</strong></td>
            </tr>
        </table>
    </div>
</body>
</html>
