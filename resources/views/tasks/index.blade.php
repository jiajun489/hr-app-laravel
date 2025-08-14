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

    <!-- Quick Add Task Section -->
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Quick Add Task</h6>
            </div>
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-8">
                        <p class="text-muted mb-2">Need to add a new task quickly? Click the button below or use the detailed form.</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('tasks.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Create New Task
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Task List</h5>
                <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i> Add New Task
                </a>
            </div>

            <div class="card-body">
    <!-- Desktop Table -->
        <div class="table-responsive d-none d-sm-block">
            <table class="table table-striped" id="table1">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Assigned To</th>
                        <th>Repository</th>
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
                            <td>
                                @if($task->repo)
                                    <a href="{{ $task->repo }}" target="_blank" class="text-primary">
                                        <i class="bi bi-github"></i> Repository
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
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
                                <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-info btn-sm mb-1">View</a>
                                @if ($task->status === 'pending')
                                    <a href="{{ route('tasks.markComplete', $task->id) }}" class="btn btn-success btn-sm mb-1">Mark Complete</a>
                                @else
                                    <a href="{{ route('tasks.markPending', $task->id) }}" class="btn btn-warning btn-sm mb-1">Mark Pending</a>
                                @endif
                                @if(session('role') == 'Admin' || session('role') == 'HR Manager' || session('role') == 'Developer')
                                    <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-primary btn-sm mb-1">Edit</a>
                                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
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

        <!-- Mobile Card List -->
        <div class="d-block d-sm-none">
            @foreach ($tasks as $task)
            <div class="border rounded mb-2 px-2 py-2 bg-white shadow-sm">
                <div class="mb-1">
                    <strong>{{ $task->title }}</strong>
                    <span class="badge float-end
                        @if($task->status === 'pending') bg-warning 
                        @elseif($task->status === 'completed') bg-success
                        @else bg-primary @endif">
                        {{ ucfirst($task->status) }}
                    </span>
                </div>
                <div class="small text-muted mb-1">Assigned to: <b>{{ $task->employee->fullname ?? '-' }}</b></div>
                @if($task->repo)
                    <div class="small text-muted mb-1">
                        Repository: <a href="{{ $task->repo }}" target="_blank" class="text-primary"><i class="bi bi-github"></i> View Repo</a>
                    </div>
                @endif
                <div class="small text-muted mb-2">Due: {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</div>
                <div>
                    <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-info btn-sm mb-1">View</a>
                    @if ($task->status === 'pending')
                        <a href="{{ route('tasks.markComplete', $task->id) }}" class="btn btn-success btn-sm mb-1">Mark Complete</a>
                    @else
                        <a href="{{ route('tasks.markPending', $task->id) }}" class="btn btn-warning btn-sm mb-1">Mark Pending</a>
                    @endif
                    @if(session('role') == 'Admin' || session('role') == 'HR Manager' || session('role') == 'Developer')
                        <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-primary btn-sm mb-1">Edit</a>
                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
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
