@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <h3>Work-Life Balance - Admin Dashboard</h3>
</div>

<div class="page-content">
    <section class="row">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4>Top Employees by Overtime (Last 2 Weeks)</h4>
                </div>
                <div class="card-body">
                    <canvas id="overtimeChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4>Low Leave Usage Employees</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Leave Usage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowLeaveUsageEmployees as $employee)
                                <tr>
                                    <td>{{ $employee->fullname }}</td>
                                    <td>{{ $employee->department->name }}</td>
                                    <td>{{ number_format($employee->getLeaveBalanceRatio() * 100, 1) }}%</td>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('overtimeChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json($topOvertimeEmployees->pluck('employee.fullname')),
        datasets: [{
            label: 'Overtime Hours',
            data: @json($topOvertimeEmployees->pluck('total_overtime')),
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
</script>
@endsection