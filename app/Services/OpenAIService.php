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
     * @return array
     */
    public function analyzeEmployeePatterns(string $employeeName, string $patternSummary): array
    {
        // Check if API key is configured
        if (empty($this->apiKey)) {
            Log::warning('OpenAI API key not configured. Using mock data for development.');
            return $this->getMockAnalysisResponse();
        }
        
        $prompt = $this->buildAnalysisPrompt($employeeName, $patternSummary);
        
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
    private function buildAnalysisPrompt(string $employeeName, string $patternSummary): string
    {
        return <<<EOT
Please analyze the following check-in/check-out pattern data for employee {$employeeName} and provide insights about potential wellbeing concerns.

CONTEXT: This is for a digital creative studio (Reltroner Studio) with flexible work arrangements. Consider industry-specific factors like creative deadlines, project-based work, and the need for work-life balance in creative fields.

DATA:
{$patternSummary}

Based on these patterns, please provide:
1. Insights about what these patterns might indicate (consider creative work cycles)
2. Possible causes (creative burnout, project stress, client deadlines, personal difficulties, etc.)
3. Risk level assessment (low, medium, high)
4. Categories of potential issues (be specific to creative work environment)
5. Recommendations for HR (actionable steps for creative team management)

IMPORTANT: 
- Consider that creative work may have natural cycles of intensity
- Weekend work might be normal during project launches
- Focus on sustainable creative productivity
- Suggest creative-specific wellness interventions

Format your response as a JSON object with the following structure:
{
  "insights": "Your analysis of the patterns considering creative work nature...",
  "possible_causes": "Potential causes specific to creative work environment...",
  "risk_level": "low|medium|high",
  "categories": ["creative_burnout", "project_stress", "client_pressure", "work_life_balance", ...],
  "recommendations": "Specific recommendations for managing creative team wellbeing..."
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
        $mockResponses = [
            [
                'success' => true,
                'insights' => 'The employee has been consistently checking in late and working long hours, which may indicate increased workload or difficulty managing time.',
                'possible_causes' => 'Increased project demands, personal stress, or potential burnout from overwork.',
                'risk_level' => 'medium',
                'categories' => ['workload', 'time management', 'potential burnout'],
                'recommendations' => 'Schedule a check-in meeting to discuss workload and time management. Consider temporary workload redistribution if necessary.',
            ],
            [
                'success' => true,
                'insights' => 'The employee has been working weekends consistently for the past month, which indicates a significant work-life balance issue.',
                'possible_causes' => 'Project deadlines, understaffing, or personal financial pressures leading to overwork.',
                'risk_level' => 'high',
                'categories' => ['work-life balance', 'overwork', 'burnout risk'],
                'recommendations' => 'Immediate intervention needed. Schedule a wellbeing check-in and consider mandatory time off to prevent burnout.',
            ],
            [
                'success' => true,
                'insights' => 'The employee shows irregular check-in patterns with occasional very early starts followed by late check-ins.',
                'possible_causes' => 'Potential personal issues affecting sleep or commute, or possible health concerns.',
                'risk_level' => 'medium',
                'categories' => ['irregular schedule', 'potential personal issues'],
                'recommendations' => 'Have an informal check-in to ensure the employee has the support they need, and consider flexible working arrangements if appropriate.',
            ],
            [
                'success' => true,
                'insights' => 'No significant anomalies detected in the employee\'s check-in patterns.',
                'possible_causes' => 'The employee appears to be maintaining a healthy work schedule.',
                'risk_level' => 'low',
                'categories' => ['healthy patterns'],
                'recommendations' => 'Continue regular check-ins as part of normal management process.',
            ],
        ];
        
        // Return a random mock response
        return $mockResponses[array_rand($mockResponses)];
    }
}
