<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkRequestLog;
use App\Models\Employee;
use Illuminate\Http\Request;

class WorkRequestLogController extends Controller
{
    public function index(Request $request)
    {
        // Eager-load workRequest + the acting User (stored as user_id by addLog())
        // Also load employee.user as fallback for any legacy rows
        $query = WorkRequestLog::with(['workRequest', 'user', 'employee.user'])->latest();

        if ($request->filled('employee_id')) {
            $query->byEmployee($request->employee_id);
        }

        if ($request->filled('event')) {
            $query->byEvent($request->event);
        }

        $logs = $query->paginate(20);

        // For the "Filter by Employee" dropdown
        $employees = Employee::join('users', 'employees.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('employees.*')
            ->get();

        $events = [
            WorkRequestLog::EVENT_CREATED,
            WorkRequestLog::EVENT_UPDATED,
            WorkRequestLog::EVENT_STATUS_CHANGED,
            WorkRequestLog::EVENT_SUBMITTED,
            WorkRequestLog::EVENT_INSPECTED,
            WorkRequestLog::EVENT_REVIEWED,
            WorkRequestLog::EVENT_APPROVED,
            WorkRequestLog::EVENT_REJECTED,
            WorkRequestLog::EVENT_ACCEPTED,
            WorkRequestLog::EVENT_DELETED,
            WorkRequestLog::EVENT_RESTORED,
        ];

        $eventLabels = array_reduce($events, function ($carry, $event) {
            $carry[$event] = match ($event) {
                WorkRequestLog::EVENT_CREATED        => 'Created',
                WorkRequestLog::EVENT_UPDATED        => 'Updated',
                WorkRequestLog::EVENT_STATUS_CHANGED => 'Status Changed',
                WorkRequestLog::EVENT_SUBMITTED      => 'Submitted',
                WorkRequestLog::EVENT_INSPECTED      => 'Inspected',
                WorkRequestLog::EVENT_REVIEWED       => 'Reviewed',
                WorkRequestLog::EVENT_APPROVED       => 'Approved',
                WorkRequestLog::EVENT_REJECTED       => 'Rejected',
                WorkRequestLog::EVENT_ACCEPTED       => 'Accepted',
                WorkRequestLog::EVENT_DELETED        => 'Deleted',
                WorkRequestLog::EVENT_RESTORED       => 'Restored',
                default                              => ucfirst(str_replace('_', ' ', $event)),
            };
            return $carry;
        }, []);

        return view('admin.work-request-logs.index', compact('logs', 'employees', 'events', 'eventLabels'));
    }
}