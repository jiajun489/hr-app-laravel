<!-- resources/views/payrolls/show.blade.php -->

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
                <h3>View Payroll</h3>
                <p class="text-subtitle text-muted">Detailed information of the selected payroll record</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('payrolls.index') }}">Payrolls</a></li>
                        <li class="breadcrumb-item active" aria-current="page">View</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Payroll Detail</h5>
            </div>

            <div class="card-body" id="printableArea">
                <dl class="row">
                    <dt class="col-sm-3">Employee</dt>
                    <dd class="col-sm-9">{{ $payroll->employee->fullname ?? '-' }}</dd>

                    <dt class="col-sm-3">Salary</dt>
                    <dd class="col-sm-9">${{ number_format($payroll->salary, 2) }}</dd>

                    <dt class="col-sm-3">Bonus</dt>
                    <dd class="col-sm-9">${{ number_format($payroll->bonus ?? 0, 2) }}</dd>

                    <dt class="col-sm-3">Deduction</dt>
                    <dd class="col-sm-9">${{ number_format($payroll->deduction ?? 0, 2) }}</dd>

                    <dt class="col-sm-3">Net Salary</dt>
                    <dd class="col-sm-9"><strong>${{ number_format($payroll->net_salary, 2) }}</strong></dd>

                    <dt class="col-sm-3">Payment Date</dt>
                    <dd class="col-sm-9">{{ \Carbon\Carbon::parse($payroll->payment_date)->format('d M Y') }}</dd>
                </dl>
            </div>

            <div class="card-footer d-flex justify-content-start gap-2">
                <button id="btnPrint" class="btn btn-primary">
                    <i class="bi bi-printer"></i> Print
                </button>
                <a href="{{ route('payrolls.index') }}" class="btn btn-secondary">Back to Payroll List</a>
            </div>
        </div>
    </section>
</div>

@endsection

@push('scripts')
<script>
    document.getElementById('btnPrint').addEventListener('click', function () {
        let printContents = document.getElementById('printableArea').innerHTML;
        let originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload(); // reload to restore scripts/css
    });
</script>
@endpush
