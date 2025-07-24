<?php
// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Payroll;
use App\Models\Presence;
use App\Models\Task;

class DashboardController extends Controller
{
    public function index()
    {
        $employees = Employee::count();
        $departments = Department::count();
        $payrolls = Payroll::count();
        $presences = Presence::count();
        $tasks = Task::all();
        return view('dashboard.index', compact('employees', 'departments', 'payrolls', 'presences', 'tasks'));
    }

    public function presence()
    {
        $statuses = ['present', 'absent', 'late', 'leave'];
        $response = [];

        foreach ($statuses as $status) {
            // Use PostgreSQL compatible date extraction instead of MySQL's MONTH() function
            $rawData = Presence::where('status', $status)
                ->selectRaw('EXTRACT(MONTH FROM date) as month, COUNT(*) as total')
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // Inisialisasi 12 bulan (Jan–Dec) dengan 0
            $data = array_fill(0, 12, 0);
            foreach ($rawData as $item) {
                $data[$item->month - 1] = $item->total;
            }

            $response[$status] = $data;
        }

        return response()->json($response);
    }
}
