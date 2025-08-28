<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\EmployeeAIAnalysis;
use App\Services\SlackNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TestSlackNotification extends Command
{
    protected $signature = 'test:slack-notification {--employee_id=} {--create-test-analysis}';
    protected $description = 'Test Slack notification functionality';

    private SlackNotificationService $slackService;

    public function __construct(SlackNotificationService $slackService)
    {
        parent::__construct();
        $this->slackService = $slackService;
    }

    public function handle()
    {
        $this->info('Testing Slack Notification Service');
        $this->newLine();

        // Check if webhook URL is configured
        $webhookUrl = config('services.slack.webhook_url') ?? 'https://hooks.slack.com/services/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';
        if (empty($webhookUrl)) {
            $this->warn('⚠️  Slack webhook URL not configured in .env file');
            $this->line('To set up Slack notifications:');
            $this->line('1. Create a Slack app at https://api.slack.com/apps');
            $this->line('2. Add an Incoming Webhook');
            $this->line('3. Add SLACK_WEBHOOK_URL=your_webhook_url to .env');
            $this->newLine();
            $this->line('For now, I\'ll show you what the notification would look like...');
        } else {
            $this->info('✅ Slack webhook URL configured');
        }

        // Get or create test analysis
        $analysis = $this->getTestAnalysis();
        
        if (!$analysis) {
            $this->error('No analysis data found. Run with --create-test-analysis to create test data.');
            return 1;
        }

        $this->info("Testing notification for: {$analysis->employee->fullname}");
        $this->line("Risk Level: {$analysis->risk_level}");
        $this->line("Categories: " . implode(', ', $analysis->categories ?? []));
        $this->newLine();

        // Show what would be sent to Slack
        $this->showSlackMessage($analysis);

        // Send actual notification if webhook is configured
        if (!empty($webhookUrl)) {
            $this->info('Sending notification to Slack...');
            $success = $this->slackService->notifyHR($analysis);
            
            if ($success) {
                $this->info('✅ Slack notification sent successfully!');
            } else {
                $this->error('❌ Failed to send Slack notification');
            }
        }

        return 0;
    }

    private function getTestAnalysis(): ?EmployeeAIAnalysis
    {
        $employeeId = $this->option('employee_id');
        
        if ($employeeId) {
            $analysis = EmployeeAIAnalysis::where('employee_id', $employeeId)->latest()->first();
        } else {
            $analysis = EmployeeAIAnalysis::latest()->first();
        }

        // Create test analysis if requested and none exists
        if (!$analysis && $this->option('create-test-analysis')) {
            $employee = Employee::first();
            if (!$employee) {
                $this->error('No employees found in database');
                return null;
            }

            $analysis = EmployeeAIAnalysis::create([
                'employee_id' => $employee->id,
                'analysis_date' => Carbon::today(),
                'check_in_pattern_summary' => 'Test analysis for Slack notification',
                'ai_insights' => 'This employee has been showing signs of potential burnout with irregular work patterns and extended hours.',
                'risk_level' => 'high',
                'categories' => ['burnout_risk', 'work_life_balance', 'overtime_concern'],
                'recommendations' => 'Immediate intervention recommended. Schedule a wellbeing check-in and consider workload redistribution.',
                'notified_hr' => false,
            ]);

            $this->info('✅ Created test analysis data');
        }

        return $analysis;
    }

    private function showSlackMessage(EmployeeAIAnalysis $analysis): void
    {
        $employee = $analysis->employee;
        
        $this->line('='.str_repeat('=', 60));
        $this->line('SLACK MESSAGE PREVIEW:');
        $this->line('='.str_repeat('=', 60));
        
        // Risk level emoji
        $riskEmoji = match($analysis->risk_level) {
            'high' => '🚨',
            'medium' => '⚠️',
            'low' => '💚',
            default => '📊'
        };
        
        $this->line("{$riskEmoji} *Employee Wellbeing Alert - {$analysis->risk_level} Risk*");
        $this->newLine();
        
        $this->line("*Employee:* {$employee->fullname}");
        $this->line("*Department:* " . ($employee->department->name ?? 'Unknown'));
        $this->line("*Risk Level:* " . ucfirst($analysis->risk_level));
        $this->line("*Analysis Date:* " . $analysis->analysis_date->format('Y-m-d'));
        $this->newLine();
        
        $this->line("*Categories:* " . implode(', ', $analysis->categories ?? []));
        $this->newLine();
        
        $this->line("*AI Insights:*");
        $this->line($analysis->ai_insights);
        $this->newLine();
        
        $this->line("*Recommendations:*");
        $this->line($analysis->recommendations);
        $this->newLine();
        
        $this->line("*Pattern Summary:*");
        $this->line($analysis->check_in_pattern_summary);
        
        $this->line('='.str_repeat('=', 60));
    }
}
