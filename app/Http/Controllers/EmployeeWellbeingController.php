<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAIAnalysis;
use App\Services\AnomalyDetectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeWellbeingController extends Controller
{
    private AnomalyDetectionService $anomalyService;
    
    public function __construct(AnomalyDetectionService $anomalyService)
    {
        $this->anomalyService = $anomalyService;
        // Middleware is already applied in routes file
    }
    
    /**
     * Display wellbeing dashboard with employee risk levels
     */
    public function index()
    {
        // Get employees with their latest analysis
        $employees = Employee::with(['department', 'workLifeBalanceMetrics' => function($query) {
            $query->latest('week_start')->limit(1);
        }])
        ->whereHas('presences')
        ->withCount('presences')
        ->get()
        ->map(function($employee) {
            // Get latest AI analysis if available
            $latestAnalysis = EmployeeAIAnalysis::where('employee_id', $employee->id)
                ->latest('analysis_date')
                ->first();
                
            return [
                'id' => $employee->id,
                'name' => $employee->fullname,
                'department' => $employee->department->name ?? 'Unknown',
                'risk_level' => $latestAnalysis->risk_level ?? 'unknown',
                'categories' => $latestAnalysis ? implode(', ', $latestAnalysis->categories ?? []) : null,
                'last_analysis' => $latestAnalysis ? $latestAnalysis->analysis_date->format('Y-m-d') : null,
                'overtime_hours' => $employee->workLifeBalanceMetrics->first()->overtime_hours ?? 0,
            ];
        });
        
        // Group by risk level
        $highRisk = $employees->where('risk_level', 'high');
        $mediumRisk = $employees->where('risk_level', 'medium');
        $lowRisk = $employees->where('risk_level', 'low');
        $unknown = $employees->where('risk_level', 'unknown');
        
        return view('wellbeing.index', compact(
            'highRisk', 
            'mediumRisk', 
            'lowRisk', 
            'unknown'
        ));
    }
    
    /**
     * Show detailed wellbeing information for a specific employee
     */
    public function show(Employee $employee)
    {
        // Get all analyses for this employee, ordered by date
        $analysisHistory = EmployeeAIAnalysis::where('employee_id', $employee->id)
            ->orderBy('analysis_date', 'desc')
            ->get();
            
        // Get the latest analysis
        $latestAnalysis = $analysisHistory->first();
            
        // Get latest work-life balance metrics
        $workLifeMetrics = $employee->workLifeBalanceMetrics()
            ->latest('week_start')
            ->limit(10)
            ->get();
            
        // Get presence data for the last 30 days
        $recentPresences = $employee->presences()
            ->where('date', '>=', now()->subDays(30))
            ->orderBy('date', 'desc')
            ->get();
            
        // Run anomaly detection to get fresh data
        $anomalyResults = $this->anomalyService->detectAnomalies($employee);
        
        return view('wellbeing.show', compact(
            'employee',
            'latestAnalysis',
            'analysisHistory',
            'workLifeMetrics',
            'recentPresences',
            'anomalyResults'
        ));
    }
    
    /**
     * Store HR feedback for an employee analysis
     */
    public function storeFeedback(Request $request, EmployeeAIAnalysis $analysis)
    {
        $validated = $request->validate([
            'hr_feedback' => 'required|string|max:1000',
        ]);
        
        $analysis->update([
            'hr_feedback' => $validated['hr_feedback'],
        ]);
        
        return redirect()->back()->with('success', 'Feedback saved successfully');
    }
    
    /**
     * Run manual analysis for an employee
     */
    public function runAnalysis(Employee $employee)
    {
        \Log::info('=== runAnalysis method called ===');
        \Log::info('Employee: ' . $employee->fullname . ' (ID: ' . $employee->id . ')');
        \Log::info('User: ' . auth()->user()->name . ' (' . auth()->user()->email . ')');
        
        try {
            // Run the analysis synchronously for immediate feedback
            \Artisan::call('employees:analyze-patterns', [
                '--employee_id' => $employee->id,
                '--notify-hr' => true,
            ]);
            
            \Log::info('Analysis completed successfully for employee ' . $employee->id);
            
            return redirect()->route('employee.wellbeing', $employee->id)
                ->with('success', 'Analysis completed successfully! Check the results below.');
                
        } catch (\Exception $e) {
            \Log::error('Error in runAnalysis: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->route('employee.wellbeing', $employee->id)
                ->with('error', 'Failed to run analysis: ' . $e->getMessage());
        }
    }
}
