@extends('layouts.admin')

@section('title', 'Inventory Management - Vending POC')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="fas fa-boxes me-2"></i>
        Inventory Management
    </h1>
    <div class="d-flex gap-2">
        <select class="form-select" id="machineFilter" onchange="filterByMachine()">
            <option value="">All Machines</option>
            @foreach($machines as $machine)
                <option value="{{ $machine->id }}" {{ $machineId == $machine->id ? 'selected' : '' }}>
                    {{ $machine->machine_name }} ({{ $machine->machine_num }})
                </option>
            @endforeach
        </select>
        <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Add Drug to Machine
        </a>
    </div>
</div>

<!-- Quick Stats -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1">Total Items</h6>
                        <h3 class="mb-0">{{ $inventories->count() }}</h3>
                    </div>
                    <i class="fas fa-boxes fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1">In Stock</h6>
                        <h3 class="mb-0">{{ $inventories->where('stock_quantity', '>', 0)->count() }}</h3>
                    </div>
                    <i class="fas fa-check-circle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1">Low Stock</h6>
                        <h3 class="mb-0">{{ $inventories->filter(function($item) { return $item->isLowStock(); })->count() }}</h3>
                    </div>
                    <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1">Out of Stock</h6>
                        <h3 class="mb-0">{{ $inventories->where('stock_quantity', 0)->count() }}</h3>
                    </div>
                    <i class="fas fa-times-circle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>Slot</th>
                        <th>Machine</th>
                        <th>Drug</th>
                        <th>Stock</th>
                        <th>Threshold</th>
                        <th>Status</th>
                        <th>Expiry</th>
                        <th>Batch</th>
                        <th>Last Restocked</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventories as $inventory)
                    <tr class="{{ $inventory->isLowStock() ? 'table-warning' : '' }} {{ $inventory->stock_quantity == 0 ? 'table-danger' : '' }}">
                        <td>
                            <span class="badge bg-dark">
                                R{{ $inventory->slot_row }}C{{ $inventory->slot_column }}
                            </span>
                        </td>
                        <td>
                            <strong>{{ $inventory->vendingMachine->machine_name ?? 'Unknown' }}</strong>
                            <br>
                            <small class="text-muted">Machine #{{ $inventory->vendingMachine->machine_num ?? 'N/A' }}</small>
                        </td>
                        <td>
                            <strong>{{ $inventory->drug->name ?? 'Unknown Drug' }}</strong>
                            @if($inventory->drug->generic_name)
                                <br><small class="text-muted">{{ $inventory->drug->generic_name }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-2">{{ $inventory->stock_quantity }}</span>
                                @if($inventory->isLowStock())
                                    <i class="fas fa-exclamation-triangle text-warning" title="Low Stock"></i>
                                @elseif($inventory->stock_quantity == 0)
                                    <i class="fas fa-times-circle text-danger" title="Out of Stock"></i>
                                @else
                                    <i class="fas fa-check-circle text-success" title="In Stock"></i>
                                @endif
                            </div>
                        </td>
                        <td>{{ $inventory->threshold_quantity ?? 'Not set' }}</td>
                        <td>
                            @if($inventory->stock_quantity > 0)
                                @if($inventory->isExpired())
                                    <span class="badge bg-danger">Expired</span>
                                @elseif($inventory->isLowStock())
                                    <span class="badge bg-warning">Low Stock</span>
                                @else
                                    <span class="badge bg-success">Available</span>
                                @endif
                            @else
                                <span class="badge bg-danger">Out of Stock</span>
                            @endif
                        </td>
                        <td>
                            @if($inventory->expiry_date)
                                @if($inventory->isExpired())
                                    <span class="text-danger">
                                        {{ $inventory->expiry_date->format('M d, Y') }}
                                        <i class="fas fa-exclamation-triangle ms-1"></i>
                                    </span>
                                @else
                                    {{ $inventory->expiry_date->format('M d, Y') }}
                                @endif
                            @else
                                <span class="text-muted">Not set</span>
                            @endif
                        </td>
                        <td>{{ $inventory->batch_number ?? 'Not set' }}</td>
                        <td>
                            @if($inventory->last_restocked_at)
                                {{ $inventory->last_restocked_at->diffForHumans() }}
                            @else
                                <span class="text-muted">Never</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" 
                                        class="btn btn-sm btn-outline-primary" 
                                        onclick="updateStock({{ $inventory->id }})"
                                        title="Update Stock">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-warning" 
                                        onclick="restockItem({{ $inventory->id }})"
                                        title="Restock">
                                    <i class="fas fa-plus-square"></i>
                                </button>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-info" 
                                        onclick="viewHistory({{ $inventory->id }})"
                                        title="View History">
                                    <i class="fas fa-history"></i>
                                </button>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger" 
                                        onclick="confirmDelete('{{ route('admin.inventory.destroy', $inventory->id) }}')"
                                        title="Remove">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center">
                            <div class="py-4">
                                <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No inventory items found</h5>
                                <p class="text-muted">Add drugs to your vending machines to get started.</p>
                                <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>
                                    Add Drug to Machine
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Update Stock Modal -->
<div class="modal fade" id="updateStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>
                    Update Stock
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="updateStockForm">
                    <div class="mb-3">
                        <label class="form-label">Current Stock</label>
                        <input type="number" class="form-control" id="currentStock" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Stock Quantity</label>
                        <input type="number" class="form-control" id="newStock" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Threshold Quantity</label>
                        <input type="number" class="form-control" id="thresholdQuantity" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Expiry Date</label>
                        <input type="date" class="form-control" id="expiryDate">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Batch Number</label>
                        <input type="text" class="form-control" id="batchNumber">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveStockUpdate()">Update Stock</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentInventoryId = null;

    function filterByMachine() {
        const machineId = document.getElementById('machineFilter').value;
        const url = new URL(window.location);
        if (machineId) {
            url.searchParams.set('machine_id', machineId);
        } else {
            url.searchParams.delete('machine_id');
        }
        window.location = url;
    }

    function updateStock(inventoryId) {
        currentInventoryId = inventoryId;
        
        // Get current inventory data via AJAX
        $.ajax({
            url: `/api/v2/vending/inventory/${inventoryId}`,
            method: 'GET',
            success: function(response) {
                const inventory = response.data;
                $('#currentStock').val(inventory.stock_quantity);
                $('#newStock').val(inventory.stock_quantity);
                $('#thresholdQuantity').val(inventory.threshold_quantity || '');
                $('#expiryDate').val(inventory.expiry_date || '');
                $('#batchNumber').val(inventory.batch_number || '');
                
                $('#updateStockModal').modal('show');
            },
            error: function() {
                Swal.fire('Error', 'Failed to load inventory data', 'error');
            }
        });
    }

    function saveStockUpdate() {
        if (!currentInventoryId) return;

        const formData = {
            stock_quantity: parseInt($('#newStock').val()),
            threshold_quantity: parseInt($('#thresholdQuantity').val()) || null,
            expiry_date: $('#expiryDate').val() || null,
            batch_number: $('#batchNumber').val() || null
        };

        $.ajax({
            url: `/api/v2/vending/inventory/${currentInventoryId}`,
            method: 'PUT',
            data: formData,
            success: function(response) {
                $('#updateStockModal').modal('hide');
                Swal.fire({
                    title: 'Success!',
                    text: 'Stock updated successfully',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors || {};
                let errorMsg = 'Failed to update stock';
                if (Object.keys(errors).length > 0) {
                    errorMsg = Object.values(errors).flat().join(', ');
                }
                Swal.fire('Error', errorMsg, 'error');
            }
        });
    }

    function restockItem(inventoryId) {
        Swal.fire({
            title: 'Restock Item',
            text: 'Enter the quantity to add:',
            input: 'number',
            inputAttributes: {
                min: 1,
                step: 1
            },
            showCancelButton: true,
            confirmButtonText: 'Add Stock',
            preConfirm: (quantity) => {
                if (!quantity || quantity <= 0) {
                    Swal.showValidationMessage('Please enter a valid quantity');
                }
                return quantity;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Get current stock and add the new quantity
                $.ajax({
                    url: `/api/v2/vending/inventory/${inventoryId}`,
                    method: 'GET',
                    success: function(response) {
                        const newStock = parseInt(response.data.stock_quantity) + parseInt(result.value);
                        
                        $.ajax({
                            url: `/api/v2/vending/inventory/${inventoryId}`,
                            method: 'PUT',
                            data: { stock_quantity: newStock },
                            success: function() {
                                Swal.fire({
                                    title: 'Success!',
                                    text: `Added ${result.value} items to stock`,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function() {
                                Swal.fire('Error', 'Failed to update stock', 'error');
                            }
                        });
                    }
                });
            }
        });
    }

    function viewHistory(inventoryId) {
        Swal.fire({
            title: 'Dispensing History',
            html: `
                <div class="text-start">
                    <p><i class="fas fa-spinner fa-spin"></i> Loading dispensing history...</p>
                </div>
            `,
            width: 600,
            showCloseButton: true,
            showConfirmButton: false
        });
        
        // Simulate loading history (replace with actual API call)
        setTimeout(() => {
            Swal.update({
                html: `
                    <div class="text-start">
                        <div class="list-group">
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <span>Dispensed 1 unit</span>
                                    <small>2 hours ago</small>
                                </div>
                                <small class="text-muted">Invoice #12345</small>
                            </div>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <span>Restocked +20 units</span>
                                    <small>1 day ago</small>
                                </div>
                                <small class="text-muted">Admin action</small>
                            </div>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <span>Dispensed 1 unit</span>
                                    <small>2 days ago</small>
                                </div>
                                <small class="text-muted">Invoice #12344</small>
                            </div>
                        </div>
                    </div>
                `
            });
        }, 1000);
    }
</script>
@endpush