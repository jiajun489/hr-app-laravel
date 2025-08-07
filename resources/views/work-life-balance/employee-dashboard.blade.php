@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <h3>My Work-Life Balance</h3>
</div>

<div class="page-content">
    <section class="row">
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon purple mb-2">
                                <i class="iconly-boldCalendar"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Remaining Leave Days</h6>
                            <h6 class="font-extrabold mb-0">{{ number_format($remainingLeave, 1) }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon blue mb-2">
                                <i class="iconly-boldWork"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Consecutive Work Days</h6>
                            <h6 class="font-extrabold mb-0">{{ $employee->getConsecutiveWorkDays() }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon green mb-2">
                                <i class="iconly-boldTime-Circle"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">This Week Overtime</h6>
                            <h6 class="font-extrabold mb-0">{{ $employee->getWeeklyOvertimeHours(now()->startOfWeek()) }}h</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Weekly Overtime Trends (Last 12 Weeks)</h4>
                </div>
                <div class="card-body">
                    <canvas id="overtimeTrendChart"></canvas>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('overtimeTrendChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($weeklyMetrics->pluck('week_start')->map(fn($date) => $date->format('M d'))),
        datasets: [{
            label: 'Overtime Hours',
            data: @json($weeklyMetrics->pluck('overtime_hours')),
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
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