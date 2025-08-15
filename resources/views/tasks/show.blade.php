<!-- resources/views/tasks/show.blade.php -->

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
                <h3>View Task</h3>
                <p class="text-subtitle text-muted">Detail information of the selected task</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('tasks.index') }}">Tasks</a></li>
                        <li class="breadcrumb-item active" aria-current="page">View</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Task Detail</h5>
            </div>

            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Title</dt>
                    <dd class="col-sm-9">{{ $task->title }}</dd>

                    <dt class="col-sm-3">Description</dt>
                    <dd class="col-sm-9">{{ $task->description ?? '-' }}</dd>

                    <dt class="col-sm-3">PR URL</dt>
                    <dd class="col-sm-9">
                        @if($task->pr_url)
                            <a href="{{ $task->pr_url }}" target="_blank" class="text-primary">
                                <i class="bi bi-link-45deg"></i> {{ $task->pr_url }}
                            </a>
                            <small class="text-muted d-block">Pull Request for code review</small>
                        @else
                            <span class="text-muted">No PR linked</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Platform</dt>
                    <dd class="col-sm-9">
                        @if($task->platform)
                            <span class="badge bg-info">{{ $task->platform }}</span>
                        @else
                            <span class="text-muted">Not specified</span>
                        @endif
                    </dd>

                    <dt class="col-sm-3">Assigned To</dt>
                    <dd class="col-sm-9">{{ $task->employee->fullname ?? '-' }}</dd>

                    <dt class="col-sm-3">Due Date</dt>
                    <dd class="col-sm-9">{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</dd>

                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9">
                        @if ($task->status === 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @elseif ($task->status === 'in_progress')
                            <span class="badge bg-primary">In Progress</span>
                        @elseif ($task->status === 'completed')
                            <span class="badge bg-success">Completed</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($task->status) }}</span>
                        @endif
                    </dd>
                </dl>

                <div class="d-flex justify-content-start">
                    <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Back to Task List</a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
