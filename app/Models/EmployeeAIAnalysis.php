<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeAIAnalysis extends Model
{
    use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employee_ai_analyses';

    protected $fillable = [
        'employee_id',
        'analysis_date',
        'check_in_pattern_summary',
        'ai_insights',
        'risk_level', // 'low', 'medium', 'high'
        'categories', // JSON array of categories like 'stress', 'burnout', 'personal issues'
        'recommendations',
        'notified_hr',
        'hr_feedback',
    ];

    /**
     * Calculate numerical risk score based on risk level and categories
     */
    public function getRiskScoreAttribute(): int
    {
        // Base score based on risk level
        $baseScore = match($this->risk_level) {
            'low' => 25,
            'medium' => 55,
            'high' => 85,
            default => 0
        };
        
        // Additional points based on number of risk categories
        $categories = $this->categories ?? [];
        $categoryCount = is_array($categories) ? count($categories) : 0;
        $categoryBonus = min($categoryCount * 3, 15); // Max 15 points from categories
        
        // Calculate final score (max 100)
        $finalScore = min($baseScore + $categoryBonus, 100);
        
        return $finalScore;
    }

    /**
     * Format categories for display
     */
    public function getRiskCategoriesAttribute(): string
    {
        $categories = $this->categories ?? [];
        if (empty($categories)) {
            return 'None';
        }
        
        // Convert array to readable format
        if (is_array($categories)) {
            return implode(', ', array_map('ucfirst', $categories));
        }
        
        return 'None';
    }

    protected $casts = [
        'analysis_date' => 'date',
        'categories' => 'array',
        'notified_hr' => 'boolean',
    ];

    /**
     * Get the employee associated with this analysis.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
