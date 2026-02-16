<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkRequest;
use App\Models\WorkRequestLog;
use App\Models\Employee;
use App\Imports\EmployeesImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;


class WorkRequestController extends Controller
{
    /**
     * Display listing of work requests with search and filter
     */
    public function index(Request $request)
    {
        $query = WorkRequest::query();

        if (Auth::user()->employee) {
            $logData['employee_id'] = Auth::user()->employee->id;
        }
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name_of_project', 'LIKE', "%{$search}%")
                  ->orWhere('project_location', 'LIKE', "%{$search}%")
                  ->orWhere('requested_by', 'LIKE', "%{$search}%")
                  ->orWhere('contractor_name', 'LIKE', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $workRequests = $query->latest()->paginate(15)->withQueryString();
            
        return view('admin.work-requests.index', compact('workRequests'));
    }

    /**
     * Show form to create new work request
     */
    public function create()
    {
        return view('admin.work-requests.create');
    }

    /**
     * Search employees for autofill
     */
    public function searchEmployee(Request $request)
    {
        $term = $request->get('term');
        
        $employees = Employee::with('user')
            ->search($term)
            ->limit(10)
            ->get()
            ->map(function ($employee) {
                return [
                    'id'          => $employee->id,
                    'name'        => $employee->user->name,
                    'employee_id' => $employee->employee_id,
                    'position'    => $employee->position,
                    'department'  => $employee->department,
                    'office'      => $employee->office,
                    'email'       => $employee->user->email,
                    'phone'       => $employee->phone,
                ];
            });
            
        return response()->json($employees);
    }


    /**
     * Get employee details for autofill
     */
    public function getEmployee($id)
    {
        $employee = Employee::with('user')->findOrFail($id);

        return response()->json([
            'id'          => $employee->id,
            'name'        => $employee->user->name,
            'employee_id' => $employee->employee_id,
            'position'    => $employee->position,
            'department'  => $employee->department,
            'office'      => $employee->office,
            'email'       => $employee->user->email,
            'phone'       => $employee->phone,
        ]);
    }


    /**
     * Store new work request
     */
    public function store(Request $request)
    {
        $validated = $request->validate(WorkRequest::validationRules());
        
        $workRequest = WorkRequest::create($validated);
        
        // Log the creation with employee_id (only if employee exists)
        $logData = ['description' => 'Work request created'];
        
        if (Auth::user()->employee) {
            $logData['employee_id'] = Auth::user()->employee->id;
        }
        
        $workRequest->addLog(WorkRequestLog::EVENT_CREATED, $logData);
        
        return redirect()
            ->route('admin.work-requests.show', $workRequest)
            ->with('success', 'Work request created successfully!');
    }

    /**
     * Display specific work request
     */
    public function show(WorkRequest $workRequest)
    {
        return view('admin.work-requests.show', compact('workRequest'));
    }

    /**
     * Edit work request
     */
    public function edit(WorkRequest $workRequest)
    {
        if (!$workRequest->canEdit()) {
            return redirect()
                ->route('admin.work-requests.show', $workRequest)
                ->with('error', 'This work request cannot be edited.');
        }
        
        return view('admin.work-requests.edit', compact('workRequest'));
    }

    /**
     * Update work request
     */
    public function update(Request $request, WorkRequest $workRequest)
    {
        if (!$workRequest->canEdit()) {
            return redirect()
                ->route('admin.work-requests.show', $workRequest)
                ->with('error', 'This work request cannot be edited.');
        }
        
        $validated = $request->validate(WorkRequest::validationRules($workRequest->id));
        
        $workRequest->update($validated);
        
        return redirect()
            ->route('admin.work-requests.show', $workRequest)
            ->with('success', 'Work request updated successfully!');
    }

    /**
     * Delete work request
     */
    public function destroy(WorkRequest $workRequest)
    {
        $workRequest->delete();
        
        return redirect()
            ->route('admin.work-requests.index')
            ->with('success', 'Work request deleted successfully!');
    }
    /**
     * Print work request as PDF
     */
    public function print(WorkRequest $workRequest)
    {
        $pdf = Pdf::loadView('admin.work-requests.print', compact('workRequest'))
            ->setPaper('a4', 'portrait');
            
        return $pdf->stream('work-request-' . $workRequest->id . '.pdf');
    }

    /**
     * Download work request as PDF
     */
    public function download(WorkRequest $workRequest)
    {
        $pdf = Pdf::loadView('admin.work-requests.print', compact('workRequest'))
            ->setPaper('a4', 'portrait');
            
        return $pdf->download('work-request-' . $workRequest->id . '.pdf');
    }

    /**
     * Show CSV import form
     */
    public function importForm()
    {
        return view('admin.work-requests.import');
    }
    /**
     * Import work requests from CSV
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        try {
            $import = new EmployeesImport();

            Excel::import($import, $request->file('csv_file'));

            $workRequestCount = WorkRequest::count();

            // Build a flash message that surfaces any row-level failures
            if (!empty($import->failures)) {
                $failureMessages = collect($import->failures)->map(function ($failure) {
                    return "Row {$failure->row()}: " . implode(', ', $failure->errors());
                })->implode(' | ');

                return redirect()
                    ->route('admin.work-requests.import.form')
                    ->with('warning', "Import completed with errors. Total work requests: {$workRequestCount}. Issues: {$failureMessages}");
            }

            return redirect()
                ->route('admin.work-requests.import.form')
                ->with('success', "Work requests imported successfully! Total work requests: {$workRequestCount}");

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.work-requests.import.form')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Update work request status
     */
    public function updateStatus(Request $request, WorkRequest $workRequest)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', WorkRequest::getStatuses()),
        ]);

        $workRequest->update(['status' => $request->status]);

        return redirect()
            ->route('admin.work-requests.show', $workRequest)
            ->with('success', 'Status updated successfully!');
    }

    

    
}