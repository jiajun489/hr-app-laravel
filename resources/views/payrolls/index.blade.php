<!-- resources/views/payrolls/index.blade.php -->
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
                <h3>Payrolls</h3>
                <p class="text-subtitle text-muted">Manage employee salary records</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Payrolls</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card payroll-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Payroll List</h5>
                @if(session('role') == 'Admin' || session('role') == 'HR Manager')
                    <a href="{{ route('payrolls.create') }}" class="btn btn-primary btn-md">New Payroll</a>
                @endif
            </div>

            <div class="card-body">
                <!-- Desktop Table -->
                <div class="d-none d-md-block">
                    <div class="table-responsive">
                        <table class="table table-striped" id="table1">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Salary</th>
                                    <th>Bonus</th>
                                    <th>Deduction</th>
                                    <th>Net Salary</th>
                                    <th>Payment Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payrolls as $payroll)
                                    <tr>
                                        <td>{{ $payroll->employee->fullname ?? '-' }}</td>
                                        <td>${{ number_format($payroll->salary, 2) }}</td>
                                        <td>${{ number_format($payroll->bonus ?? 0, 2) }}</td>
                                        <td>${{ number_format($payroll->deduction ?? 0, 2) }}</td>
                                        <td><strong>${{ number_format($payroll->net_salary, 2) }}</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($payroll->payment_date)->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('payrolls.show', $payroll->id) }}" class="btn btn-info btn-sm mb-1">View</a>
                                            @if(session('role') == 'Admin' || session('role') == 'HR Manager')
                                                <a href="{{ route('payrolls.edit', $payroll->id) }}" class="btn btn-primary btn-sm mb-1">Edit</a>
                                                <form action="{{ route('payrolls.destroy', $payroll->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure to delete this payroll record?');">
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
                    @foreach ($payrolls as $payroll)
                        <div class="payroll-list-card mb-3 p-3 border rounded bg-white shadow-sm">
                            <div class="fw-bold mb-1">{{ $payroll->employee->fullname ?? '-' }}</div>
                            <div class="text-muted" style="font-size:14px;">
                                Payment: <strong>{{ \Carbon\Carbon::parse($payroll->payment_date)->format('d M Y') }}</strong>
                            </div>
                            <div style="font-size:15px;">
                                <span>Salary: <b>${{ number_format($payroll->salary, 2) }}</b></span><br>
                                <span>Bonus: <b>${{ number_format($payroll->bonus ?? 0, 2) }}</b></span><br>
                                <span>Deduction: <b>${{ number_format($payroll->deduction ?? 0, 2) }}</b></span><br>
                                <span>Net: <b style="color:#1A7F37">${{ number_format($payroll->net_salary, 2) }}</b></span>
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('payrolls.show', $payroll->id) }}" class="btn btn-info btn-sm mb-1">View</a>
                                @if(session('role') == 'Admin' || session('role') == 'HR Manager')
                                    <a href="{{ route('payrolls.edit', $payroll->id) }}" class="btn btn-primary btn-sm mb-1">Edit</a>
                                    <form action="{{ route('payrolls.destroy', $payroll->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure to delete this payroll record?');">
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
/* Mobile card for payrolls */
@media (max-width: 576px) {
    .payroll-card {
        margin-left: 5px;
        margin-right: 5px;
    }
    .payroll-list-card {
        border: 1px solid #eee;
        border-radius: 13px;
        background: #fff;
        margin-bottom: 16px;
        box-shadow: 0 1px 8px 0 rgba(180,200,230,0.07);
    }
}
</style>
@endsection
