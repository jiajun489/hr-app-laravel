@extends('layouts.dashboard')
@section('content')
<header class="mb-3">
    <!-- // resources/views/dashboard/index.blade.php -->
    <a href="#" class="burger-btn d-block d-xl-none">
        <i class="bi bi-justify fs-3"></i>
    </a>
</header>
            
<div class="page-heading">
    <h3>Dashboard</h3>
</div> 
<div class="page-content"> 
    <div class="row">
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                            <div class="stats-icon purple mb-2">
                                <i class="icon dripicons dripicons-tag"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Departments</h6>
                            <h6 class="font-extrabold mb-0">{{ $departments }}</h6>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card"> 
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                            <div class="stats-icon blue mb-2">
                                <i class="icon dripicons dripicons-user"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Employees</h6>
                            <h6 class="font-extrabold mb-0">{{ $employees }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                            <div class="stats-icon green mb-2">
                                <i class="icon dripicons dripicons-alarm"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Presence</h6>
                            <h6 class="font-extrabold mb-0">{{ $presences }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                            <div class="stats-icon red mb-2">
                                <i class="icon dripicons dripicons-wallet"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                            <h6 class="text-muted font-semibold">Payrolls</h6>
                            <h6 class="font-extrabold mb-0">{{ $payrolls }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Latest Presences</h4>
                </div>
                <div class="card-body">
                    <canvas id="presence" style="width:100%;height:200px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Latest Tasks</h4>
                </div>
                <div class="card-body">
                    <!-- Desktop Table (shown ≥576px) -->
                    <div class="table-responsive d-none d-sm-block">
                        <table class="table table-hover table-lg">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Detail</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tasks as $task)
                                <tr>
                                    <td class="col-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-md">
                                                <img src="https://ui-avatars.com/api/?name={{ $task->employee->fullname }}$background=random">
                                            </div>
                                            <p class="font-bold ms-3 mb-0">{{ $task->employee->fullname }}</p>
                                        </div>
                                    </td>
                                    <td class="col-auto">
                                        <p class=" mb-0">{{ $task->title }}</p>
                                    </td>
                                    <td class="col-auto">
                                        <p class=" mb-0">{{ ucfirst($task->status) }}</p>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile List (shown <576px) -->
                    <div class="d-block d-sm-none">
                        @foreach ($tasks as $task)
                        <div class="border rounded mb-2 px-2 py-2 bg-white shadow-sm">
                            <div class="d-flex align-items-center mb-1">
                                <div class="avatar avatar-sm me-2">
                                    <img src="https://ui-avatars.com/api/?name={{ $task->employee->fullname }}$background=random" width="32" height="32">
                                </div>
                                <div>
                                    <strong>{{ $task->employee->fullname }}</strong>
                                    <div class="small text-muted">{{ ucfirst($task->status) }}</div>
                                </div>
                            </div>
                            <div class="small ps-4">{{ $task->title }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

@endsection