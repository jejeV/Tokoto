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

    <!-- Statistics Cards -->
    <div class="row mb-4">
      @foreach(['pending', 'processing', 'completed', 'cancelled'] as $status)
      <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card status-card" data-status="{{ $status }}" onclick="filterByStatus('{{ $status }}')">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between">
              <div class="avatar flex-shrink-0">
                <i class="bx
                  @if($status == 'pending') bx-time-five text-warning
                  @elseif($status == 'processing') bx-loader-circle text-primary
                  @elseif($status == 'completed') bx-check-circle text-success
                  @else bx-x-circle text-danger @endif
                  fs-2"></i>
              </div>
            </div>
            <span class="fw-semibold d-block mb-1">{{ ucfirst($status) }}</span>
            <h3 class="card-title mb-2">{{ $statistics[$status] ?? 0 }}</h3>
          </div>
        </div>
      </div>
      @endforeach
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
            <tr data-status="{{ $order->order_status }}" id="order-row-{{ $order->id }}">
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
                <span class="badge bg-{{
                  $order->payment_status == 'paid' ? 'success' :
                  ($order->payment_status == 'pending' ? 'warning' :
                  ($order->payment_status == 'failed' ? 'danger' : 'secondary'))
                }}">
                  {{ ucfirst($order->payment_status) }}
                </span>
              </td>
              <td>
                <span class="badge bg-{{
                  $order->order_status == 'delivered' ? 'success' :
                  ($order->order_status == 'processing' ? 'primary' :
                  ($order->order_status == 'shipped' ? 'info' :
                  ($order->order_status == 'pending' ? 'warning' :
                  ($order->order_status == 'cancelled' ? 'danger' : 'dark'))))
                }}" id="status-badge-{{ $order->id }}">
                  {{ ucfirst($order->order_status) }}
                </span>
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
                  <button class="btn btn-sm btn-icon dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.orders.show', $order->id) }}">
                      <i class="bx bx-show me-2"></i> View Details</a>
                    </li>
                    <li><a class="dropdown-item" href="{{ route('admin.orders.export.pdf', $order->id) }}">
                      <i class="bx bx-download me-2"></i> Export PDF</a>
                    </li>
                    @if($order->order_status !== 'cancelled')
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item status-update-btn" href="#"
                        data-bs-toggle="modal"
                        data-bs-target="#statusModal"
                        data-order-id="{{ $order->id }}"
                        data-current-status="{{ $order->order_status }}">
                      <i class="bx bx-edit me-2"></i> Update Status</a>
                    </li>
                    @endif
                  </ul>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="8" class="text-center py-4">No orders found</td>
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

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Order Status</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="statusForm" method="POST">
        @csrf
        <input type="hidden" name="order_id" id="modalOrderId">

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Current Status</label>
            <div class="form-control-plaintext fw-semibold" id="currentStatusText"></div>
          </div>

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
            <label for="statusNotes" class="form-label">Notes</label>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Initialize modals
  const statusModal = new bootstrap.Modal('#statusModal');

  // Status update button handler
  document.querySelectorAll('.status-update-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();

      const orderId = this.dataset.orderId;
      const currentStatus = this.dataset.currentStatus;

      // Set form values
      document.getElementById('modalOrderId').value = orderId;
      document.getElementById('currentStatusText').textContent =
        currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1);
      document.getElementById('statusSelect').value = currentStatus;
      document.getElementById('statusNotes').value = '';

      // Set form action with correct route
      const form = document.getElementById('statusForm');
      form.action = `/admin/orders/${orderId}/status`;
    });
  });

  // Status form submission
  document.getElementById('statusForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    try {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Updating...';

      const formData = new FormData(form);

      // Make AJAX request
      const response = await fetch(form.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();

      if (data.success) {
        updateOrderStatusUI(data);
        showAlert('success', data.message);
        statusModal.hide();
      } else {
        throw new Error(data.message || 'Error updating status');
      }
    } catch (error) {
      console.error('Error:', error);
      showAlert('error', error.message || 'Failed to update status. Please try again.');
    } finally {
      // Re-enable button
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    }
  });

  // Search functionality
  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    searchInput.addEventListener('input', debounce(function() {
      const searchValue = this.value.toLowerCase().trim();
      const rows = document.querySelectorAll('.datatables-orders tbody tr');

      rows.forEach(row => {
        const rowText = row.textContent.toLowerCase();
        row.style.display = searchValue === '' || rowText.includes(searchValue) ? '' : 'none';
      });
    }, 300));
  }

  // Export button handler
  const exportBtn = document.getElementById('exportBtn');
  if (exportBtn) {
    exportBtn.addEventListener('click', function() {
      const exportModal = new bootstrap.Modal('#exportModal');
      exportModal.show();
    });
  }

  function updateOrderStatusUI(data) {
    const statusBadge = document.getElementById(`status-badge-${data.order_id}`);
    const orderRow = document.getElementById(`order-row-${data.order_id}`);

    if (statusBadge && orderRow) {
      statusBadge.textContent = data.new_status_label;
      statusBadge.className = `badge bg-${getStatusColor(data.new_status)}`;

      orderRow.setAttribute('data-status', data.new_status);

      const updateBtn = orderRow.querySelector('.status-update-btn');
      if (updateBtn) {
        updateBtn.setAttribute('data-current-status', data.new_status);
      }
    }
  }
});

// Helper function to get status color
function getStatusColor(status) {
  const statusColors = {
    'pending': 'warning',
    'processing': 'primary',
    'shipped': 'info',
    'delivered': 'success',
    'completed': 'success',
    'cancelled': 'danger'
  };
  return statusColors[status] || 'dark';
}

// Filter by status card click
function filterByStatus(status) {
  const rows = document.querySelectorAll('.datatables-orders tbody tr');

  // Update card active state
  document.querySelectorAll('.status-card').forEach(card => {
    card.classList.toggle('border-primary', card.dataset.status === status);
  });

  // Show/hide rows
  rows.forEach(row => {
    row.style.display = status === 'all' || row.dataset.status === status ? '' : 'none';
  });
}

// Debounce function for search input
function debounce(func, wait) {
  let timeout;
  return function() {
    const context = this, args = arguments;
    clearTimeout(timeout);
    timeout = setTimeout(() => func.apply(context, args), wait);
  };
}

// Show alert function
function showAlert(type, message) {
  // Remove existing alerts
  document.querySelectorAll('.alert.position-fixed').forEach(alert => alert.remove());

  const alertDiv = document.createElement('div');
  alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
  alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
  alertDiv.innerHTML = `
    ${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  `;

  // Add to page and auto-remove after 5 seconds
  document.body.appendChild(alertDiv);
  setTimeout(() => alertDiv.remove(), 5000);
}
</script>
@endpush

@section('styles')
<style>
  .status-card {
    cursor: pointer;
    transition: all 0.2s ease;
    border: 2px solid transparent;
  }
  .status-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  }
  .status-card.border-primary {
    border-color: #696cff !important;
  }
  .badge {
    font-size: 0.85em;
    padding: 0.35em 0.65em;
  }
  .dropdown-menu {
    min-width: 10rem;
  }
  .spinner-border-sm {
    width: 0.875rem;
    height: 0.875rem;
  }
  .alert.position-fixed {
    animation: slideInRight 0.3s ease-out;
  }
  @keyframes slideInRight {
    from {
      transform: translateX(100%);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
</style>
@endsection
