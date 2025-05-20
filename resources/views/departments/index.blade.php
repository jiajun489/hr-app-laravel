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
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Department List</h5>
                <a href="{{ route('departments.create') }}" class="btn btn-primary btn-sm">New Department</a>
            </div>

            <div class="card-body">
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
                                    <a href="{{ route('departments.show', $department->id) }}" class="btn btn-info btn-sm">View</a>
                                    <a href="{{ route('departments.edit', $department->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                    <form action="{{ route('departments.destroy', $department->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure to delete this department?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Delete</button>
                                    </form>
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
