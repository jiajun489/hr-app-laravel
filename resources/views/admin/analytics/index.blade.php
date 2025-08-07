@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <h3>HR Analytics Dashboard</h3>
</div>

<div class="page-content">
    <section class="row">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4>Employee Workload</h4>
                </div>
                <div class="card-body">
                    <canvas id="workloadChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4>Task Bottlenecks</h4>
                </div>
                <div class="card-body">
                    <canvas id="bottleneckChart"></canvas>
                </div>
            </div>
        </div>
    </section>
    
    <section class="row">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4>Productivity vs Punctuality</h4>
                </div>
                <div class="card-body">
                    <canvas id="productivityChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4>Overdue Tasks</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Overdue Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analytics['overdue'] as $overdue)
                                <tr>
                                    <td>{{ $overdue['employee_name'] }}</td>
                                    <td><span class="badge bg-danger">{{ $overdue['overdue_count'] }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Workload Chart
    const workloadCanvas = document.getElementById('workloadChart');
    if (workloadCanvas) {
        const workloadCtx = workloadCanvas.getContext('2d');
        new Chart(workloadCtx, {
            type: 'bar',
            data: {
                labels: @json(collect($analytics['workload'])->pluck('employee_name')),
                datasets: [{
                    label: 'Active Tasks',
                    data: @json(collect($analytics['workload'])->pluck('active_tasks')),
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Bottleneck Chart
    const bottleneckCanvas = document.getElementById('bottleneckChart');
    if (bottleneckCanvas) {
        const bottleneckCtx = bottleneckCanvas.getContext('2d');
        new Chart(bottleneckCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'In Progress', 'Completed'],
                datasets: [{
                    data: [
                        {{ $analytics['bottlenecks']['pending'] }},
                        {{ $analytics['bottlenecks']['in_progress'] }},
                        {{ $analytics['bottlenecks']['completed'] }}
                    ],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            }
        });
    }

    // Productivity Chart
    const productivityCanvas = document.getElementById('productivityChart');
    if (productivityCanvas) {
        const productivityCtx = productivityCanvas.getContext('2d');
        new Chart(productivityCtx, {
            type: 'scatter',
            data: {
                datasets: [{
                    label: 'Employees',
                    data: @json(collect($analytics['productivity'])->map(function($item) {
                        return ['x' => $item['lateness_rate'], 'y' => $item['completion_rate']];
                    })),
                    backgroundColor: 'rgba(255, 99, 132, 0.6)'
                }]
            },
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Lateness Rate (%)'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Task Completion Rate (%)'
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection