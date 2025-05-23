<!--resource/views/tasks/index.blade.php-->
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

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Tasks</h3>
                <p class="text-subtitle text-muted">You can manage your tasks here</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tasks</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Task List</h5>
                @if(session('role') == 'Admin' || session('role') == 'HR Manager')
                    <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm">New Task</a>
                @endif
            </div>

            <div class="card-body">
                <table class="table table-striped" id="table1">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Assigned To</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tasks as $task)
                            <tr>
                                <td>{{ $task->title }}</td>
                                <td>{{ $task->employee->fullname ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</td>
                                <td>
                                    @if ($task->status === 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif ($task->status === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @else
                                        <span class="badge bg-primary">{{ ucfirst($task->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-info btn-sm">View</a>

                                    @if ($task->status === 'pending')
                                        <a href="{{ route('tasks.markComplete', $task->id) }}" class="btn btn-success btn-sm">Mark Complete</a>
                                    @else
                                        <a href="{{ route('tasks.markPending', $task->id) }}" class="btn btn-warning btn-sm">Mark Pending</a>
                                    @endif

                                    @if(session('role') == 'Admin' || session('role') == 'HR Manager')
                                            <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-primary btn-sm">Edit</a>

                                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
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
