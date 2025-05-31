<!-- resources/views/roles/index.blade.php -->

@extends('layouts.dashboard')

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
                <h3>Roles</h3>
                <p class="text-subtitle text-muted">Manage user roles and permissions</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Roles</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Role List</h5>
                <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">New Role</a>
            </div>

            <div class="card-body">
                <!-- Desktop Table (shown ≥576px) -->
                <div class="table-responsive d-none d-sm-block">
                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                <tr>
                                    <td>{{ $role->title }}</td>
                                    <td>{{ $role->description ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('roles.show', $role->id) }}" class="btn btn-info btn-sm">View</a>
                                        @if (!in_array($role->title, ['Admin', 'HR Manager', 'Developer', 'Accountant', 'Data Entry', 'Animator', 'Marketer']))
                                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure to delete this role?');">
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
                <!-- Mobile Card/List (shown <576px) -->
                <div class="d-block d-sm-none">
                    @foreach ($roles as $role)
                    <div class="border rounded mb-2 px-2 py-2 bg-white shadow-sm">
                        <div class="fw-bold mb-1">{{ $role->title }}</div>
                        <div class="small text-muted mb-1">{{ $role->description ?? '-' }}</div>
                        <div>
                            <a href="{{ route('roles.show', $role->id) }}" class="btn btn-info btn-sm mb-1">View</a>
                            @if (!in_array($role->title, ['Admin', 'HR Manager', 'Developer', 'Accountant', 'Data Entry', 'Animator', 'Marketer']))
                                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary btn-sm mb-1">Edit</a>
                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure to delete this role?');">
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
@endsection
