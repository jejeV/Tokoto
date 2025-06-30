@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold py-3 mb-0">
                <span class="text-muted fw-light">Orders /</span> Order Details
            </h4>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-label-secondary">
                <i class="bx bx-chevron-left me-1"></i> Back to Orders
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Order #{{ $order->order_number }}</h5>
                    <span class="text-muted">Placed on {{ $order->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div>
                    {{-- Menggunakan class helper untuk badge status agar lebih bersih --}}
                    <span class="badge bg-{{ $order->order_status_badge_color }}">
                        {{ ucfirst($order->order_status) }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6 mb-4 mb-md-0">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Customer Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h6 class="mb-1">{{ $order->customer->name ?? ($order->billing_first_name . ' ' . $order->billing_last_name) }}</h6>
                                    <span class="text-muted">Customer ID: #{{ $order->user_id ?? 'Guest' }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="d-block fw-semibold">Contact Info</span>
                                    <span class="text-muted">{{ $order->billing_email }}</span><br>
                                    <span class="text-muted">{{ $order->billing_phone }}</span>
                                </div>
                                <div class="mb-3">
                                    <span class="d-block fw-semibold">Shipping Address</span>
                                    <address class="text-muted mb-2">
                                        {{ $order->billing_address_line_1 }}<br>
                                        @if($order->billing_address_line_2)
                                            {{ $order->billing_address_line_2 }}<br>
                                        @endif
                                        {{ $order->billing_zip_code }}
                                    </address>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Order Summary</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Subtotal:</span>
                                    <span>RP{{ number_format($order->subtotal_amount, 0) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Discount:</span>
                                    <span>-RP{{ number_format($order->discount_amount, 0) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Tax:</span>
                                    <span>RP{{ number_format($order->tax_amount ?? 0, 0) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Shipping:</span>
                                    <span>RP{{ number_format($order->shipping_cost, 0) }}</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Total:</span>
                                    <span>RP{{ number_format($order->total_amount, 0) }}</span>
                                </div>
                                <hr>
                                <div class="mb-2">
                                    <span class="d-block fw-semibold">Payment Method</span>
                                    <span class="text-muted">
                                        @if($order->payment_method === 'credit_card')
                                            <i class="bx bx-credit-card me-1"></i> Credit Card (****{{ substr($order->payment_card_last4, -4) }})
                                        @elseif($order->payment_method === 'paypal')
                                            <i class="bx bxl-paypal me-1"></i> PayPal
                                        @else
                                            {{-- Pastikan payment_method tidak null --}}
                                            {{ ucfirst($order->payment_method ?? 'N/A') }}
                                        @endif
                                    </span>
                                </div>
                                <div class="mb-2">
                                    <span class="d-block fw-semibold">Payment Status</span>
                                    <span class="badge bg-{{
                                     $order->payment_status == 'paid' ? 'success' :
                                    ($order->payment_status == 'pending' ? 'warning' :
                                    ($order->payment_status == 'failed' ? 'danger' : 'secondary'))
                                    }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </div>
                                @if($order->payment_status == 'refunded')
                                <div class="mt-2">
                                    <span class="d-block fw-semibold">Refund Amount</span>
                                    <span class="text-danger">-${{ number_format($order->refund_amount ?? 0, 2) }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Order Items ({{ count($order->items) }})</h6>
                        <span class="badge bg-label-primary">Total Items: {{ $order->items->sum('quantity') }}</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead class="border-bottom">
                                    <tr>
                                        <th>PRODUCT</th>
                                        <th>PRICE</th>
                                        <th>QTY</th>
                                        <th>TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    {{-- Gunakan asset() untuk memastikan URL gambar yang benar --}}
                                                    @if($item->product && $item->product->image)
                                                        <img src="{{ asset('assets/home/img/nike/' . $item->product->image) }}" alt="{{ $item->product_name }}" width="60" class="rounded">
                                                    @else
                                                        {{-- Placeholder jika tidak ada gambar produk --}}
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                            <i class="bx bx-image fs-4 text-muted"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $item->product_name }}</h6>
                                                    <small class="text-muted">SKU: {{ $item->product->sku ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>RP{{ number_format($item->price_per_item, 0) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>RP{{ number_format($item->total_price, 0) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Gunakan empty directive untuk kasus tanpa relatedOrders --}}
                @forelse($relatedOrders ?? [] as $relatedOrder)
                @if ($loop->first) {{-- Tampilkan card header hanya sekali --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Customer's Recent Orders</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                @endif
                                    <tr>
                                        <td>{{ $relatedOrder->order_number }}</td>
                                        <td>{{ $relatedOrder->created_at->format('M d, Y') }}</td>
                                        <td>
                                            {{-- Menggunakan class helper untuk badge status --}}
                                            <span class="badge bg-{{ $relatedOrder->order_status_badge_color }}">
                                                {{ ucfirst($relatedOrder->order_status) }}
                                            </span>
                                        </td>
                                        <td>${{ number_format($relatedOrder->total_amount, 2) }}</td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $relatedOrder->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        </td>
                                    </tr>
                @if ($loop->last)
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
                @empty
                    {{-- Tampilkan pesan jika tidak ada related orders --}}
                    <div class="alert alert-info" role="alert">
                        No recent orders found for this customer.
                    </div>
                @endforelse

                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Order Activity Timeline</h6>
                    </div>
                    <div class="card-body">
                        <ul class="timeline">
                            @foreach($timeline as $event)
                            <li class="timeline-item timeline-item-{{
                                $event['event'] == 'Processing' ? 'primary' :
                                ($event['event'] == 'Shipped' ? 'info' :
                                ($event['event'] == 'Delivered' ? 'success' :
                                ($event['event'] == 'Cancelled' ? 'danger' :
                                ($event['event'] == 'Payment received' ? 'success' : 'primary'))))
                            }} mb-4">
                                <span class="timeline-indicator">
                                    <i class="bx bx-{{
                                        strtolower($event['event']) == 'processing' ? 'loader' :
                                        (strtolower($event['event']) == 'shipped' ? 'package' :
                                        (strtolower($event['event']) == 'delivered' ? 'check' :
                                        (strtolower($event['event']) == 'cancelled' ? 'x' :
                                        (strtolower($event['event']) == 'payment received' ? 'credit-card' : 'time'))))
                                    }}"></i>
                                </span>
                                <div class="timeline-event">
                                    <div class="timeline-header">
                                        <h6 class="mb-0">{{ $event['event'] }}</h6>
                                        <small class="text-muted">{{ $event['date']->format('M d, Y H:i') }}</small>
                                    </div>
                                    @if(isset($event['description']) && $event['description'])
                                        <p class="mb-2">{{ $event['description'] }}</p>
                                    @endif
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <div>
                    <button class="btn btn-label-danger" data-bs-toggle="modal" data-bs-target="#deleteOrderModal">
                        <i class="bx bx-trash me-1"></i> Delete Order
                    </button>
                    @if($order->payment_status == 'paid' && $order->order_status != 'cancelled')
                    <button class="btn btn-label-warning ms-2" data-bs-toggle="modal" data-bs-target="#refundOrderModal">
                        <i class="bx bx-dollar me-1"></i> Process Refund
                    </button>
                    @endif
                </div>
                <div>
                    <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-label-secondary me-2">
                        <i class="bx bx-printer me-1"></i> Print Invoice
                    </a>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                        <i class="bx bx-edit me-1"></i> Update Status
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Are you sure you want to delete this order (#{{ $order->order_number }})? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.orders.updatestatus', $order->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Current Status</label>
                        <div class="form-control-plaintext fw-semibold">
                            {{ ucfirst($order->order_status) }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">New Status</label>
                        <select class="form-select" id="status" name="status" required>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ $order->order_status == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add any notes about this status change..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Pastikan modal hanya dirender jika kondisi terpenuhi --}}
{{-- @if($order->payment_status == 'paid' && $order->order_status != 'cancelled')
<div class="modal fade" id="refundOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Process Refund</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.orders.refund', $order->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Order Total</label>
                        <div class="form-control-plaintext fw-bold">${{ number_format($order->total_amount, 2) }}</div>
                    </div>
                    <div class="mb-3">
                        <label for="refund_amount" class="form-label">Refund Amount</label>
                        <input type="number" class="form-control" id="refund_amount" name="refund_amount"
                                min="0.01" max="{{ $order->total_amount }}" step="0.01"
                                value="{{ $order->total_amount }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="refund_reason" class="form-label">Reason</label>
                        <textarea class="form-control" id="refund_reason" name="refund_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Process Refund</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif --}}
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize modals
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteOrderModal'));
    const statusModal = new bootstrap.Modal(document.getElementById('updateStatusModal'));

    // Check if refundModal element exists before initializing
    const refundModalElement = document.getElementById('refundOrderModal');
    if (refundModalElement) {
        new bootstrap.Modal(refundModalElement);
    }

    // Update status button click
    // Prefer event delegation or target specific button if multiple
    document.querySelector('[data-bs-target="#updateStatusModal"]').addEventListener('click', function(e) {
        e.preventDefault();
        statusModal.show();
    });
});
</script>
@endpush
