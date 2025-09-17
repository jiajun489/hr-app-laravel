@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <h3>My Work-Life Balance</h3>
</div>

<div class="page-content">
    <section class="row">
        <div class="col-12 col-lg-3">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon {{ $employee->getWorkLifeBalanceStatus()['color'] }} mb-2">
                                <i class="iconly-boldHeart"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Work-Life Score</h6>
                            <h6 class="font-extrabold mb-0">{{ number_format($employee->calculateWorkLifeScore(), 1) }}/10</h6>
                            <small class="text-{{ $employee->getWorkLifeBalanceStatus()['color'] }}">{{ $employee->getWorkLifeBalanceStatus()['status'] }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-lg-3">
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
        
        <div class="col-12 col-lg-3">
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
        
        <div class="col-12 col-lg-3">
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
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4>Work-Life Balance Trends</h4>
                </div>
                <div class="card-body">
                    <canvas id="workLifeScoreChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4>Weekly Overtime Hours</h4>
                </div>
                <div class="card-body">
                    <canvas id="overtimeTrendChart"></canvas>
                </div>
            </div>
        </div>
    </section>
    
    <section class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Work-Life Balance Insights</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Average Work Hours/Day</h6>
                            <p class="text-muted">{{ number_format($employee->getAverageWorkHoursPerDay(), 1) }} hours</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Leave Usage Rate</h6>
                            <p class="text-muted">{{ number_format($employee->getLeaveBalanceRatio() * 100, 1) }}% of annual leave used</p>
                        </div>
                    </div>
                    
                    @php
                        $score = $employee->calculateWorkLifeScore();
                        $recommendations = [];
                        
                        if ($employee->getWeeklyOvertimeHours(now()->startOfWeek()) > 10) {
                            $recommendations[] = "Consider reducing overtime hours to improve work-life balance.";
                        }
                        
                        if ($employee->getConsecutiveWorkDays() > 7) {
                            $recommendations[] = "Take regular breaks - you've worked " . $employee->getConsecutiveWorkDays() . " consecutive days.";
                        }
                        
                        if ($employee->getLeaveBalanceRatio() < 0.3) {
                            $recommendations[] = "Consider taking more leave days to recharge and maintain productivity.";
                        }
                        
                        if (empty($recommendations)) {
                            $recommendations[] = "Great job maintaining a healthy work-life balance!";
                        }
                    @endphp
                    
                    <h6 class="mt-3">Recommendations</h6>
                    <ul class="list-unstyled">
                        @foreach($recommendations as $recommendation)
                            <li class="mb-2">
                                <i class="bi bi-lightbulb text-warning me-2"></i>
                                {{ $recommendation }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Work-Life Score Chart
const scoreCtx = document.getElementById('workLifeScoreChart').getContext('2d');
new Chart(scoreCtx, {
    type: 'line',
    data: {
        labels: @json($weeklyMetrics->pluck('week_start')->map(fn($date) => $date->format('M d'))),
        datasets: [{
            label: 'Work-Life Score',
            data: @json($weeklyMetrics->pluck('work_life_score')),
            borderColor: 'rgb(54, 162, 235)',
            backgroundColor: 'rgba(54, 162, 235, 0.1)',
            tension: 0.1,
            fill: true
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                max: 10
            }
        }
    }
});

// Overtime Trend Chart
const overtimeCtx = document.getElementById('overtimeTrendChart').getContext('2d');
new Chart(overtimeCtx, {
    type: 'bar',
    data: {
        labels: @json($weeklyMetrics->pluck('week_start')->map(fn($date) => $date->format('M d'))),
        datasets: [{
            label: 'Overtime Hours',
            data: @json($weeklyMetrics->pluck('overtime_hours')),
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
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