@extends('layouts.dashboard')
<!-- resources/views/employees/index.blade.php -->
@section('content')
<header class="mb-3">
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>
</header>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success:</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error:</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Employees</h3>
                <p class="text-subtitle text-muted">For employee management</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Employees</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Employee List</h5>
                <a href="{{ route('employees.create') }}" class="btn btn-primary btn-md">New Employee</a>
            </div>

            <div class="card-body">
                <!-- Desktop Table -->
                <div class="d-none d-md-block">
                    <div class="table-responsive">
                        <table class="table table-striped" id="table1">
                            <thead>
                                <tr>
                                    <th>Fullname</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Salary</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $employee)
                                    <tr>
                                        <td>{{ $employee->fullname }}</td>
                                        <td>{{ $employee->email }}</td>
                                        <td>{{ $employee->department->name ?? '-' }}</td>
                                        <td>{{ $employee->role->title ?? '-' }}</td>
                                        <td>
                                            @if ($employee->status === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif ($employee->status === 'inactive')
                                                <span class="badge bg-secondary">Inactive</span>
                                            @else
                                                <span class="badge bg-warning text-dark">{{ ucfirst($employee->status) }}</span>
                                            @endif
                                        </td>
                                        <td>${{ number_format($employee->salary, 2) }}</td>
                                        <td>
                                            <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-info btn-sm mb-1">View</a>
                                            <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-primary btn-sm mb-1">Edit</a>
                                            @if (auth()->user()->email !== $employee->email)
                                                <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure to delete this employee?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm mb-1">Delete</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Mobile Card Stack -->
                <div class="d-block d-md-none">
                    @foreach ($employees as $employee)
                        <div class="emp-list-card mb-3 p-3 border rounded bg-white shadow-sm">
                            <div class="fw-bold mb-1">{{ $employee->fullname }}</div>
                            <div class="text-muted" style="font-size:14px;">
                                {{ $employee->email }}
                            </div>
                            <div style="font-size:15px;">
                                Department: <strong>{{ $employee->department->name ?? '-' }}</strong><br>
                                Role: <strong>{{ $employee->role->title ?? '-' }}</strong><br>
                                Status:
                                @if ($employee->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif ($employee->status === 'inactive')
                                    <span class="badge bg-secondary">Inactive</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ ucfirst($employee->status) }}</span>
                                @endif<br>
                                Salary: <strong>${{ number_format($employee->salary, 2) }}</strong>
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-info btn-sm mb-1">View</a>
                                <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-primary btn-sm mb-1">Edit</a>
                                @if (auth()->user()->email !== $employee->email)
                                    <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure to delete this employee?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm mb-1">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</div>
<style>
@media (max-width: 576px) {
    .emp-list-card {
        border: 1px solid #eee;
        border-radius: 13px;
        background: #fff;
        margin-bottom: 16px;
        box-shadow: 0 1px 8px 0 rgba(180,200,230,0.07);
    }
}
</style>
@endsection
