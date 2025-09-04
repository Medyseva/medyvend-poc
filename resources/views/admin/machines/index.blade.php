@extends('layouts.admin')

@section('title', 'Vending Machines - Vending POC')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="fas fa-robot me-2"></i>
        Vending Machines
    </h1>
    <a href="{{ route('admin.machines.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>
        Add New Machine
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Machine Name</th>
                        <th>Machine Number</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Last Ping</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($machines as $machine)
                    <tr>
                        <td>{{ $machine->id }}</td>
                        <td>
                            <strong>{{ $machine->machine_name }}</strong>
                            <br>
                            <small class="text-muted">{{ $machine->machine_id }}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $machine->machine_num }}</span>
                        </td>
                        <td>{{ $machine->machine_type ?? 'Standard' }}</td>
                        <td>
                            @if($machine->machine_lat && $machine->machine_long)
                                <i class="fas fa-map-marker-alt text-primary"></i>
                                {{ $machine->machine_lat }}, {{ $machine->machine_long }}
                            @else
                                <span class="text-muted">Not set</span>
                            @endif
                        </td>
                        <td>
                            @if($machine->machine_is_active)
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle"></i> Active
                                </span>
                            @else
                                <span class="badge bg-warning">
                                    <i class="fas fa-exclamation-circle"></i> Inactive
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($machine->machine_last_ping)
                                {{ $machine->machine_last_ping->diffForHumans() }}
                            @else
                                <span class="text-muted">Never</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.machines.show', $machine->id) }}" 
                                   class="btn btn-sm btn-outline-info" 
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.machines.edit', $machine->id) }}" 
                                   class="btn btn-sm btn-outline-warning" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-success" 
                                        onclick="testMachine({{ $machine->id }}, '{{ $machine->machine_num }}')" 
                                        title="Test Connection">
                                    <i class="fas fa-plug"></i>
                                </button>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger" 
                                        onclick="resetMachine({{ $machine->id }}, '{{ $machine->machine_num }}')" 
                                        title="Hard Reset">
                                    <i class="fas fa-power-off"></i>
                                </button>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger" 
                                        onclick="confirmDelete('{{ route('admin.machines.destroy', $machine->id) }}')" 
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            <div class="py-4">
                                <i class="fas fa-robot fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No vending machines found</h5>
                                <p class="text-muted">Add your first vending machine to get started.</p>
                                <a href="{{ route('admin.machines.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>
                                    Add Vending Machine
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

<!-- Machine Status Modal -->
<div class="modal fade" id="machineStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-robot me-2"></i>
                    Machine Status
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="machineStatusContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function testMachine(machineId, machineNum) {
        Swal.fire({
            title: 'Testing Connection',
            text: `Testing connection to Machine #${machineNum}...`,
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '/api/v2/vending/machine-details',
            method: 'POST',
            data: {
                machine_num: machineNum
            },
            success: function(response) {
                Swal.fire({
                    title: 'Connection Successful',
                    text: `Machine #${machineNum} is online and responding.`,
                    icon: 'success',
                    timer: 3000
                });
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Connection Failed',
                    text: `Unable to connect to Machine #${machineNum}. Please check the machine status.`,
                    icon: 'error'
                });
            }
        });
    }

    function resetMachine(machineId, machineNum) {
        Swal.fire({
            title: 'Hard Reset Machine',
            text: `Are you sure you want to perform a hard reset on Machine #${machineNum}? This will restart the machine.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, reset it!'
        }).then((result) => {
            if (result.isConfirmed) {
                performHardReset(machineNum);
            }
        });
    }

    function performHardReset(machineNum) {
        Swal.fire({
            title: 'Performing Hard Reset',
            text: `Resetting Machine #${machineNum}...`,
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '/api/v2/vending/hard-reset',
            method: 'POST',
            data: {
                machine_num: machineNum
            },
            success: function(response) {
                Swal.fire({
                    title: 'Reset Complete',
                    text: `Machine #${machineNum} has been reset successfully.`,
                    icon: 'success',
                    timer: 3000
                });
                
                // Refresh the page after 3 seconds
                setTimeout(() => {
                    location.reload();
                }, 3000);
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Reset Failed',
                    text: `Failed to reset Machine #${machineNum}. Please try again.`,
                    icon: 'error'
                });
            }
        });
    }

    // Refresh machine status every 30 seconds
    setInterval(function() {
        // This would make an AJAX call to refresh machine ping times
        console.log('Refreshing machine status...');
    }, 30000);
</script>
@endpush