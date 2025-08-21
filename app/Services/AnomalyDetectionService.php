<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Presence;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AnomalyDetectionService
{
    /**
     * Detect anomalies in check-in patterns for an employee
     *
     * @param Employee $employee
     * @param int $daysToAnalyze Number of past days to analyze
     * @param int $baselineDays Number of days to use as baseline for normal patterns
     * @return array
     */
    public function detectAnomalies(Employee $employee, int $daysToAnalyze = 30, int $baselineDays = 90): array
    {
        $today = Carbon::now()->startOfDay();
        $recentStartDate = $today->copy()->subDays($daysToAnalyze);
        $baselineEndDate = $today->copy()->subDays($daysToAnalyze);
        $baselineStartDate = $today->copy()->subDays($daysToAnalyze + $baselineDays);
        
        Log::info("Date ranges for employee {$employee->id}:", [
            'today' => $today->format('Y-m-d'),
            'recent_start' => $recentStartDate->format('Y-m-d'),
            'baseline_end' => $baselineEndDate->format('Y-m-d'),
            'baseline_start' => $baselineStartDate->format('Y-m-d'),
        ]);
        
        // Get recent check-ins for analysis period (last 30 days)
        $recentPresences = $employee->presences()
            ->whereDate('date', '>=', $recentStartDate)
            ->orderBy('date', 'desc')
            ->get();
            
        Log::info("Recent presences query result: {$recentPresences->count()} records");
        
        // Get historical check-ins for baseline period (31-120 days ago)
        $baselinePresences = $employee->presences()
            ->whereDate('date', '<', $baselineEndDate)
            ->whereDate('date', '>=', $baselineStartDate)
            ->orderBy('date', 'desc')
            ->get();
            
        Log::info("Baseline presences query result: {$baselinePresences->count()} records");
        
        // If we don't have enough baseline data, use all available historical data
        if ($baselinePresences->count() < 10) {
            Log::info("Insufficient baseline data, getting all historical data");
            
            $baselinePresences = $employee->presences()
                ->whereDate('date', '<', $baselineEndDate)
                ->orderBy('date', 'desc')
                ->limit(60) // Get up to 60 days of historical data
                ->get();
                
            Log::info("Extended baseline query result: {$baselinePresences->count()} records");
            
            // If still no historical data, use recent data as baseline
            if ($baselinePresences->count() < 5) {
                Log::info("No historical data available, using recent data as baseline");
                $baselinePresences = $recentPresences->take(10); // Use first 10 recent records as baseline
            }
        }
        
        // Debug: Show some sample dates
        if ($baselinePresences->count() > 0) {
            Log::info("Sample baseline dates:", [
                'first' => $baselinePresences->first()->date->format('Y-m-d'),
                'last' => $baselinePresences->last()->date->format('Y-m-d'),
            ]);
        }
        
        // Calculate baseline metrics
        $baselineMetrics = $this->calculateBaselineMetrics($baselinePresences);
        
        // Detect anomalies
        $anomalies = [
            'early_check_ins' => $this->detectEarlyCheckIns($recentPresences, $baselineMetrics),
            'late_check_ins' => $this->detectLateCheckIns($recentPresences, $baselineMetrics),
            'long_work_days' => $this->detectLongWorkDays($recentPresences, $baselineMetrics),
            'weekend_work' => $this->detectWeekendWork($recentPresences),
            'inconsistent_patterns' => $this->detectInconsistentPatterns($recentPresences, $baselineMetrics),
            'consecutive_long_days' => $this->detectConsecutiveLongDays($recentPresences),
        ];
        
        // Generate summary text
        $summary = $this->generateSummary($employee, $anomalies, $baselineMetrics);
        
        return [
            'anomalies' => $anomalies,
            'summary' => $summary,
            'baseline_metrics' => $baselineMetrics,
            'recent_metrics' => $this->calculateBaselineMetrics($recentPresences),
            'baseline_data_count' => $baselinePresences->count(),
            'recent_data_count' => $recentPresences->count(),
        ];
    }
    
    /**
     * Calculate baseline metrics from historical presence data
     */
    private function calculateBaselineMetrics(Collection $presences): array
    {
        if ($presences->isEmpty()) {
            Log::info('No presence data available for baseline calculation');
            return [
                'avg_check_in_time' => null,
                'avg_check_out_time' => null,
                'avg_work_hours' => 0,
                'std_dev_check_in' => 0,
                'std_dev_check_out' => 0,
            ];
        }
        
        Log::info("Calculating baseline metrics from {$presences->count()} presence records");
        
        // Calculate average check-in time
        $checkInMinutes = $presences
            ->filter(fn($p) => $p->check_in !== null)
            ->map(fn($p) => $p->check_in->hour * 60 + $p->check_in->minute);
            
        $checkOutMinutes = $presences
            ->filter(fn($p) => $p->check_out !== null)
            ->map(fn($p) => $p->check_out->hour * 60 + $p->check_out->minute);
        
        Log::info("Check-in records: {$checkInMinutes->count()}, Check-out records: {$checkOutMinutes->count()}");
        
        $avgCheckInMinutes = $checkInMinutes->isEmpty() ? 540 : $checkInMinutes->avg(); // Default to 9:00 AM
        $avgCheckOutMinutes = $checkOutMinutes->isEmpty() ? 1020 : $checkOutMinutes->avg(); // Default to 5:00 PM
        
        // Calculate standard deviation
        $stdDevCheckIn = $this->calculateStdDev($checkInMinutes, $avgCheckInMinutes);
        $stdDevCheckOut = $this->calculateStdDev($checkOutMinutes, $avgCheckOutMinutes);
        
        // Calculate average work hours using the accessor method
        $workingHours = $presences
            ->filter(fn($p) => $p->check_in !== null && $p->check_out !== null)
            ->map(fn($p) => $p->working_hours); // This uses the accessor
            
        $avgWorkHours = $workingHours->isEmpty() ? 8 : $workingHours->avg();
        
        Log::info("Working hours records: {$workingHours->count()}, Average: {$avgWorkHours}");
        
        $result = [
            'avg_check_in_time' => $this->minutesToTimeString($avgCheckInMinutes),
            'avg_check_out_time' => $this->minutesToTimeString($avgCheckOutMinutes),
            'avg_work_hours' => round($avgWorkHours, 2),
            'std_dev_check_in' => $stdDevCheckIn,
            'std_dev_check_out' => $stdDevCheckOut,
        ];
        
        Log::info('Baseline metrics calculated', $result);
        
        return $result;
    }
    
    /**
     * Detect early check-ins (significantly earlier than baseline)
     */
    private function detectEarlyCheckIns(Collection $presences, array $baselineMetrics): array
    {
        if (empty($baselineMetrics['avg_check_in_time'])) {
            return [];
        }
        
        $baselineMinutes = $this->timeStringToMinutes($baselineMetrics['avg_check_in_time']);
        $threshold = max(30, $baselineMetrics['std_dev_check_in']); // At least 30 minutes or 1 std dev
        
        return $presences
            ->filter(function ($presence) use ($baselineMinutes, $threshold) {
                if (!$presence->check_in) return false;
                $checkInMinutes = $presence->check_in->hour * 60 + $presence->check_in->minute;
                return $checkInMinutes < ($baselineMinutes - $threshold);
            })
            ->map(function ($presence) use ($baselineMinutes) {
                $checkInMinutes = $presence->check_in->hour * 60 + $presence->check_in->minute;
                $minutesEarly = $baselineMinutes - $checkInMinutes;
                return [
                    'date' => $presence->date->format('Y-m-d'),
                    'check_in' => $presence->check_in->format('H:i'),
                    'minutes_early' => $minutesEarly,
                ];
            })
            ->values()
            ->toArray();
    }
    
    /**
     * Detect late check-ins (significantly later than baseline)
     */
    private function detectLateCheckIns(Collection $presences, array $baselineMetrics): array
    {
        if (empty($baselineMetrics['avg_check_in_time'])) {
            return [];
        }
        
        $baselineMinutes = $this->timeStringToMinutes($baselineMetrics['avg_check_in_time']);
        $threshold = max(30, $baselineMetrics['std_dev_check_in']); // At least 30 minutes or 1 std dev
        
        return $presences
            ->filter(function ($presence) use ($baselineMinutes, $threshold) {
                if (!$presence->check_in) return false;
                $checkInMinutes = $presence->check_in->hour * 60 + $presence->check_in->minute;
                return $checkInMinutes > ($baselineMinutes + $threshold);
            })
            ->map(function ($presence) use ($baselineMinutes) {
                $checkInMinutes = $presence->check_in->hour * 60 + $presence->check_in->minute;
                $minutesLate = $checkInMinutes - $baselineMinutes;
                return [
                    'date' => $presence->date->format('Y-m-d'),
                    'check_in' => $presence->check_in->format('H:i'),
                    'minutes_late' => $minutesLate,
                ];
            })
            ->values()
            ->toArray();
    }
    
    /**
     * Detect long work days (significantly longer than baseline)
     */
    private function detectLongWorkDays(Collection $presences, array $baselineMetrics): array
    {
        $threshold = 2; // Hours longer than average
        
        return $presences
            ->filter(function ($presence) use ($baselineMetrics, $threshold) {
                return $presence->working_hours > ($baselineMetrics['avg_work_hours'] + $threshold);
            })
            ->map(function ($presence) use ($baselineMetrics) {
                $extraHours = $presence->working_hours - $baselineMetrics['avg_work_hours'];
                return [
                    'date' => $presence->date->format('Y-m-d'),
                    'hours_worked' => $presence->working_hours,
                    'extra_hours' => $extraHours,
                ];
            })
            ->values()
            ->toArray();
    }
    
    /**
     * Detect weekend work
     */
    private function detectWeekendWork(Collection $presences): array
    {
        return $presences
            ->filter(function ($presence) {
                $dayOfWeek = $presence->date->dayOfWeek;
                return $dayOfWeek == 0 || $dayOfWeek == 6; // Sunday or Saturday
            })
            ->map(function ($presence) {
                return [
                    'date' => $presence->date->format('Y-m-d'),
                    'day' => $presence->date->format('l'),
                    'hours_worked' => $presence->working_hours,
                ];
            })
            ->values()
            ->toArray();
    }
    
    /**
     * Detect inconsistent patterns (high variability in check-in/out times)
     */
    private function detectInconsistentPatterns(Collection $presences, array $baselineMetrics): bool
    {
        if ($presences->count() < 5) {
            return false;
        }
        
        $checkInMinutes = $presences
            ->filter(fn($p) => $p->check_in !== null)
            ->map(fn($p) => $p->check_in->hour * 60 + $p->check_in->minute);
            
        $stdDev = $this->calculateStdDev($checkInMinutes, $checkInMinutes->avg() ?? 0);
        
        // If standard deviation is more than 2x the baseline, consider it inconsistent
        return $stdDev > ($baselineMetrics['std_dev_check_in'] * 2);
    }
    
    /**
     * Detect consecutive long days (potential burnout indicator)
     */
    private function detectConsecutiveLongDays(Collection $presences): array
    {
        $threshold = 10; // Hours per day considered "long"
        $consecutiveThreshold = 3; // Number of consecutive long days to flag
        
        $consecutiveStreaks = [];
        $currentStreak = [];
        $lastDate = null;
        
        foreach ($presences->sortBy('date') as $presence) {
            if (!$presence->check_in || !$presence->check_out) {
                // Reset streak if missing data
                if (count($currentStreak) >= $consecutiveThreshold) {
                    $consecutiveStreaks[] = $currentStreak;
                }
                $currentStreak = [];
                continue;
            }
            
            if ($presence->working_hours >= $threshold) {
                if ($lastDate && $presence->date->diffInDays($lastDate) == 1) {
                    // Consecutive day
                    $currentStreak[] = [
                        'date' => $presence->date->format('Y-m-d'),
                        'hours_worked' => $presence->working_hours,
                    ];
                } else {
                    // Start new streak
                    if (count($currentStreak) >= $consecutiveThreshold) {
                        $consecutiveStreaks[] = $currentStreak;
                    }
                    $currentStreak = [[
                        'date' => $presence->date->format('Y-m-d'),
                        'hours_worked' => $presence->working_hours,
                    ]];
                }
                $lastDate = $presence->date;
            } else {
                // Reset streak
                if (count($currentStreak) >= $consecutiveThreshold) {
                    $consecutiveStreaks[] = $currentStreak;
                }
                $currentStreak = [];
                $lastDate = null;
            }
        }
        
        // Check if last streak meets threshold
        if (count($currentStreak) >= $consecutiveThreshold) {
            $consecutiveStreaks[] = $currentStreak;
        }
        
        return $consecutiveStreaks;
    }
    
    /**
     * Generate a human-readable summary of the anomalies
     */
    private function generateSummary(Employee $employee, array $anomalies, array $baselineMetrics): string
    {
        $summary = "Analysis of {$employee->fullname}'s check-in patterns:\n\n";
        
        // Add baseline information
        $summary .= "Baseline metrics (normal pattern):\n";
        $summary .= "- Average check-in time: {$baselineMetrics['avg_check_in_time']}\n";
        $summary .= "- Average check-out time: {$baselineMetrics['avg_check_out_time']}\n";
        $summary .= "- Average work hours: {$baselineMetrics['avg_work_hours']} hours\n\n";
        
        // Add anomaly information
        $summary .= "Detected anomalies:\n";
        
        if (count($anomalies['early_check_ins']) > 0) {
            $count = count($anomalies['early_check_ins']);
            $summary .= "- Early check-ins: {$count} instances in the last month\n";
            $summary .= "  Most significant: " . $anomalies['early_check_ins'][0]['check_in'] . 
                " (" . $anomalies['early_check_ins'][0]['minutes_early'] . " minutes early)\n";
        }
        
        if (count($anomalies['late_check_ins']) > 0) {
            $count = count($anomalies['late_check_ins']);
            $summary .= "- Late check-ins: {$count} instances in the last month\n";
            $summary .= "  Most significant: " . $anomalies['late_check_ins'][0]['check_in'] . 
                " (" . $anomalies['late_check_ins'][0]['minutes_late'] . " minutes late)\n";
        }
        
        if (count($anomalies['long_work_days']) > 0) {
            $count = count($anomalies['long_work_days']);
            $summary .= "- Long work days: {$count} instances in the last month\n";
            $summary .= "  Longest day: " . $anomalies['long_work_days'][0]['hours_worked'] . " hours on " . 
                $anomalies['long_work_days'][0]['date'] . "\n";
        }
        
        if (count($anomalies['weekend_work']) > 0) {
            $count = count($anomalies['weekend_work']);
            $summary .= "- Weekend work: {$count} instances in the last month\n";
        }
        
        if ($anomalies['inconsistent_patterns']) {
            $summary .= "- Highly inconsistent check-in patterns detected\n";
        }
        
        if (count($anomalies['consecutive_long_days']) > 0) {
            $streaks = count($anomalies['consecutive_long_days']);
            $longestStreak = max(array_map('count', $anomalies['consecutive_long_days']));
            $summary .= "- Consecutive long work days: {$streaks} streaks detected\n";
            $summary .= "  Longest streak: {$longestStreak} consecutive days\n";
        }

        return $summary;
    }
    
    /**
     * Helper function to calculate standard deviation
     */
    private function calculateStdDev($values, $mean): float
    {
        if (count($values) <= 1) {
            return 0;
        }
        
        $variance = 0;
        foreach ($values as $value) {
            $variance += pow($value - $mean, 2);
        }
        
        return sqrt($variance / (count($values) - 1));
    }
    
    /**
     * Convert minutes since midnight to time string (HH:MM)
     */
    private function minutesToTimeString(float $minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%02d:%02d', $hours, $mins);
    }
    
    /**
     * Convert time string (HH:MM) to minutes since midnight
     */
    private function timeStringToMinutes(string $timeString): int
    {
        list($hours, $minutes) = explode(':', $timeString);
        return (int)$hours * 60 + (int)$minutes;
    }
}
