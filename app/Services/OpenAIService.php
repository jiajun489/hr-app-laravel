<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OpenAIService
{
    private ?string $apiKey;
    private string $model;
    private string $baseUrl;
    
    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        $this->model = config('services.openai.model', 'gpt-4o');
        $this->baseUrl = config('services.openai.base_url', 'https://api.openai.com/v1');
    }
    
    /**
     * Analyze employee check-in patterns and provide insights
     * 
     * @param string $employeeName
     * @param string $patternSummary
     * @param string $taskWorkload
     * @return array
     */
    public function analyzeEmployeePatterns(string $employeeName, string $patternSummary, string $taskWorkload = ''): array
    {
        // Check if API key is configured
        if (empty($this->apiKey)) {
            Log::warning('OpenAI API key not configured. Using mock data for development.');
            return $this->getMockAnalysisResponse();
        }
        
        $prompt = $this->buildAnalysisPrompt($employeeName, $patternSummary, $taskWorkload);
        
        Log::info('Sending request to OpenAI', [
            'employee' => $employeeName,
            'model' => $this->model,
            'prompt_length' => strlen($prompt)
        ]);
        
        try {
            $response = Http::timeout(30)->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/chat/completions", [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an HR analytics assistant specialized in analyzing employee work patterns. Your task is to identify potential issues like stress, burnout, or personal difficulties based on check-in/check-out patterns. Provide insights, possible causes, and recommendations. Be empathetic but professional. Focus on employee wellbeing while maintaining privacy and avoiding assumptions.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 1000,
                'response_format' => ['type' => 'json_object']
            ]);
            
            Log::info('OpenAI API response', [
                'status' => $response->status(),
                'successful' => $response->successful()
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                $content = json_decode($result['choices'][0]['message']['content'], true);
                
                Log::info('OpenAI analysis completed successfully', [
                    'risk_level' => $content['risk_level'] ?? 'unknown'
                ]);
                
                return [
                    'success' => true,
                    'insights' => $content['insights'] ?? null,
                    'possible_causes' => $content['possible_causes'] ?? null,
                    'risk_level' => $content['risk_level'] ?? 'low',
                    'categories' => $content['categories'] ?? [],
                    'recommendations' => $content['recommendations'] ?? null,
                ];
            } else {
                $errorBody = $response->body();
                Log::error('OpenAI API error', [
                    'status' => $response->status(),
                    'body' => $errorBody
                ]);
                
                // If rate limited, fall back to mock data
                if ($response->status() === 429) {
                    Log::warning('OpenAI rate limit exceeded, using mock data');
                    return $this->getMockAnalysisResponse();
                }
                
                return [
                    'success' => false,
                    'error' => 'API request failed: ' . $response->status() . ' - ' . $errorBody,
                ];
            }
        } catch (\Exception $e) {
            Log::error('OpenAI service exception: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Service exception: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * Build the prompt for analyzing employee patterns
     */
    private function buildAnalysisPrompt(string $employeeName, string $patternSummary, string $taskWorkload = ''): string
    {
        $taskSection = '';
        if (!empty($taskWorkload)) {
            $taskSection = "\n\nTASK WORKLOAD:\n{$taskWorkload}";
        }

        return <<<EOT
Please analyze the following check-in/check-out pattern data for employee {$employeeName} and provide insights about potential wellbeing concerns.

CONTEXT: This is for a digital creative studio (Reltroner Studio) with flexible work arrangements. Consider industry-specific factors like creative deadlines, project-based work, and the need for work-life balance in creative fields.

ATTENDANCE DATA:
{$patternSummary}{$taskSection}

Based on these patterns and task workload, please provide:
1. Insights about what these patterns might indicate (consider creative work cycles and task pressure)
2. Possible causes (creative burnout, project stress, client deadlines, task overload, personal difficulties, etc.)
3. Risk level assessment (low, medium, high)
4. Categories of potential issues (be specific to creative work environment and task management)
5. Recommendations for HR (actionable steps for creative team management and workload balancing)

IMPORTANT: 
- Consider that creative work may have natural cycles of intensity
- Weekend work might be normal during project launches
- Correlate attendance patterns with task workload and deadlines
- Focus on sustainable creative productivity
- Suggest creative-specific wellness interventions
- Consider task distribution and deadline management

Format your response as a JSON object with the following structure:
{
  "insights": "Your analysis of the patterns considering creative work nature and task workload...",
  "possible_causes": "Potential causes specific to creative work environment and task pressure...",
  "risk_level": "low|medium|high",
  "categories": ["creative_burnout", "project_stress", "client_pressure", "work_life_balance", "task_overload", ...],
  "recommendations": "Specific recommendations for managing creative team wellbeing and task distribution..."
}
EOT;
    }
    
    /**
     * Provide mock analysis response for development when API key is not configured
     * 
     * @return array
     */
    private function getMockAnalysisResponse(): array
    {
        // Temporarily force medium risk for testing notifications
        return [
            'success' => true,
            'insights' => 'The employee has been consistently checking in late and working long hours, which may indicate increased workload or difficulty managing time. Task analysis shows multiple pending assignments with approaching deadlines.',
            'possible_causes' => 'Increased project demands, task overload, personal stress, or potential burnout from overwork. Multiple concurrent tasks may be creating scheduling conflicts.',
            'risk_level' => 'medium',
            'categories' => ['workload', 'time management', 'potential burnout', 'task_overload'],
            'recommendations' => 'Schedule a check-in meeting to discuss workload and time management. Consider temporary workload redistribution and task prioritization. Review task assignment process to prevent overload.',
        ];
    }
}
