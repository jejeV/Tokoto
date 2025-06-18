@extends('layouts.admin')

@section('title', 'Product Management')

@section('content')
<!-- Content wrapper -->
<div class="content-wrapper">
  <!-- Content -->
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Products /</span> Management
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
                <i class="bx bx-package text-primary fs-2"></i>
              </div>
            </div>
            <span class="fw-semibold d-block mb-1">Total Products</span>
            <h3 class="card-title mb-2">{{ $totalProducts ?? 0 }}</h3>
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
            <span class="fw-semibold d-block mb-1">In Stock</span>
            <h3 class="card-title mb-2">{{ $inStockProducts ?? 0 }}</h3>
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
            <span class="fw-semibold d-block mb-1">Out of Stock</span>
            <h3 class="card-title mb-2">{{ $outOfStockProducts ?? 0 }}</h3>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
          <div class="card-body">
            <div class="card-title d-flex align-items-start justify-content-between">
              <div class="avatar flex-shrink-0">
                <i class="bx bx-collection text-info fs-2"></i>
              </div>
            </div>
            <span class="fw-semibold d-block mb-1">Total Variants</span>
            <h3 class="card-title mb-2">{{ $totalVariants ?? 0 }}</h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Product Table -->
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Product List</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" id="addProductBtn">
          <i class="bx bx-plus me-1"></i> Add Product
        </button>
      </div>
      <div class="card-datatable table-responsive">
        <table class="datatables-products table border-top">
          <thead>
            <tr>
              <th>Product</th>
              <th>Variants</th>
              <th>Stock</th>
              <th>Price</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($products as $product)
            <tr>
              <td>
                <div class="d-flex align-items-center">
                  <img src="{{ $product->image ? asset('assets/home/img/nike/'.$product->image) : asset('assets/home/img/nike/') }}"
                       alt="{{ $product->name }}" class="rounded me-3" width="50" height="50"
                       style="object-fit: cover;">
                  <div>
                    <span class="fw-semibold">{{ $product->name }}</span>
                    <small class="text-muted d-block">{{ Str::limit($product->description, 30) }}</small>
                  </div>
                </div>
              </td>
              <td>
                @if($product->productVariants->count() > 0)
                  <span class="badge bg-label-primary">{{ $product->productVariants->count() }} variants</span>
                @else
                  <span class="badge bg-label-secondary">No variants</span>
                @endif
              </td>
              <td>
                @if($product->productVariants->count() > 0)
                  {{ $product->productVariants->sum('stock') }}
                @else
                  0
                @endif
              </td>
              <td>
                @if($product->productVariants->count() > 0)
                  @php
                    $minPrice = $product->productVariants->min('price');
                    $maxPrice = $product->productVariants->max('price');
                  @endphp
                  @if($minPrice == $maxPrice)
                    Rp {{ number_format($minPrice, 0, ',', '.') }}
                  @else
                    Rp {{ number_format($minPrice, 0, ',', '.') }} - Rp {{ number_format($maxPrice, 0, ',', '.') }}
                  @endif
                @else
                  Rp {{ number_format($product->price, 0, ',', '.') }}
                @endif
              </td>
              <td>
                <div class="dropdown">
                  <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu">
                    <button class="dropdown-item edit-product" data-id="{{ $product->id }}">
                      <i class="bx bx-edit me-1"></i> Edit
                    </button>
                    <button class="dropdown-item delete-product" data-id="{{ $product->id }}">
                      <i class="bx bx-trash me-1"></i> Delete
                    </button>
                  </div>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="text-center">No products found</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($products->hasPages())
        <div class="card-footer">
          {{ $products->links() }}
        </div>
      @endif
    </div>
  </div>
</div>

<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Add New Product</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="productForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="product_id" name="id">
            <input type="hidden" id="form_method" name="_method" value="">

            <div class="modal-body">
              <!-- Error container -->
              <div id="errorContainer" class="alert alert-danger d-none"></div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Product Name *</label>
                  <input type="text" class="form-control" id="name" name="name" required>
                  <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Base Price *</label>
                  <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                  <div class="invalid-feedback"></div>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
              </div>

              <div class="mb-3">
                <label class="form-label">Product Image</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                <div class="form-text">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF, SVG</div>
                <div class="form-check mt-2 d-none" id="removeImageContainer">
                  <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                  <label class="form-check-label" for="remove_image">
                    Remove current image
                  </label>
                </div>
                <div id="imagePreview" class="mt-2"></div>
              </div>

              <hr class="my-4">

              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Product Variants</h5>
                <button type="button" class="btn btn-sm btn-primary" id="addVariantBtn">
                  <i class="bx bx-plus me-1"></i> Add Variant
                </button>
              </div>

              <div id="variantsContainer">
                <!-- Variants will be added here dynamically -->
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary" id="submitBtn">Save Product</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Variant Template -->
    <div id="variantTemplate" class="d-none">
      <div class="variant-row mb-3 border p-3 rounded bg-light">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Size</label>
            <select class="form-select variant-size" name="variants[INDEX][size_id]">
              <option value="">Select Size</option>
              @foreach($sizes as $size)
                <option value="{{ $size->id }}">{{ $size->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Color</label>
            <select class="form-select variant-color" name="variants[INDEX][color_id]">
              <option value="">Select Color</option>
              @foreach($colors as $color)
                <option value="{{ $color->id }}">{{ $color->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Price *</label>
            <input type="number" step="0.01" class="form-control variant-price" name="variants[INDEX][price]" required>
          </div>
          <div class="col-md-2">
            <label class="form-label">Stock *</label>
            <input type="number" class="form-control variant-stock" name="variants[INDEX][stock]" required min="0">
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="btn btn-sm btn-danger remove-variant w-100">
              <i class="bx bx-trash"></i> Remove
            </button>
          </div>
          <input type="hidden" name="variants[INDEX][id]" class="variant-id" value="">
        </div>
      </div>
    </div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this product? This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let variantIndex = 0;
    const baseUrl = "{{ url('admin/products') }}";

    // Initialize modal for new product
    $('#addProductBtn').click(function() {
        resetForm();
        $('#modalTitle').text('Add New Product');
        $('#submitBtn').text('Save Product');
        $('#form_method').val('');
        $('#productModal').modal('show');
    });

    // Handle edit product
    $(document).on('click', '.edit-product', function() {
        var productId = $(this).data('id');
        fetchProductData(productId);
    });

    // Fetch product data for editing
    function fetchProductData(productId) {
        resetForm();
        $('#modalTitle').text('Loading...');
        $('#submitBtn').html('<span class="spinner-border spinner-border-sm"></span> Loading...').prop('disabled', true);
        $('#productModal').modal('show');

        $.ajax({
            url: baseUrl + '/' + productId + '/edit',
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                if (!response.success) {
                    showError('Failed to load product data');
                    return;
                }

                const product = response.product;

                // Fill basic product data
                $('#product_id').val(product.id);
                $('#name').val(product.name || '');
                $('#price').val(product.price || '');
                $('#description').val(product.description || '');

                // Handle product image
                $('#imagePreview').empty();
                $('#removeImageContainer').addClass('d-none');
                $('#remove_image').prop('checked', false);

                if (product.image_url) {
                    $('#imagePreview').html(
                        `<div class="mt-2">
                            <img src="${product.image_url}"
                                 class="img-thumbnail" style="max-height: 150px;"
                                 alt="Current Image">
                            <div class="form-text">Current image</div>
                        </div>`
                    );
                    $('#removeImageContainer').removeClass('d-none');
                }

                // Clear variants container
                $('#variantsContainer').empty();
                variantIndex = 0;

                // Add existing variants
                if (product.variants && product.variants.length > 0) {
                    product.variants.forEach(function(variant) {
                        addVariantRow({
                            id: variant.id,
                            size_id: variant.size_id,
                            color_id: variant.color_id,
                            price: variant.price,
                            stock: variant.stock
                        });
                    });
                }

                // Update modal
                $('#modalTitle').text('Edit Product: ' + (product.name || 'Unknown'));
                $('#form_method').val('PUT');
                $('#submitBtn').text('Update Product').prop('disabled', false);
            },
            error: function(xhr) {
                let errorMessage = 'Error loading product data. ';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage += xhr.responseJSON.message;
                }
                showError(errorMessage);
                $('#productModal').modal('hide');
            }
        });
    }

    // Handle delete product
    var productIdToDelete;
    $(document).on('click', '.delete-product', function() {
        productIdToDelete = $(this).data('id');
        $('#deleteConfirmModal').modal('show');
    });

    $('#confirmDeleteBtn').click(function() {
        $(this).html('<span class="spinner-border spinner-border-sm"></span> Deleting...').prop('disabled', true);

        $.ajax({
            url: baseUrl + '/' + productIdToDelete,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                _method: 'DELETE'
            },
            success: function(response) {
                $('#deleteConfirmModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                $('#confirmDeleteBtn').text('Delete').prop('disabled', false);
                let errorMessage = 'Failed to delete product.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage += ' ' + xhr.responseJSON.message;
                }
                showError(errorMessage);
            }
        });
    });

    // Add variant row
    $('#addVariantBtn').click(function() {
        addVariantRow();
    });

    // Remove variant row
    $(document).on('click', '.remove-variant', function() {
        $(this).closest('.variant-row').remove();
    });

    // Submit form
    $('#productForm').submit(function(e) {
        e.preventDefault();
        clearErrors();

        var formData = new FormData(this);
        var productId = $('#product_id').val();
        var isEdit = productId && productId.trim() !== '';

        // Show loading state
        $('#submitBtn').html('<span class="spinner-border spinner-border-sm"></span> Processing...').prop('disabled', true);

        var url = isEdit ? (baseUrl + '/' + productId) : (baseUrl);

        // For edit, ensure _method is set to PUT
        if (isEdit) {
            formData.set('_method', 'PUT');
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#productModal').modal('hide');

                // Show success message
                if (response.message) {
                    $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                      response.message +
                      '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                      '</div>').insertAfter('.fw-bold.py-3.mb-4');
                }

                // Reload page after short delay
                setTimeout(function() {
                    location.reload();
                }, 1000);
            },
            error: function(xhr) {
                resetSubmitButton();

                if (xhr.status === 422) {
                    // Handle validation errors
                    var errors = xhr.responseJSON.errors;
                    displayValidationErrors(errors);
                } else {
                    let errorMessage = 'Server error occurred.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showError(errorMessage);
                }
            }
        });
    });

    // Helper functions
    function resetForm() {
        $('#productForm')[0].reset();
        $('#product_id').val('');
        $('#variantsContainer').empty();
        $('#imagePreview').empty();
        $('#removeImageContainer').addClass('d-none');
        $('#remove_image').prop('checked', false);
        clearErrors();
        variantIndex = 0;
    }

    function resetSubmitButton() {
        var productId = $('#product_id').val();
        var isEdit = productId && productId.trim() !== '';
        $('#submitBtn').text(isEdit ? 'Update Product' : 'Save Product').prop('disabled', false);
    }

    function addVariantRow(data = {}) {
        var template = $('#variantTemplate').html();
        template = template.replace(/INDEX/g, variantIndex);

        var $newRow = $(template);

        // Set values if provided
        if (data.id) {
            $newRow.find('.variant-id').val(data.id);
        }
        if (data.size_id) {
            $newRow.find('.variant-size').val(data.size_id);
        }
        if (data.color_id) {
            $newRow.find('.variant-color').val(data.color_id);
        }
        if (data.price) {
            $newRow.find('.variant-price').val(data.price);
        } else {
            // Default to product base price if not set
            $newRow.find('.variant-price').val($('#price').val());
        }
        if (data.stock !== undefined) {
            $newRow.find('.variant-stock').val(data.stock);
        } else {
            $newRow.find('.variant-stock').val(0);
        }

        $('#variantsContainer').append($newRow);
        variantIndex++;
    }

    function showError(message) {
        $('#errorContainer').removeClass('d-none').html(`
            <div class="d-flex align-items-center">
                <i class="bx bx-error-circle me-2"></i>
                <div>${message}</div>
            </div>
        `);

        // Scroll to error if modal is open
        if ($('#productModal').hasClass('show')) {
            $('#productModal .modal-body').animate({
                scrollTop: 0
            }, 300);
        }
    }

    function clearErrors() {
        $('#errorContainer').addClass('d-none').empty();
        $('.form-control, .form-select').removeClass('is-invalid');
        $('.invalid-feedback').empty();
    }

    function displayValidationErrors(errors) {
        var errorMessages = [];

        $.each(errors, function(field, messages) {
            errorMessages.push(messages[0]);

            // Highlight specific fields
            var $field = $(`[name="${field}"]`);
            if ($field.length) {
                $field.addClass('is-invalid');
                $field.siblings('.invalid-feedback').text(messages[0]);
            }
        });

        showError('Please fix the following errors:<br>• ' + errorMessages.join('<br>• '));
    }

    // Handle image preview
    $('#image').change(function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').html(
                    `<div class="mt-2">
                        <img src="${e.target.result}" class="img-thumbnail" style="max-height: 150px;" alt="Preview">
                        <div class="form-text">New image preview</div>
                    </div>`
                );
            };
            reader.readAsDataURL(file);
        }
    });

    // Handle modal cleanup
    $('#productModal').on('hidden.bs.modal', function () {
        resetForm();
    });
});
</script>
@endpush
