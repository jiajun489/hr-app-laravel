@extends('layouts.dashboard')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Employee Wellbeing Analytics</h3>
                <p class="text-subtitle text-muted">Monitor and analyze employee wellbeing metrics</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Wellbeing Analytics</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="page-content">
    <!-- Statistics Summary -->
    <div class="row">
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon red mb-2">
                                <i class="iconly-boldDanger"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">High Risk</h6>
                            <h6 class="font-extrabold mb-0">{{ $highRisk->count() }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon yellow mb-2">
                                <i class="iconly-boldInfo-Square"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Medium Risk</h6>
                            <h6 class="font-extrabold mb-0">{{ $mediumRisk->count() }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon green mb-2">
                                <i class="iconly-boldTick-Square"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Low Risk</h6>
                            <h6 class="font-extrabold mb-0">{{ $lowRisk->count() }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon purple mb-2">
                                <i class="iconly-boldProfile"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Not Analyzed</h6>
                            <h6 class="font-extrabold mb-0">{{ $unknown->count() }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- High Risk Employees -->
    @if($highRisk->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-danger">
                    <h4 class="card-title text-white">High Risk Employees</h4>
                    <p class="text-white-50 mb-0">These employees may need immediate attention</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="highRiskTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Risk Categories</th>
                                    <th>Last Analysis</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($highRisk as $employee)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-md">
                                                <img src="{{ asset('mazer/assets/compiled/jpg/1.jpg') }}" alt="Avatar">
                                            </div>
                                            <div class="ms-3">
                                                <h6 class="mb-0">{{ $employee['name'] }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $employee['department'] }}</td>
                                    <td>
                                        <span class="badge bg-danger">{{ $employee['categories'] }}</span>
                                    </td>
                                    <td>{{ $employee['last_analysis'] }}</td>
                                    <td>
                                        <a href="{{ route('employee.wellbeing', $employee['id']) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i> View Details
                                        </a>
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

    <!-- Medium Risk Employees -->
    @if($mediumRisk->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning">
                    <h4 class="card-title text-white">Medium Risk Employees</h4>
                    <p class="text-white-50 mb-0">These employees may need monitoring</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="mediumRiskTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Risk Categories</th>
                                    <th>Last Analysis</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mediumRisk as $employee)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-md">
                                                <img src="{{ asset('mazer/assets/compiled/jpg/2.jpg') }}" alt="Avatar">
                                            </div>
                                            <div class="ms-3">
                                                <h6 class="mb-0">{{ $employee['name'] }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $employee['department'] }}</td>
                                    <td>
                                        <span class="badge bg-warning">{{ $employee['categories'] }}</span>
                                    </td>
                                    <td>{{ $employee['last_analysis'] }}</td>
                                    <td>
                                        <a href="{{ route('employee.wellbeing', $employee['id']) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i> View Details
                                        </a>
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

    <!-- Low Risk and Not Analyzed Employees -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="low-risk-tab" data-bs-toggle="tab" href="#low-risk" role="tab" aria-controls="low-risk" aria-selected="true">
                                <i class="bi bi-check-circle"></i> Low Risk ({{ $lowRisk->count() }})
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="not-analyzed-tab" data-bs-toggle="tab" href="#not-analyzed" role="tab" aria-controls="not-analyzed" aria-selected="false">
                                <i class="bi bi-question-circle"></i> Not Analyzed ({{ $unknown->count() }})
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <!-- Low Risk Tab -->
                        <div class="tab-pane fade show active" id="low-risk" role="tabpanel" aria-labelledby="low-risk-tab">
                            <div class="table-responsive mt-3">
                                <table class="table table-striped" id="lowRiskTable">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Department</th>
                                            <th>Last Analysis</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lowRisk as $employee)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('mazer/assets/compiled/jpg/3.jpg') }}" alt="Avatar">
                                                    </div>
                                                    <div class="ms-3">
                                                        <h6 class="mb-0">{{ $employee['name'] }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $employee['department'] }}</td>
                                            <td>{{ $employee['last_analysis'] }}</td>
                                            <td>
                                                <a href="{{ route('employee.wellbeing', $employee['id']) }}" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i> View Details
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Not Analyzed Tab -->
                        <div class="tab-pane fade" id="not-analyzed" role="tabpanel" aria-labelledby="not-analyzed-tab">
                            <div class="table-responsive mt-3">
                                <table class="table table-striped" id="notAnalyzedTable">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Department</th>
                                            <th>Presence Count</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($unknown as $employee)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-md">
                                                        <img src="{{ asset('mazer/assets/compiled/jpg/4.jpg') }}" alt="Avatar">
                                                    </div>
                                                    <div class="ms-3">
                                                        <h6 class="mb-0">{{ $employee['name'] }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $employee['department'] }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $employee['presences_count'] ?? 0 }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('employee.wellbeing', $employee['id']) }}" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i> View Details
                                                </a>
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
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('mazer/assets/extensions/simple-datatables/umd/simple-datatables.js') }}"></script>
<script>
    // Initialize DataTables for all tables
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('highRiskTable')) {
            let highRiskTable = new simpleDatatables.DataTable("#highRiskTable");
        }
        if (document.getElementById('mediumRiskTable')) {
            let mediumRiskTable = new simpleDatatables.DataTable("#mediumRiskTable");
        }
        if (document.getElementById('lowRiskTable')) {
            let lowRiskTable = new simpleDatatables.DataTable("#lowRiskTable");
        }
        if (document.getElementById('notAnalyzedTable')) {
            let notAnalyzedTable = new simpleDatatables.DataTable("#notAnalyzedTable");
        }
    });
</script>
@endpush
@endsection
