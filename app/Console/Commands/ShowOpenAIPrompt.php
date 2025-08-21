<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Services\AnomalyDetectionService;
use App\Services\OpenAIService;
use Illuminate\Console\Command;

class ShowOpenAIPrompt extends Command
{
    protected $signature = 'debug:openai-prompt {employee_id}';
    protected $description = 'Show the exact prompt that would be sent to OpenAI';

    private AnomalyDetectionService $anomalyService;
    private OpenAIService $openAIService;

    public function __construct(AnomalyDetectionService $anomalyService, OpenAIService $openAIService)
    {
        parent::__construct();
        $this->anomalyService = $anomalyService;
        $this->openAIService = $openAIService;
    }

    public function handle()
    {
        $employeeId = $this->argument('employee_id');
        $employee = Employee::find($employeeId);
        
        if (!$employee) {
            $this->error("Employee with ID {$employeeId} not found");
            return 1;
        }
        
        $this->info("Generating OpenAI prompt for: {$employee->fullname}");
        $this->newLine();
        
        // Get anomaly detection results
        $anomalyResults = $this->anomalyService->detectAnomalies($employee);
        
        // Build the prompt using reflection to access the private method
        $reflection = new \ReflectionClass($this->openAIService);
        $method = $reflection->getMethod('buildAnalysisPrompt');
        $method->setAccessible(true);
        
        $prompt = $method->invoke($this->openAIService, $employee->fullname, $anomalyResults['summary']);
        
        $this->line("=".str_repeat("=", 80));
        $this->line("SYSTEM MESSAGE:");
        $this->line("=".str_repeat("=", 80));
        $this->line("You are an HR analytics assistant specialized in analyzing employee work patterns. Your task is to identify potential issues like stress, burnout, or personal difficulties based on check-in/check-out patterns. Provide insights, possible causes, and recommendations. Be empathetic but professional. Focus on employee wellbeing while maintaining privacy and avoiding assumptions.");
        
        $this->newLine();
        $this->line("=".str_repeat("=", 80));
        $this->line("USER MESSAGE (PROMPT):");
        $this->line("=".str_repeat("=", 80));
        $this->line($prompt);
        
        $this->newLine();
        $this->line("=".str_repeat("=", 80));
        $this->line("ADDITIONAL REQUEST PARAMETERS:");
        $this->line("=".str_repeat("=", 80));
        $this->line("Model: gpt-4o");
        $this->line("Temperature: 0.7");
        $this->line("Max Tokens: 1000");
        $this->line("Response Format: JSON Object");
        
        $this->newLine();
        $this->line("=".str_repeat("=", 80));
        $this->line("PATTERN SUMMARY DATA:");
        $this->line("=".str_repeat("=", 80));
        $this->line($anomalyResults['summary']);
        
        return 0;
    }
}
