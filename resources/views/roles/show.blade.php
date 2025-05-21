<!-- resources/views/roles/show.blade.php -->

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
                <h3>View Role</h3>
                <p class="text-subtitle text-muted">Detail information of the selected role</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
                        <li class="breadcrumb-item active" aria-current="page">View</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Role Detail</h5>
            </div>

            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Title</dt>
                    <dd class="col-sm-9">{{ $role->title }}</dd>

                    <dt class="col-sm-3">Description</dt>
                    <dd class="col-sm-9">{{ $role->description ?? '-' }}</dd>
                </dl>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">Back to Role List</a>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
