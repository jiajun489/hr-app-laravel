<!--resources/views/presences/index.blade.php-->

@extends('layouts.dashboard')

@section('content')

<header class="mb-3">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>
</header>

@if (session('success')) <div class="alert alert-success alert-dismissible fade show" role="alert"> <strong>Success:</strong> {{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> </div>
@endif

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Presences</h3>
                <p class="text-subtitle text-muted">You can manage employee presences here</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Presences</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Presence List</h5>
                <a href="{{ route('presences.create') }}" class="btn btn-primary btn-sm">New Record</a>
            </div>

            <div class="card-body">
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($presences as $presence)
                            <tr>
                                <td>{{ $presence->employee->fullname ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($presence->date)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($presence->check_in)->format('H:i:s') }}</td>
                                <td>{{ \Carbon\Carbon::parse($presence->check_out)->format('H:i:s') }}</td>
                                <td>
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
                                </td>
                                <td>
                                    <a href="{{ route('presences.show', $presence->id) }}" class="btn btn-info btn-sm">View</a>
                                    @if(session('role') == 'Admin' || session('role') == 'HR Manager')
                                    <a href="{{ route('presences.edit', $presence->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                    <form action="{{ route('presences.destroy', $presence->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure to delete this record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
