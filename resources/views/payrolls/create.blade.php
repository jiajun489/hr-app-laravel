<!-- resources/views/payrolls/create.blade.php -->
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
                <h3>Create Payroll</h3>
                <p class="text-subtitle text-muted">You can register a new payroll record here</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('payrolls.index') }}">Payrolls</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">New Payroll Form</h5>
            </div>

            <div class="card-body">
                <form action="{{ route('payrolls.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="employee_id" class="form-label">Employee</label>
                        <select class="form-select @error('employee_id') is-invalid @enderror" name="employee_id" id="employee_id">
                            <option value="">-- Select Employee --</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->fullname }}
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="salary" class="form-label">Salary</label>
                        <input type="number" step="0.01" class="form-control @error('salary') is-invalid @enderror"
                               id="salary" name="salary" value="{{ old('salary') }}">
                        @error('salary')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="bonus" class="form-label">Bonus</label>
                        <input type="number" step="0.01" class="form-control @error('bonus') is-invalid @enderror"
                               id="bonus" name="bonus" value="{{ old('bonus') }}">
                        @error('bonus')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="deduction" class="form-label">Deduction</label>
                        <input type="number" step="0.01" class="form-control @error('deduction') is-invalid @enderror"
                               id="deduction" name="deduction" value="{{ old('deduction') }}">
                        @error('deduction')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="net_salary" class="form-label">Net Salary</label>
                        <input type="number" step="0.01" readonly class="form-control @error('net_salary') is-invalid @enderror"
                            id="net_salary" name="net_salary"
                            value="{{ old('net_salary', $payroll->net_salary ?? '') }}">
                        @error('net_salary')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Payment Date</label>
                        <input type="date" class="form-control date @error('payment_date') is-invalid @enderror"
                               id="payment_date" name="payment_date" value="{{ old('payment_date') }}">
                        @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('payrolls.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Payroll</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<script>
    function calculateNetSalary() {
        const salary = parseFloat(document.getElementById('salary').value) || 0;
        const bonus = parseFloat(document.getElementById('bonus').value) || 0;
        const deduction = parseFloat(document.getElementById('deduction').value) || 0;
        document.getElementById('net_salary').value = (salary + bonus - deduction).toFixed(2);
    }

    document.getElementById('salary').addEventListener('input', calculateNetSalary);
    document.getElementById('bonus').addEventListener('input', calculateNetSalary);
    document.getElementById('deduction').addEventListener('input', calculateNetSalary);
</script>

@endsection
