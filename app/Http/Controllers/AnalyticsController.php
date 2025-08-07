<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Task;
use App\Models\Presence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // Gather data from database
        $employees = Employee::with(['department', 'role'])->get();
        $tasks = Task::with('employee')->get();
        $presences = Presence::with('employee')->get();

        // Prepare data for Python API
        $data = [
            'users' => $employees->map(function ($emp) {
                return [
                    'id' => $emp->id,
                    'name' => $emp->fullname,
                    'department' => $emp->department->name ?? 'Unknown'
                ];
            }),
            'tasks' => $tasks->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'assigned_to' => $task->assigned_to,
                    'status' => $task->status,
                    'due_date' => $task->due_date,
                    'created_at' => $task->created_at->toISOString()
                ];
            }),
            'presences' => $presences->map(function ($presence) {
                return [
                    'employee_id' => $presence->employee_id,
                    'date' => $presence->date,
                    'check_in' => $presence->check_in?->toISOString(),
                    'check_out' => $presence->check_out?->toISOString(),
                    'status' => $presence->status
                ];
            })
        ];

        try {
            // Call Python microservice
            $response = Http::withHeaders([
                'X-API-KEY' => env('PYTHON_API_KEY', 'default-key'),
                'Content-Type' => 'application/json'
            ])->post(env('PYTHON_API_URL', 'http://127.0.0.1:5000') . '/analyze', $data);

            $analytics = $response->successful() ? $response->json() : [
                'workload' => [],
                'bottlenecks' => ['pending' => 0, 'in_progress' => 0, 'completed' => 0],
                'productivity' => [],
                'overdue' => []
            ];
        } catch (\Exception $e) {
            $analytics = [
                'workload' => [],
                'bottlenecks' => ['pending' => 0, 'in_progress' => 0, 'completed' => 0],
                'productivity' => [],
                'overdue' => []
            ];
        }

        return view('admin.analytics.index', compact('analytics', 'employees'));
    }
}