<!-- resources/views/departments/show.blade.php -->

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
                <h3>View Department</h3>
                <p class="text-subtitle text-muted">Detail information of the selected department</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('departments.index') }}">Departments</a></li>
                        <li class="breadcrumb-item active" aria-current="page">View</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Department Detail</h5>
            </div>

            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Department Name</dt>
                    <dd class="col-sm-9">{{ $department->name }}</dd>

                    <dt class="col-sm-3">Description</dt>
                    <dd class="col-sm-9">{{ $department->description ?? '-' }}</dd>

                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9">
                        @if ($department->status === 'active')
                            <span class="badge bg-success">Active</span>
                        @elseif ($department->status === 'inactive')
                            <span class="badge bg-secondary">Inactive</span>
                        @else
                            <span class="badge bg-warning text-dark">{{ ucfirst($department->status) }}</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Total Employees</dt>
                    <dd class="col-sm-9">{{ $department->employees->count() }}</dd>
                </dl>

                <div class="d-flex justify-content-start">
                    <a href="{{ route('departments.index') }}" class="btn btn-secondary">Back to Department List</a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
