@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <h3>Team Work-Life Balance</h3>
</div>

<div class="page-content">
    @if($alertEmployees->count() > 0)
    <section class="row">
        <div class="col-12">
            <div class="alert alert-warning">
                <h4 class="alert-heading">Overtime Alerts!</h4>
                <p>The following employees have worked excessive overtime in the past 2 weeks:</p>
                <ul>
                    @foreach($alertEmployees as $employee)
                    <li>{{ $employee->fullname }} - {{ $employee->workLifeBalanceMetrics()->where('week_start', '>=', now()->subWeeks(2)->startOfWeek())->sum('overtime_hours') }} hours</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>
    @endif
    
    <section class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Direct Reports Work-Life Balance</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>This Week Overtime</th>
                                    <th>Consecutive Days</th>
                                    <th>Leave Usage</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($directReports as $employee)
                                @php
                                    $thisWeekOvertime = $employee->getWeeklyOvertimeHours(now()->startOfWeek());
                                    $consecutiveDays = $employee->getConsecutiveWorkDays();
                                    $leaveUsage = $employee->getLeaveBalanceRatio() * 100;
                                    
                                    $status = 'success';
                                    if ($thisWeekOvertime > 10 || $consecutiveDays > 10) $status = 'warning';
                                    if ($thisWeekOvertime > 20 || $consecutiveDays > 15) $status = 'danger';
                                @endphp
                                <tr>
                                    <td>{{ $employee->fullname }}</td>
                                    <td>{{ $thisWeekOvertime }}h</td>
                                    <td>{{ $consecutiveDays }} days</td>
                                    <td>{{ number_format($leaveUsage, 1) }}%</td>
                                    <td>
                                        <span class="badge bg-{{ $status }}">
                                            @if($status === 'success') Good
                                            @elseif($status === 'warning') Monitor
                                            @else Alert
                                            @endif
                                        </span>
                                    </td>
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
@endsection