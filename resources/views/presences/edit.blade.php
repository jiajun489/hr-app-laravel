<!-- resources/views/presences/edit.blade.php -->

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
                <h3>Edit Presence</h3>
                <p class="text-subtitle text-muted">You can update the presence details here</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('presences.index') }}">Presences</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Presence Form</h5>
            </div>

            <div class="card-body">
                <form action="{{ route('presences.update', $presence->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="employee_id" class="form-label">Employee</label>
                        <select class="form-select @error('employee_id') is-invalid @enderror" name="employee_id" id="employee_id">
                            <option value="">-- Select Employee --</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('employee_id', $presence->employee_id) == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->fullname }}
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="check_in" class="form-label">Check In</label>
                        <input type="text" class="form-control datetime @error('check_in') is-invalid @enderror"
                               name="check_in" id="check_in" value="{{ old('check_in', $presence->check_in->format('Y-m-d H:i:s')) }}">
                        @error('check_in')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="check_out" class="form-label">Check Out</label>
                        <input type="text" class="form-control datetime @error('check_out') is-invalid @enderror"
                               name="check_out" id="check_out" value="{{ old('check_out', optional($presence->check_out)->format('Y-m-d H:i:s')) }}">
                        @error('check_out')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="text" class="form-control date @error('date') is-invalid @enderror"
                               name="date" id="date" value="{{ old('date', $presence->date->format('Y-m-d')) }}">
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror" name="status" id="status">
                            <option value="present" {{ old('status', $presence->status) == 'present' ? 'selected' : '' }}>Present</option>
                            <option value="absent" {{ old('status', $presence->status) == 'absent' ? 'selected' : '' }}>Absent</option>
                            <option value="late" {{ old('status', $presence->status) == 'late' ? 'selected' : '' }}>Late</option>
                            <option value="leave" {{ old('status', $presence->status) == 'leave' ? 'selected' : '' }}>Leave</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('presences.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update Presence</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<style>
    /* Untuk semua tombol (Cancel dan Create Presence) di form presence */
.d-flex .btn {
    display: flex !important;
    align-items: center;
    justify-content: center;
    height: 55px;
    font-size: 1rem;
    padding: 0 24px;
    min-width: 120px;
}

/* Extra: Buat tombol lebih proporsional di mobile */
@media (max-width: 576px) {
    .d-flex .btn {
        width: 32vw;
        min-width: unset;
        font-size: 1rem;
    }
}   
</style>
@endsection
