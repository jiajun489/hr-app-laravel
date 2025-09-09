<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\EmployeeAIAnalysis;
use App\Services\AnomalyDetectionService;
use App\Services\OpenAIService;
use App\Services\SlackNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AnalyzeEmployeePatterns extends Command
{
    protected $signature = 'employees:analyze-patterns {--employee_id=} {--notify-hr} {--days=30} {--baseline=90}';
    protected $description = 'Analyze employee check-in patterns and detect anomalies';

    private AnomalyDetectionService $anomalyService;
    private OpenAIService $openAIService;
    private SlackNotificationService $slackService;

    public function __construct(
        AnomalyDetectionService $anomalyService,
        OpenAIService $openAIService,
        SlackNotificationService $slackService
    ) {
        parent::__construct();
        $this->anomalyService = $anomalyService;
        $this->openAIService = $openAIService;
        $this->slackService = $slackService;
    }

    public function handle()
    {
        $employeeId = $this->option('employee_id');
        $notifyHR = $this->option('notify-hr');
        $daysToAnalyze = (int)$this->option('days');
        $baselineDays = (int)$this->option('baseline');

        $query = Employee::query()->where('status', 'active');
        
        if ($employeeId) {
            $query->where('id', $employeeId);
        }
        
        $employees = $query->get();
        $count = $employees->count();
        
        $this->info("Analyzing patterns for {$count} employees...");
        $bar = $this->output->createProgressBar($count);
        
        foreach ($employees as $employee) {
            try {
                $this->analyzeEmployee($employee, $notifyHR, $daysToAnalyze, $baselineDays);
            } catch (\Exception $e) {
                Log::error("Error analyzing employee {$employee->id}: " . $e->getMessage());
                $this->error("Error analyzing employee {$employee->fullname}: " . $e->getMessage());
            }
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('Analysis completed!');
        
        return 0;
    }
    
    /**
     * Analyze patterns for a single employee
     */
    private function analyzeEmployee(
        Employee $employee, 
        bool $notifyHR, 
        int $daysToAnalyze, 
        int $baselineDays
    ): void {
        // Skip if no check-in data
        if ($employee->presences()->count() === 0) {
            $this->line("Skipping {$employee->fullname} - no presence data");
            return;
        }
        
        // 1. Detect anomalies
        $anomalyResults = $this->anomalyService->detectAnomalies(
            $employee, 
            $daysToAnalyze, 
            $baselineDays
        );
        
        // Debug: Show detected anomalies
        $this->line("Detected anomalies:");
        $this->line("- Late check-ins: " . count($anomalyResults['anomalies']['late_check_ins']));
        $this->line("- Early check-ins: " . count($anomalyResults['anomalies']['early_check_ins']));
        $this->line("- Long work days: " . count($anomalyResults['anomalies']['long_work_days']));
        $this->line("- Weekend work: " . count($anomalyResults['anomalies']['weekend_work']));
        $this->line("- Inconsistent patterns: " . ($anomalyResults['anomalies']['inconsistent_patterns'] ? 'YES' : 'NO'));
        $this->line("- Consecutive long days: " . count($anomalyResults['anomalies']['consecutive_long_days']));
        $this->newLine();
        
        // 2. Get AI insights if anomalies detected
        $hasSignificantAnomalies = $this->hasSignificantAnomalies($anomalyResults['anomalies']);
        
        $this->line("Significant anomalies detected: " . ($hasSignificantAnomalies ? 'YES' : 'NO'));
        
        if ($hasSignificantAnomalies) {
            $this->line("Sending data to OpenAI for analysis...");
            
            // Get AI analysis
            $aiAnalysis = $this->openAIService->analyzeEmployeePatterns(
                $employee->fullname, 
                $anomalyResults['summary'],
                $anomalyResults['task_workload']
            );
            
            if (!$aiAnalysis['success']) {
                $this->error("OpenAI API error for employee {$employee->id}: " . ($aiAnalysis['error'] ?? 'Unknown error'));
                Log::error("OpenAI API error for employee {$employee->id}: " . ($aiAnalysis['error'] ?? 'Unknown error'));
                return;
            }
            
            $this->line("AI Analysis completed. Risk level: " . $aiAnalysis['risk_level']);
            
            // 3. Store the analysis results
            $analysis = EmployeeAIAnalysis::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'analysis_date' => Carbon::today(),
                ],
                [
                    'check_in_pattern_summary' => $anomalyResults['summary'],
                    'ai_insights' => $aiAnalysis['insights'],
                    'risk_level' => $aiAnalysis['risk_level'],
                    'categories' => $aiAnalysis['categories'],
                    'recommendations' => $aiAnalysis['recommendations'],
                    'notified_hr' => false, // Always reset to false for new analysis
                ]
            );
            
            // Force reset notified_hr to false for manual analysis runs
            $analysis->update(['notified_hr' => false]);
            
            $this->line("Analysis saved to database with ID: " . $analysis->id);
            
            // 4. Notify HR if requested and risk level is medium or high
            if ($notifyHR && in_array($aiAnalysis['risk_level'], ['medium', 'high'])) {
                $this->line("Attempting to notify HR for {$employee->fullname} (Risk: {$aiAnalysis['risk_level']})");
                $notified = $this->slackService->notifyHR($analysis);
                if ($notified) {
                    $this->line("✅ HR notified about {$employee->fullname}");
                } else {
                    $this->line("❌ Failed to notify HR about {$employee->fullname}");
                }
            } else {
                $this->line("Skipping HR notification - Risk level: {$aiAnalysis['risk_level']}, Notify HR: " . ($notifyHR ? 'true' : 'false'));
            }
        }
    }
    
    /**
     * Check if there are significant anomalies that warrant AI analysis
     */
    private function hasSignificantAnomalies(array $anomalies): bool
    {
        // For testing purposes, make it less strict
        
        // Check for consecutive long days (burnout risk)
        if (!empty($anomalies['consecutive_long_days'])) {
            return true;
        }
        
        // Check for weekend work (lowered threshold)
        if (count($anomalies['weekend_work']) >= 1) {
            return true;
        }
        
        // Check for long work days (lowered threshold)
        if (count($anomalies['long_work_days']) >= 1) {
            return true;
        }
        
        // Check for inconsistent patterns
        if ($anomalies['inconsistent_patterns']) {
            return true;
        }
        
        // Check for significant late check-ins (lowered threshold)
        if (count($anomalies['late_check_ins']) >= 1) {
            return true;
        }
        
        // Check for significant early check-ins (lowered threshold)
        if (count($anomalies['early_check_ins']) >= 1) {
            return true;
        }
        
        return false;
    }
}
