<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkRequestLog;
use App\Models\Employee;
use Illuminate\Http\Request;

class WorkRequestLogController extends Controller
{
    /**
     * Display a listing of the work request logs.
     */
    public function index(Request $request)
    {
        $query = WorkRequestLog::with(['workRequest', 'employee.user'])->latest();

        // Optional filters
        if ($request->filled('employee_id')) {
            $query->byEmployee($request->employee_id);
        }

        if ($request->filled('event')) {
            $query->byEvent($request->event);
        }

        $logs = $query->paginate(20);

        // For filter dropdowns
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

        // Map events to labels for the view
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
