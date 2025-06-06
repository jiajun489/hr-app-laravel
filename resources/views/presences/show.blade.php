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

    <section class="section presence-section">
        <div class="d-flex justify-content-center">
            <div class="card presence-detail-card w-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Presence Detail</h5>
                </div>
                <div class="card-body">
                    <dl class="row presence-detail-dl">
                        <dt class="col-sm-4">Employee</dt>
                        <dd class="col-sm-8">{{ $presence->employee->fullname ?? '-' }}</dd>
                        <dt class="col-sm-4">Date</dt>
                        <dd class="col-sm-8">{{ \Carbon\Carbon::parse($presence->date)->format('d M Y') }}</dd>
                        <dt class="col-sm-4">Check In</dt>
                        <dd class="col-sm-8">{{ \Carbon\Carbon::parse($presence->check_in)->format('H:i:s') }}</dd>
                        <dt class="col-sm-4">Check Out</dt>
                        <dd class="col-sm-8">
                            @if ($presence->check_out)
                                {{ \Carbon\Carbon::parse($presence->check_out)->format('H:i:s') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </dd>
                        <dt class="col-sm-4">Latitude</dt>
                        <dd class="col-sm-8">{{ $presence->latitude ?? '-' }}</dd>
                        <dt class="col-sm-4">Longitude</dt>
                        <dd class="col-sm-8">{{ $presence->longitude ?? '-' }}</dd>
                        <dt class="col-sm-4">Status</dt>
                        <dd class="col-sm-8">
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
                        <dt class="col-sm-4">Location Map</dt>
                        <dd class="col-sm-8">
                            @if ($presence->latitude && $presence->longitude)
                                <div class="map-responsive mb-2">
                                    <iframe
                                        width="100%"
                                        height="220"
                                        style="border:0;"
                                        loading="lazy"
                                        allowfullscreen
                                        referrerpolicy="no-referrer-when-downgrade"
                                        src="https://www.google.com/maps?q={{ $presence->latitude }},{{ $presence->longitude }}&z=17&output=embed">
                                    </iframe>
                                </div>
                            @else
                                <span class="text-muted">No location recorded</span>
                            @endif
                        </dd>
                    </dl>
                    <div class="d-flex justify-content-start">
                        <a href="{{ route('presences.index') }}" class="btn btn-secondary">Back to Presence List</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    /* Margin pada section agar card tidak nempel ke kiri-kanan atas bawah */
    .presence-section {
        padding-top: 18px;
        padding-bottom: 18px;
        padding-left: 0;
        padding-right: 0;
    }
    @media (max-width: 576px) {
        .presence-section {
            padding: 10px 6px 18px 6px;
        }
        .presence-detail-card {
            margin: 0 2px;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(100,120,140,0.08);
        }
        .card-body {
            padding-left: 15px !important;
            padding-right: 15px !important;
        }
        /* Ubah jadi single column di mobile */
        .presence-detail-dl dt,
        .presence-detail-dl dd {
            float: none;
            width: 100%;
            text-align: left;
            margin-bottom: 2px;
            padding: 0;
        }
        .presence-detail-dl dt {
            margin-top: 14px;
            font-weight: 600;
            color: #36475A;
            font-size: 15px;
        }
        .presence-detail-dl dd {
            margin-left: 0;
            margin-bottom: 10px;
            color: #49596c;
        }
        .map-responsive {
            padding-bottom: 60%;
            min-height: 180px;
        }
    }
    .map-responsive {
        overflow: hidden;
        padding-bottom: 56.25%;
        position: relative;
        height: 0;
    }
    .map-responsive iframe {
        left: 0;
        top: 0;
        height: 100% !important;
        width: 100% !important;
        position: absolute;
    }
    /* Max-width default agar mobile tetap aman */
    .presence-detail-card {
        max-width: 520px;
    }

    /* Perbesar khusus untuk desktop viewport */
    @media (min-width: 992px) { /* Bootstrap lg breakpoint (desktop) */
        .presence-detail-card {
            max-width: 950px;  /* Bisa diubah ke 800px/900px sesuai selera */
        }
        .presence-detail-dl dt,
        .presence-detail-dl dd {
            font-size: 18px;    /* Optional: biar tidak terlalu kecil di desktop */
        }
    }

</style>
@endsection
