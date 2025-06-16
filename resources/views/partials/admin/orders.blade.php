@extends('layouts.admin')

@section('title', 'Order Management')

@section('content')
<!-- Content wrapper -->
<div class="content-wrapper">
  <!-- Content -->
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">eCommerce /</span> Orders
    </h4>

    <!-- Success/Error Messages -->
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
      <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between">
              <div class="avatar flex-shrink-0">
                <i class="bx bx-time-five text-warning fs-2"></i>
              </div>
            </div>
            <span class="fw-semibold d-block mb-1">Pending</span>
            <h3 class="card-title mb-2">{{ $statistics['pending'] ?? 0 }}</h3>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between">
              <div class="avatar flex-shrink-0">
                <i class="bx bx-loader-circle text-primary fs-2"></i>
              </div>
            </div>
            <span class="fw-semibold d-block mb-1">Processing</span>
            <h3 class="card-title mb-2">{{ $statistics['processing'] ?? 0 }}</h3>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between">
              <div class="avatar flex-shrink-0">
                <i class="bx bx-check-circle text-success fs-2"></i>
              </div>
            </div>
            <span class="fw-semibold d-block mb-1">Completed</span>
            <h3 class="card-title mb-2">{{ $statistics['completed'] ?? 0 }}</h3>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between">
              <div class="avatar flex-shrink-0">
                <i class="bx bx-x-circle text-danger fs-2"></i>
              </div>
            </div>
            <span class="fw-semibold d-block mb-1">Cancelled</span>
            <h3 class="card-title mb-2">{{ $statistics['cancelled'] ?? 0 }}</h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Order Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Order List</h5>
        <div class="d-flex">
          <button class="btn btn-outline-secondary me-2" id="exportBtn">
            <i class="bx bx-export me-1"></i> Export
          </button>
          <div class="input-group input-group-merge">
            <span class="input-group-text"><i class="bx bx-search"></i></span>
            <input type="text" class="form-control" placeholder="Search order..." id="searchInput">
          </div>
        </div>
      </div>
      <div class="card-datatable table-responsive">
        <table class="datatables-orders table border-top">
          <thead>
            <tr>
              <th>ORDER</th>
              <th>DATE</th>
              <th>CUSTOMER</th>
              <th>TOTAL</th>
              <th>PAYMENT</th>
              <th>STATUS</th>
              <th>METHOD</th>
              <th>ACTIONS</th>
            </tr>
          </thead>
          <tbody>
            @forelse($orders as $order)
            <tr>
              <td>#{{ $order->order_number }}</td>
              <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
              <td>
                {{ $order->billing_first_name }} {{ $order->billing_last_name }}
                @if($order->customer)
                  <small class="text-muted d-block">ID: #{{ $order->customer->id }}</small>
                @endif
              </td>
              <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
              <td>
                @if($order->payment_status === 'paid')
                  <span class="badge bg-label-success">Paid</span>
                @elseif($order->payment_status === 'pending')
                  <span class="badge bg-label-warning">Pending</span>
                @elseif($order->payment_status === 'failed')
                  <span class="badge bg-label-danger">Failed</span>
                @elseif($order->payment_status === 'refunded')
                  <span class="badge bg-label-info">Refunded</span>
                @else
                  <span class="badge bg-label-secondary">{{ ucfirst($order->payment_status) }}</span>
                @endif
              </td>
              <td>
                @if($order->order_status === 'delivered')
                  <span class="badge bg-label-success">Delivered</span>
                @elseif($order->order_status === 'processing')
                  <span class="badge bg-label-primary">Processing</span>
                @elseif($order->order_status === 'shipped')
                  <span class="badge bg-label-info">Shipped</span>
                @elseif($order->order_status === 'pending')
                  <span class="badge bg-label-warning">Pending</span>
                @elseif($order->order_status === 'cancelled')
                  <span class="badge bg-label-danger">Cancelled</span>
                @else
                  <span class="badge bg-label-dark">{{ ucfirst($order->order_status) }}</span>
                @endif
              </td>
              <td>
                @if($order->payment_method === 'credit_card')
                  <span class="d-flex align-items-center">
                    <i class="bx bx-credit-card me-2"></i> Credit Card
                  </span>
                @elseif($order->payment_method === 'bank_transfer')
                  <span class="d-flex align-items-center">
                    <i class="bx bx-bank me-2"></i> Bank Transfer
                  </span>
                @else
                  {{ ucfirst($order->payment_method) }}
                @endif
              </td>
              <td>
                <div class="dropdown">
                  <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('admin.orders.show', $order->id) }}">
                      <i class="bx bx-show me-1"></i> View Details
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.orders.export.pdf', $order->id) }}">
                      <i class="bx bx-download me-1"></i> Export PDF
                    </a>
                    @if($order->order_status !== 'cancelled')
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#statusModal"
                         onclick="setOrderId({{ $order->id }}, '{{ $order->order_status }}')">
                        <i class="bx bx-edit me-1"></i> Update Status
                      </a>
                    @endif
                  </div>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8" class="text-center">No orders found</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($orders->hasPages())
        <div class="card-footer">
          {{ $orders->links() }}
        </div>
      @endif
    </div>
  </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Export Orders</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.orders.export.excel') }}" method="GET">
        <div class="modal-body">
          <div class="row">
            <div class="col-12 mb-3">
              <label for="exportRange" class="form-label">Date Range</label>
              <select class="form-select" id="exportRange" name="range">
                <option value="all">All Orders</option>
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month">This Month</option>
                <option value="year">This Year</option>
                <option value="custom">Custom Range</option>
              </select>
            </div>
            <div class="col-12 mb-3 d-none" id="customRangeContainer">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="startDate" class="form-label">Start Date</label>
                  <input type="date" class="form-control" id="startDate" name="start_date">
                </div>
                <div class="col-md-6 mb-3">
                  <label for="endDate" class="form-label">End Date</label>
                  <input type="date" class="form-control" id="endDate" name="end_date">
                </div>
              </div>
            </div>
            <div class="col-12 mb-3">
              <label for="exportStatus" class="form-label">Status Filter</label>
              <select class="form-select" id="exportStatus" name="status">
                <option value="all">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Export</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Order Status</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="statusForm" method="POST">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="order_id" id="modalOrderId">
          <div class="mb-3">
            <label for="statusSelect" class="form-label">New Status</label>
            <select class="form-select" id="statusSelect" name="status" required>
              <option value="pending">Pending</option>
              <option value="processing">Processing</option>
              <option value="shipped">Shipped</option>
              <option value="delivered">Delivered</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="statusNotes" class="form-label">Notes (Optional)</label>
            <textarea class="form-control" id="statusNotes" name="notes" rows="3"></textarea>
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

@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Export button click handler
    document.getElementById('exportBtn').addEventListener('click', function() {
      const exportModal = new bootstrap.Modal(document.getElementById('exportModal'));
      exportModal.show();
    });

    // Date range selector handler
    document.getElementById('exportRange').addEventListener('change', function() {
      const customRangeContainer = document.getElementById('customRangeContainer');
      if (this.value === 'custom') {
        customRangeContainer.classList.remove('d-none');
      } else {
        customRangeContainer.classList.add('d-none');
      }
    });

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
      const searchValue = this.value.toLowerCase();
      const rows = document.querySelectorAll('.datatables-orders tbody tr');

      rows.forEach(row => {
        const rowText = row.textContent.toLowerCase();
        if (rowText.includes(searchValue)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });
  });

  // Set order ID and current status for status modal
  function setOrderId(orderId, currentStatus) {
    document.getElementById('modalOrderId').value = orderId;
    document.getElementById('statusSelect').value = currentStatus;
    document.getElementById('statusForm').action = `/admin/orders/${orderId}/status`;
  }
</script>
@endsection
