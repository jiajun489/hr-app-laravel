@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Employee Wellbeing: {{ $employee->fullname }}</h3>
                <p class="text-subtitle text-muted">Detailed wellbeing analysis and metrics</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('employee.wellbeing.index') }}">Wellbeing Analytics</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $employee->fullname }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="page-content">
    <!-- Action Buttons -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-end gap-2">
                <form action="{{ route('employee.wellbeing.analyze', $employee->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-arrow-clockwise"></i> Run New Analysis
                    </button>
                </form>
                <a href="{{ route('employee.wellbeing.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Employee Information Cards -->
    <div class="row">
        <div class="col-lg-4 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Employee Information</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-xl">
                            <img src="{{ asset('mazer/assets/compiled/jpg/1.jpg') }}" alt="Avatar">
                        </div>
                        <div class="ms-3">
                            <h5 class="mb-0">{{ $employee->fullname }}</h5>
                            <p class="text-muted mb-0">{{ $employee->email }}</p>
                        </div>
                    </div>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="fw-bold">Department:</span>
                            <span>{{ $employee->department->name ?? 'N/A' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="fw-bold">Role:</span>
                            <span>{{ $employee->role->name ?? 'N/A' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="fw-bold">Hire Date:</span>
                            <span>{{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Work-Life Balance Metrics</h4>
                </div>
                <div class="card-body">
                    @if($workLifeMetrics->isNotEmpty())
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="fw-bold">Recent Overtime:</span>
                                <span class="badge bg-warning">{{ $workLifeMetrics->first()->overtime_hours ?? 0 }} hours</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="fw-bold">Consecutive Work Days:</span>
                                <span class="badge bg-info">{{ $workLifeMetrics->first()->consecutive_work_days ?? 0 }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="fw-bold">Leave Balance Ratio:</span>
                                <span class="badge bg-success">{{ $workLifeMetrics->first()->leave_balance_ratio ?? 'N/A' }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="fw-bold">Week Starting:</span>
                                <span>{{ $workLifeMetrics->first()->week_start ? $workLifeMetrics->first()->week_start->format('Y-m-d') : 'N/A' }}</span>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-light">
                            <i class="bi bi-info-circle"></i> No work-life balance metrics available
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Risk Assessment</h4>
                </div>
                <div class="card-body">
                    @if($latestAnalysis)
                        <div class="text-center mb-3">
                            @php
                                $riskLevel = $latestAnalysis->risk_level;
                                $badgeClass = match($riskLevel) {
                                    'high' => 'bg-danger',
                                    'medium' => 'bg-warning',
                                    'low' => 'bg-success',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} fs-6 px-3 py-2">
                                {{ ucfirst($riskLevel) }} Risk
                            </span>
                        </div>
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="fw-bold">Risk Score:</span>
                                <span>{{ $latestAnalysis->risk_score }}/100</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="fw-bold">Categories:</span>
                                <span>{{ $latestAnalysis->risk_categories }}</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="fw-bold">Last Analysis:</span>
                                <span>{{ $latestAnalysis->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-light text-center">
                            <i class="bi bi-exclamation-triangle"></i>
                            <p class="mb-0">No risk assessment available</p>
                            <small class="text-muted">Run an analysis to get started</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Analysis History -->
    @if($analysisHistory->isNotEmpty())
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Analysis History</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="analysisHistoryTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Risk Level</th>
                                    <th>Risk Score</th>
                                    <th>Categories</th>
                                    <th>Recommendations</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analysisHistory as $analysis)
                                <tr>
                                    <td>{{ $analysis->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match($analysis->risk_level) {
                                                'high' => 'bg-danger',
                                                'medium' => 'bg-warning',
                                                'low' => 'bg-success',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ ucfirst($analysis->risk_level) }}</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar {{ $badgeClass }}" role="progressbar" 
                                                 style="width: {{ $analysis->risk_score }}%" 
                                                 aria-valuenow="{{ $analysis->risk_score }}" 
                                                 aria-valuemin="0" aria-valuemax="100">
                                                {{ $analysis->risk_score }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $analysis->risk_categories }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#recommendationsModal{{ $analysis->id }}">
                                            <i class="bi bi-eye"></i> View
                                        </button>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#feedbackModal{{ $analysis->id }}">
                                            <i class="bi bi-chat-dots"></i> Feedback
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Presence Data -->
    @if($recentPresences->isNotEmpty())
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Recent Attendance (Last 30 days)</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="presenceTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Work Hours</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPresences as $presence)
                                <tr>
                                    <td>{{ $presence->date->format('Y-m-d') }}</td>
                                    <td>{{ $presence->check_in ? $presence->check_in->format('H:i') : '-' }}</td>
                                    <td>{{ $presence->check_out ? $presence->check_out->format('H:i') : '-' }}</td>
                                    <td>
                                        @if($presence->check_in && $presence->check_out)
                                            @php
                                                $diffInHours = $presence->check_in->diffInHours($presence->check_out);
                                                $diffInMinutes = $presence->check_in->diffInMinutes($presence->check_out) % 60;
                                            @endphp
                                            {{ $diffInHours }}h {{ $diffInMinutes }}m
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($presence->check_in && $presence->check_out)
                                            <span class="badge bg-success">Complete</span>
                                        @elseif($presence->check_in)
                                            <span class="badge bg-warning">Incomplete</span>
                                        @else
                                            <span class="badge bg-danger">Absent</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Modals for Recommendations and Feedback -->
@if($analysisHistory->isNotEmpty())
    @foreach($analysisHistory as $analysis)
        <!-- Recommendations Modal -->
        <div class="modal fade" id="recommendationsModal{{ $analysis->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Recommendations - {{ $analysis->created_at->format('Y-m-d') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <h6>Risk Level: <span class="badge {{ match($analysis->risk_level) { 'high' => 'bg-danger', 'medium' => 'bg-warning', 'low' => 'bg-success', default => 'bg-secondary' } }}">{{ ucfirst($analysis->risk_level) }}</span></h6>
                            <p><strong>Categories:</strong> {{ $analysis->risk_categories }}</p>
                        </div>
                        <div class="recommendations">
                            {!! nl2br(e($analysis->recommendations)) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feedback Modal -->
        <div class="modal fade" id="feedbackModal{{ $analysis->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Feedback</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('employee.wellbeing.feedback', $analysis->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="feedback{{ $analysis->id }}">Feedback:</label>
                                <textarea class="form-control" id="feedback{{ $analysis->id }}" name="feedback" rows="4" 
                                          placeholder="Add your feedback about this analysis...">{{ $analysis->hr_feedback }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Feedback</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endif

@push('scripts')
<script src="{{ asset('mazer/assets/extensions/simple-datatables/umd/simple-datatables.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('analysisHistoryTable')) {
            let analysisTable = new simpleDatatables.DataTable("#analysisHistoryTable");
        }
        if (document.getElementById('presenceTable')) {
            let presenceTable = new simpleDatatables.DataTable("#presenceTable");
        }
    });
</script>
@endpush
@endsection
