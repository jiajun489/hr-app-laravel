<!-- resources/views/employees/show.blade.php -->
 
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
                <h3>View Employee</h3>
                <p class="text-subtitle text-muted">Detail information of the selected employee</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
                        <li class="breadcrumb-item active" aria-current="page">View</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Employee Detail</h5>
            </div>

            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Full Name</dt>
                    <dd class="col-sm-9">{{ $employee->fullname }}</dd>

                    <dt class="col-sm-3">Email</dt>
                    <dd class="col-sm-9">{{ $employee->email }}</dd>

                    <dt class="col-sm-3">Phone</dt>
                    <dd class="col-sm-9">{{ $employee->phone }}</dd>

                    <dt class="col-sm-3">Address</dt>
                    <dd class="col-sm-9">{{ $employee->address }}</dd>

                    <dt class="col-sm-3">Birth Date</dt>
                    <dd class="col-sm-9">{{ \Carbon\Carbon::parse($employee->birth_date)->format('d M Y') }}</dd>

                    <dt class="col-sm-3">Hire Date</dt>
                    <dd class="col-sm-9">{{ \Carbon\Carbon::parse($employee->hire_date)->format('d M Y') }}</dd>

                    <dt class="col-sm-3">Department</dt>
                    <dd class="col-sm-9">{{ $employee->department->name ?? '-' }}</dd>

                    <dt class="col-sm-3">Role</dt>
                    <dd class="col-sm-9">{{ $employee->role->title ?? '-' }}</dd>

                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9">
                        @if ($employee->status === 'active')
                            <span class="badge bg-success">Active</span>
                        @elseif ($employee->status === 'inactive')
                            <span class="badge bg-secondary">Inactive</span>
                        @else
                            <span class="badge bg-warning text-dark">{{ ucfirst($employee->status) }}</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Salary</dt>
                    <dd class="col-sm-9">${{ number_format($employee->salary, 2) }}</dd>
                </dl>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary">Back to Employee List</a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
