@extends('layouts.dashboard')
<!-- resources/views/departments/index.blade.php -->
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

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Departments</h3>
                <p class="text-subtitle text-muted">Manage organizational departments</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Departments</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-column flex-sm-row" style="gap:10px;">
                <h5 class="card-title mb-0 w-100" style="flex: 1 1 0; text-align: left;">Department List</h5>
                <a href="{{ route('departments.create') }}" class="btn btn-primary btn-md w-100 w-sm-auto" style="max-width:220px;">New Department</a>
            </div>

            <div class="card-body">
                <!-- Desktop Table -->
                <div class="d-none d-md-block">
                    <div class="table-responsive">
                        <table class="table table-striped" id="table1">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($departments as $department)
                                    <tr>
                                        <td>{{ $department->name }}</td>
                                        <td>{{ $department->description ?? '-' }}</td>
                                        <td>
                                            @if ($department->status === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif ($department->status === 'inactive')
                                                <span class="badge bg-secondary">Inactive</span>
                                            @else
                                                <span class="badge bg-warning text-dark">{{ ucfirst($department->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('departments.show', $department->id) }}" class="btn btn-info btn-sm mb-1">View</a>
                                            <a href="{{ route('departments.edit', $department->id) }}" class="btn btn-primary btn-sm mb-1">Edit</a>
                                            <form action="{{ route('departments.destroy', $department->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure to delete this department?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm mb-1">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Mobile Card Stack -->
                <div class="d-block d-md-none">
                    @foreach ($departments as $department)
                        <div class="dep-list-card mb-3 p-3 border rounded bg-white shadow-sm">
                            <div class="fw-bold mb-1">{{ $department->name }}</div>
                            <div class="mb-2" style="font-size:14px;">
                                {{ $department->description ?? '-' }}
                            </div>
                            <div style="font-size:15px;">
                                Status:
                                @if ($department->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif ($department->status === 'inactive')
                                    <span class="badge bg-secondary">Inactive</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ ucfirst($department->status) }}</span>
                                @endif
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('departments.show', $department->id) }}" class="btn btn-info btn-sm mb-1">View</a>
                                <a href="{{ route('departments.edit', $department->id) }}" class="btn btn-primary btn-sm mb-1">Edit</a>
                                <form action="{{ route('departments.destroy', $department->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure to delete this department?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm mb-1">Delete</button>
                                </form>
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
    .dep-list-card {
        border: 1px solid #eee;
        border-radius: 13px;
        background: #fff;
        margin-bottom: 16px;
        box-shadow: 0 1px 8px 0 rgba(180,200,230,0.07);
    }
    .card-header {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    .card-header .card-title {
        margin-bottom: 8px !important;
        font-size: 19px;
    }
    .card-header .btn {
        width: 100% !important;
        font-size: 16px;
    }
}
</style>
@endsection
