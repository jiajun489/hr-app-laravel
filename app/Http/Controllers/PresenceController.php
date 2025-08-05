<?php
// app/Http/Controllers/PresenceController.php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PresenceController extends Controller
{
    /**
     * Display a listing of the presences.
     */
    public function index()
    {
        if(session('role') == 'Admin' || session('role') == 'HR Manager'){
            $presences = Presence::all();
        }else {
            $presences = Presence::where('employee_id', session('employee_id'))->get();
        }
        return view('presences.index', compact('presences'));
    }

    /**
     * Show the form for creating a new presence.
     */
    public function create()
    {
        $employees = Employee::all();
        
        // Check if the user has already clocked in today
        $hasActivePresence = false;
        $hasCompletedPresence = false;
        
        if (session('employee_id')) {
            $today = Carbon::now()->format('Y-m-d');
            
            // Check for active presence (clocked in but not out)
            $activePresence = Presence::where('employee_id', session('employee_id'))
                                ->where('date', $today)
                                ->whereNull('check_out')
                                ->first();
            
            // Check for completed presence (clocked in and out)
            $completedPresence = Presence::where('employee_id', session('employee_id'))
                                ->where('date', $today)
                                ->whereNotNull('check_out')
                                ->first();
            
            $hasActivePresence = $activePresence ? true : false;
            $hasCompletedPresence = $completedPresence ? true : false;
        }
        
        return view('presences.create', compact('employees', 'hasActivePresence', 'hasCompletedPresence'));
    }

    /**
     * Store a newly created presence in storage.
     */
    public function store(Request $request)
    {
        if(session('role') == 'Admin' || session('role') == 'HR Manager'){

        $request->merge([
        'check_in'  => date('Y-m-d H:i:s', strtotime($request->check_in)),
        ]);

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'check_in'    => 'required|date_format:Y-m-d H:i:s',
            'date'        => 'required|date_format:Y-m-d',
            'status'      => 'required|in:present,absent,late,leave',
        ]);

            Presence::create([
            'employee_id' => $request->employee_id,
            'check_in'    => $request->check_in,
            'check_out'   => null, // biarkan kosong/null
            'date'        => $request->date,
            'status'      => $request->status,
        ]);
        }else {
            Presence::create([
                'employee_id' => session('employee_id'),
                'check_in'    => Carbon::now()->format('Y-m-d H:i:s'),
                'check_out'   => null, 
                'latitude'    => $request->latitude,
                'longitude'   => $request->longitude,
                'date'        => Carbon::now()->format('Y-m-d'),
                'status'      => 'present',
            ]);
        }

        return redirect()->route('presences.index')->with('success', 'Presence recorded successfully.');
    }

    /**
     * Display the specified presence.
     */
    public function show(Presence $presence)
    {
        return view('presences.show', compact('presence'));
    }

    /**
     * Show the form for editing the specified presence.
     */
    public function edit(Presence $presence)
    {
        $employees = Employee::all();
        return view('presences.edit', compact('presence', 'employees'));
    }

    /**
     * Update the specified presence in storage.
     */
    public function update(Request $request, Presence $presence)
    {
        $request->merge([
            'check_in' => date('Y-m-d H:i:s', strtotime($request->check_in)),
            'check_out' => date('Y-m-d H:i:s', strtotime($request->check_out)),
        ]);

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'check_in'    => 'required|date_format:Y-m-d H:i:s',
            'check_out'   => 'required|date_format:Y-m-d H:i:s|after_or_equal:check_in',
            'date'        => 'required|date_format:Y-m-d',
            'status'      => 'required|in:present,absent,late,leave',
        ]);

        $presence->update($request->all());

        return redirect()->route('presences.index')->with('success', 'Presence updated successfully.');
    }

    /**
     * Remove the specified presence from storage.
     */
    public function destroy(Presence $presence)
    {
        $presence->delete();

        return redirect()->route('presences.index')->with('success', 'Presence deleted successfully.');
    }

    /**
     * Clock out the employee for today's presence.
     */
    public function clockOut(Request $request)
    {
        // Find today's presence record for the current employee
        $today = Carbon::now()->format('Y-m-d');
        $presence = Presence::where('employee_id', session('employee_id'))
                           ->where('date', $today)
                           ->whereNull('check_out')
                           ->first();

        if ($presence) {
            // Update the check_out time
            $presence->update([
                'check_out' => Carbon::now()->format('Y-m-d H:i:s')
            ]);

            return redirect()->route('presences.index')->with('success', 'Clock out recorded successfully.');
        }

        return redirect()->route('presences.index')->with('error', 'No active presence found for today.');
    }
}
