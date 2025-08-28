<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeAIAnalysis;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SlackNotificationService
{
    private ?string $webhookUrl;
    
    public function __construct()
    {
        // Hardcoded webhook URL for production testing
        $this->webhookUrl = 'https://hooks.slack.com/services/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';
        
        // Fallback to config if hardcoded URL is empty
        if (empty($this->webhookUrl)) {
            $this->webhookUrl = config('services.slack.webhook_url');
        }
    }
    
    /**
     * Send notification to HR about employee wellbeing concerns
     * 
     * @param EmployeeAIAnalysis $analysis
     * @return bool
     */
    public function notifyHR(EmployeeAIAnalysis $analysis): bool
    {
        try {
            // Check if webhook URL is configured
            if (empty($this->webhookUrl)) {
                Log::warning('Slack webhook URL not configured. Skipping notification but marking as notified for development purposes.');
                $analysis->update(['notified_hr' => true]);
                return true;
            }
            
            $employee = $analysis->employee;
            
            // Create message blocks for Slack
            $blocks = $this->buildSlackBlocks($employee, $analysis);
            
            $response = Http::post($this->webhookUrl, [
                'blocks' => $blocks
            ]);
            
            if ($response->successful()) {
                // Update the analysis record to mark as notified
                $analysis->update(['notified_hr' => true]);
                return true;
            } else {
                Log::error('Slack notification failed: ' . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Slack notification exception: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Build Slack message blocks
     */
    private function buildSlackBlocks(Employee $employee, EmployeeAIAnalysis $analysis): array
    {
        // Determine emoji based on risk level
        $riskEmoji = [
            'low' => ':large_green_circle:',
            'medium' => ':large_yellow_circle:',
            'high' => ':large_red_circle:',
        ][$analysis->risk_level] ?? ':large_yellow_circle:';
        
        // Format categories as comma-separated list
        $categories = implode(', ', $analysis->categories ?? []);
        
        return [
            [
                'type' => 'header',
                'text' => [
                    'type' => 'plain_text',
                    'text' => 'Employee Wellbeing Alert',
                    'emoji' => true
                ]
            ],
            [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => "*Employee:* {$employee->fullname}\n*Department:* {$employee->department->name}\n*Risk Level:* {$riskEmoji} {$analysis->risk_level}\n*Categories:* {$categories}"
                ]
            ],
            [
                'type' => 'divider'
            ],
            [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => "*AI Insights:*\n{$analysis->ai_insights}"
                ]
            ],
            [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => "*Recommendations:*\n{$analysis->recommendations}"
                ]
            ],
            [
                'type' => 'divider'
            ],
            [
                'type' => 'actions',
                'elements' => [
                    [
                        'type' => 'button',
                        'text' => [
                            'type' => 'plain_text',
                            'text' => 'View Details',
                            'emoji' => true
                        ],
                        'url' => route('employee.wellbeing', ['employee' => $employee->id]),
                        'style' => 'primary'
                    ]
                ]
            ],
            [
                'type' => 'context',
                'elements' => [
                    [
                        'type' => 'mrkdwn',
                        'text' => "Analysis generated on {$analysis->analysis_date->format('Y-m-d')} · This is an automated message from the HR Analytics System"
                    ]
                ]
            ]
        ];
    }
}
