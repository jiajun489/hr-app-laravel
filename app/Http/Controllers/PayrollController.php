<?php
// app/Http/Controllers/PayrollController.php
namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        $payrolls = Payroll::with('employee')->latest()->get();
        return view('payrolls.index', compact('payrolls'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('payrolls.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id'  => 'required|exists:employees,id',
            'salary'       => 'required|numeric|min:0',
            'bonus'        => 'nullable|numeric|min:0',
            'deduction'    => 'nullable|numeric|min:0',
            'payment_date' => 'required|date',
        ]);

        $data = $request->all();
        $data['bonus'] = $data['bonus'] ?? 0;
        $data['deduction'] = $data['deduction'] ?? 0;
        $data['net_salary'] = $data['salary'] + $data['bonus'] - $data['deduction'];

        Payroll::create($data);

        return redirect()->route('payrolls.index')->with('success', 'Payroll record created successfully.');
    }

    public function show(Payroll $payroll)
    {
        return view('payrolls.show', compact('payroll'));
    }

    public function edit(Payroll $payroll)
    {
        $employees = Employee::all();
        return view('payrolls.edit', compact('payroll', 'employees'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        $request->validate([
            'employee_id'  => 'required|exists:employees,id',
            'salary'       => 'required|numeric|min:0',
            'bonus'        => 'nullable|numeric|min:0',
            'deduction'    => 'nullable|numeric|min:0',
            'payment_date' => 'required|date',
        ]);

        $data = $request->all();
        $data['bonus'] = $data['bonus'] ?? 0;
        $data['deduction'] = $data['deduction'] ?? 0;
        $data['net_salary'] = $data['salary'] + $data['bonus'] - $data['deduction'];

        $payroll->update($data);

        return redirect()->route('payrolls.index')->with('success', 'Payroll record updated successfully.');
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return redirect()->route('payrolls.index')->with('success', 'Payroll record deleted successfully.');
    }
}
