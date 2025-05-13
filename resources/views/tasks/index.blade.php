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
                <h3>Tasks</h3>
                <p class="text-subtitle text-muted">You can manage your tasks here</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tasks</li>
                        <li class="breadcrumb-item active" aria-current="page">Index</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Datatable
                </h5>
            </div>
            <div class="card-body">

            <div class="d-flex">
                <a href="" class="btn btn-primary mb-3 ms-auto">New Task</a>
            </div>
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
                            <td>{{ $task->employee->fullname }}</td>
                            <td>{{ $task->due_date }}</td>
                            <td>
                                @if ($task->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                                @elseif ($task->status == 'completed')
                                <span class="badge bg-success">Completed</span>
                                @else
                                <span class="badge bg-success">{{ $task->status }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="" class="btn btn-info btn-sm">View</a>
                                @if ($task->status == 'pending')
                                <a href="" class="btn btn-success btn-sm">Mark Complete</a>
                                @else
                                <a href="" class="btn btn-warning btn-sm">Mark Pending</a>
                                @endif
                                <a href="" class="btn btn-primary btn-sm">Edit</a>
                                <a href="" class="btn btn-danger btn-sm">Delete</a>
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