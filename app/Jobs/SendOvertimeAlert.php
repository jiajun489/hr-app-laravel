<?php

namespace App\Jobs;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOvertimeAlert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Employee $employee,
        public float $overtimeHours
    ) {}

    public function handle(): void
    {
        if ($this->employee->manager) {
            Mail::raw(
                "Alert: {$this->employee->fullname} has worked {$this->overtimeHours} overtime hours in the past two weeks. Please review their workload to prevent burnout.",
                function ($message) {
                    $message->to($this->employee->manager->email)
                        ->subject('Overtime Alert - ' . $this->employee->fullname);
                }
            );
        }
    }
}