<?php
// app/Http/Controllers/LeaveRequestController.php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Employee;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of the leave requests.
     */
    public function index()
    {
        $leaveRequests = LeaveRequest::with('employee')->latest()->get();
        return view('leave_requests.index', compact('leaveRequests'));
    }

    /**
     * Show the form for creating a new leave request.
     */
    public function create()
    {
        $employees = Employee::all();
        return view('leave_requests.create', compact('employees'));
    }

    /**
     * Store a newly created leave request in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type'  => 'required|string|max:255',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'status'      => 'required|in:pending,approved,rejected',
        ]);

        LeaveRequest::create($request->all());

        return redirect()->route('leave-requests.index')->with('success', 'Leave request submitted successfully.');
    }

    /**
     * Display the specified leave request.
     */
    public function show(LeaveRequest $leaveRequest)
    {
        return view('leave_requests.show', compact('leaveRequest'));
    }

    /**
     * Show the form for editing the specified leave request.
     */
    public function edit(LeaveRequest $leaveRequest)
    {
        $employees = Employee::all();
        return view('leave_requests.edit', compact('leaveRequest', 'employees'));
    }

    /**
     * Update the specified leave request in storage.
     */
    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type'  => 'required|string|max:255',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'status'      => 'required|in:pending,approved,rejected',
        ]);

        $leaveRequest->update($request->all());

        return redirect()->route('leave-requests.index')->with('success', 'Leave request updated successfully.');
    }

    /**
     * Remove the specified leave request from storage.
     */
    public function destroy(LeaveRequest $leaveRequest)
    {
        $leaveRequest->delete();

        return redirect()->route('leave-requests.index')->with('success', 'Leave request deleted successfully.');
    }
}
