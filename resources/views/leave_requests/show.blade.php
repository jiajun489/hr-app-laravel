<!-- resources/views/leave_requests/show.blade.php -->

@extends('layouts.dashboard')

@section('content')
<header class="mb-3">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>
</header>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>View Leave Request</h3>
                <p class="text-subtitle text-muted">Detailed information of the selected leave request</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('leave_requests.index') }}">Leave Requests</a></li>
                        <li class="breadcrumb-item active" aria-current="page">View</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Leave Request Detail</h5>
            </div>

            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Employee Name</dt>
                    <dd class="col-sm-9">{{ $leave_request->employee->fullname ?? '-' }}</dd>

                    <dt class="col-sm-3">Leave Type</dt>
                    <dd class="col-sm-9">{{ ucfirst($leave_request->leave_type) }}</dd>

                    <dt class="col-sm-3">Start Date</dt>
                    <dd class="col-sm-9">{{ \Carbon\Carbon::parse($leave_request->start_date)->format('d M Y') }}</dd>

                    <dt class="col-sm-3">End Date</dt>
                    <dd class="col-sm-9">{{ \Carbon\Carbon::parse($leave_request->end_date)->format('d M Y') }}</dd>

                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9">
                        @if ($leave_request->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif ($leave_request->status === 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @elseif ($leave_request->status === 'pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($leave_request->status) }}</span>
                        @endif
                    </dd>
                </dl>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('leave_requests.index') }}" class="btn btn-secondary">Back to Leave Request List</a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
