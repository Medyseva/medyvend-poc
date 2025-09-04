@extends('layouts.admin')

@section('title', 'Dashboard - Vending POC')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="fas fa-tachometer-alt me-2"></i>
        Dashboard
    </h1>
</div>

<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1">Total Machines</h6>
                        <h2 class="mb-0" id="totalMachines">{{ $totalMachines ?? 0 }}</h2>
                    </div>
                    <div class="text-primary-50">
                        <i class="fas fa-robot fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1">Active Machines</h6>
                        <h2 class="mb-0" id="activeMachines">{{ $activeMachines ?? 0 }}</h2>
                    </div>
                    <div class="text-success-50">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1">Low Stock Items</h6>
                        <h2 class="mb-0" id="lowStockItems">{{ $lowStockItems ?? 0 }}</h2>
                    </div>
                    <div class="text-warning-50">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1">Today's Dispensing</h6>
                        <h2 class="mb-0" id="todaysDispensing">{{ $todaysDispensing ?? 0 }}</h2>
                    </div>
                    <div class="text-info-50">
                        <i class="fas fa-pills fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Dispensing Activity (Last 7 Days)
                </h5>
            </div>
            <div class="card-body">
                <canvas id="dispensingChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Alerts & Notifications
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning alert-sm" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>5 items</strong> are running low on stock
                </div>
                <div class="alert alert-info alert-sm" role="alert">
                    <i class="fas fa-calendar-times me-2"></i>
                    <strong>2 items</strong> expiring within 30 days
                </div>
                <div class="alert alert-danger alert-sm" role="alert">
                    <i class="fas fa-robot me-2"></i>
                    <strong>Machine #001</strong> offline for 2 hours
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>
                    Recent Dispensing Records
                </h5>
                <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Medicine</th>
                                <th>Machine</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>10:30 AM</td>
                                <td>Paracetamol</td>
                                <td>Machine #001</td>
                                <td><span class="status-badge status-successful">Success</span></td>
                            </tr>
                            <tr>
                                <td>10:15 AM</td>
                                <td>Ibuprofen</td>
                                <td>Machine #002</td>
                                <td><span class="status-badge status-processing">Processing</span></td>
                            </tr>
                            <tr>
                                <td>09:45 AM</td>
                                <td>Acetaminophen</td>
                                <td>Machine #001</td>
                                <td><span class="status-badge status-successful">Success</span></td>
                            </tr>
                            <tr>
                                <td>09:30 AM</td>
                                <td>Naproxen</td>
                                <td>Machine #003</td>
                                <td><span class="status-badge status-failed">Failed</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-robot me-2"></i>
                    Machine Status
                </h5>
                <button class="btn btn-sm btn-outline-success" onclick="refreshMachineStatus()">
                    <i class="fas fa-sync"></i> Refresh
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-circle text-success"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Machine #001</h6>
                                <small class="text-muted">Online - Last ping: 2 min ago</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-circle text-success"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Machine #002</h6>
                                <small class="text-muted">Online - Last ping: 1 min ago</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-circle text-danger"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Machine #003</h6>
                                <small class="text-muted">Offline - Last ping: 2 hours ago</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-circle text-warning"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Machine #004</h6>
                                <small class="text-muted">Maintenance - Last ping: 30 min ago</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Dispensing Chart
    const ctx = document.getElementById('dispensingChart').getContext('2d');
    const dispensingChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Successful Dispensing',
                data: [12, 19, 15, 8, 20, 25, 18],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Failed Dispensing',
                data: [2, 3, 1, 0, 2, 1, 1],
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Refresh machine status
    function refreshMachineStatus() {
        // Simulate API call to refresh machine status
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
        btn.disabled = true;

        setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            
            Swal.fire({
                title: 'Status Updated',
                text: 'Machine status has been refreshed successfully.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        }, 2000);
    }

    // Auto-refresh dashboard data every 30 seconds
    setInterval(function() {
        // This would make AJAX calls to refresh dashboard statistics
        console.log('Refreshing dashboard data...');
    }, 30000);
</script>
@endpush