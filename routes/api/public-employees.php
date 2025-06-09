<?php
// routes/api/public-employees.php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Response;
use App\Models\Employee;

Route::get('/api/public-employees', function () {
    return Response::json(
        Employee::select('id', 'fullname', 'email', 'department_id', 'role_id')->get()
    );
});
