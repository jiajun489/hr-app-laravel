<!-- resources/views/presences/show.blade.php -->

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
                <h3>View Presence</h3>
                <p class="text-subtitle text-muted">Detailed record of employee presence</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('presences.index') }}">Presences</a></li>
                        <li class="breadcrumb-item active" aria-current="page">View</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>


    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Presence Detail</h5>
            </div>

            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Employee</dt>
                    <dd class="col-sm-9">{{ $presence->employee->fullname ?? '-' }}</dd>

                    <dt class="col-sm-3">Date</dt>
                    <dd class="col-sm-9">{{ \Carbon\Carbon::parse($presence->date)->format('d M Y') }}</dd>

                    <dt class="col-sm-3">Check In</dt>
                    <dd class="col-sm-9">{{ \Carbon\Carbon::parse($presence->check_in)->format('H:i:s') }}</dd>

                    <dt class="col-sm-3">Check Out</dt>
                    <dd class="col-sm-9">
                        @if ($presence->check_out)
                            {{ \Carbon\Carbon::parse($presence->check_out)->format('H:i:s') }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </dd>
                    
                    <dt class="col-sm-3">Latitude</dt>
                    <dd class="col-sm-9">{{ $presence->latitude ?? '-' }}</dd>

                    <dt class="col-sm-3">Longitude</dt>
                    <dd class="col-sm-9">{{ $presence->longitude ?? '-' }}</dd>

                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9">
                        @if ($presence->status === 'present')
                            <span class="badge bg-success">Present</span>
                        @elseif ($presence->status === 'absent')
                            <span class="badge bg-danger">Absent</span>
                        @elseif ($presence->status === 'late')
                            <span class="badge bg-warning text-dark">Late</span>
                        @elseif ($presence->status === 'leave')
                            <span class="badge bg-info">Leave</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($presence->status) }}</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Location Map</dt>
                    <dd class="col-sm-9">
                        @if ($presence->latitude && $presence->longitude)
                            <iframe
                                width="400"
                                height="250"
                                style="border:0"
                                loading="lazy"
                                allowfullscreen
                                referrerpolicy="no-referrer-when-downgrade"
                                src="https://www.google.com/maps?q={{ $presence->latitude }},{{ $presence->longitude }}&z=17&output=embed">
                            </iframe>
                        @else
                            <span class="text-muted">No location recorded</span>
                        @endif
                    </dd>
                </dl>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('presences.index') }}" class="btn btn-secondary">Back to Presence List</a>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
