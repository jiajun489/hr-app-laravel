<?php
// app/Http/Controllers/PayrollController.php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    /**
     * Display a listing of the payrolls.
     */
    public function index()
    {
        $payrolls = Payroll::with('employee')->latest()->get();
        return view('payrolls.index', compact('payrolls'));
    }

    /**
     * Show the form for creating a new payroll.
     */
    public function create()
    {
        $employees = Employee::all();
        return view('payrolls.create', compact('employees'));
    }

    /**
     * Store a newly created payroll in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id'  => 'required|exists:employees,id',
            'salary'       => 'required|numeric|min:0',
            'bonus'        => 'nullable|numeric|min:0',
            'deduction'    => 'nullable|numeric|min:0',
            'net_salary'   => 'required|numeric|min:0',
            'payment_date' => 'required|date',
        ]);

        Payroll::create($request->all());

        return redirect()->route('payrolls.index')->with('success', 'Payroll record created successfully.');
    }

    /**
     * Display the specified payroll.
     */
    public function show(Payroll $payroll)
    {
        return view('payrolls.show', compact('payroll'));
    }

    /**
     * Show the form for editing the specified payroll.
     */
    public function edit(Payroll $payroll)
    {
        $employees = Employee::all();
        return view('payrolls.edit', compact('payroll', 'employees'));
    }

    /**
     * Update the specified payroll in storage.
     */
    public function update(Request $request, Payroll $payroll)
    {
        $request->validate([
            'employee_id'  => 'required|exists:employees,id',
            'salary'       => 'required|numeric|min:0',
            'bonus'        => 'nullable|numeric|min:0',
            'deduction'    => 'nullable|numeric|min:0',
            'net_salary'   => 'required|numeric|min:0',
            'payment_date' => 'required|date',
        ]);

        $payroll->update($request->all());

        return redirect()->route('payrolls.index')->with('success', 'Payroll record updated successfully.');
    }

    /**
     * Remove the specified payroll from storage.
     */
    public function destroy(Payroll $payroll)
    {
        $payroll->delete();

        return redirect()->route('payrolls.index')->with('success', 'Payroll record deleted successfully.');
    }
}
