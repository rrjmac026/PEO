<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConcretePouring;
use App\Models\WorkRequest;
use App\Models\Memo;
use App\Models\User;
use App\Services\Reports\ReportPdfService;
use App\Services\Reports\ReportExcelService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminReportsController extends Controller
{
    public function __construct(
        protected ReportPdfService   $pdfService,
        protected ReportExcelService $excelService,
    ) {}

    // =========================================================================
    // MAIN DASHBOARD  —  GET /admin/reports
    // =========================================================================

    /**
     * Landing page with summary cards + quick-filter links per module.
     */
    public function index(Request $request)
    {
        $range = $this->resolveRange($request);

        // ── Work Requests ────────────────────────────────────────────────────
        $wrBase = WorkRequest::whereBetween('created_at', [$range['from'], $range['to']]);

        $workRequestStats = [
            'total'      => (clone $wrBase)->count(),
            'approved'   => (clone $wrBase)->where('status', WorkRequest::STATUS_APPROVED)->count(),
            'rejected'   => (clone $wrBase)->where('status', WorkRequest::STATUS_REJECTED)->count(),
            'pending'    => (clone $wrBase)->whereNotIn('status', [
                                WorkRequest::STATUS_APPROVED,
                                WorkRequest::STATUS_REJECTED,
                            ])->count(),
            'in_review'  => (clone $wrBase)->where('status', WorkRequest::STATUS_IN_REVIEW)->count(),
            'by_status'  => (clone $wrBase)
                                ->select('status', DB::raw('count(*) as total'))
                                ->groupBy('status')
                                ->pluck('total', 'status'),
            'by_month'   => (clone $wrBase)
                                ->select(DB::raw("DATE_FORMAT(created_at,'%Y-%m') as month"), DB::raw('count(*) as total'))
                                ->groupBy('month')
                                ->orderBy('month')
                                ->pluck('total', 'month'),
        ];

        // ── Concrete Pourings ────────────────────────────────────────────────
        $cpBase = ConcretePouring::whereBetween('created_at', [$range['from'], $range['to']]);

        $concretePouringStats = [
            'total'        => (clone $cpBase)->count(),
            'approved'     => (clone $cpBase)->where('status', 'approved')->count(),
            'disapproved'  => (clone $cpBase)->where('status', 'disapproved')->count(),
            'pending'      => (clone $cpBase)->where('status', 'requested')->count(),
            'total_volume' => round((clone $cpBase)->sum('estimated_volume'), 2),
            'avg_volume'   => round((clone $cpBase)->avg('estimated_volume'), 2),
            'by_contractor' => (clone $cpBase)
                                ->select('contractor', DB::raw('count(*) as total'), DB::raw('sum(estimated_volume) as volume'))
                                ->groupBy('contractor')
                                ->orderByDesc('total')
                                ->limit(10)
                                ->get(),
            'by_month'     => (clone $cpBase)
                                ->select(DB::raw("DATE_FORMAT(created_at,'%Y-%m') as month"), DB::raw('count(*) as total'))
                                ->groupBy('month')
                                ->orderBy('month')
                                ->pluck('total', 'month'),
        ];

        // ── Memos ────────────────────────────────────────────────────────────
        $memoBase = Memo::whereBetween('created_at', [$range['from'], $range['to']]);

        $memoStats = [
            'total'     => (clone $memoBase)->count(),
            'sent'      => (clone $memoBase)->where('status', Memo::STATUS_SENT)->count(),
            'draft'     => (clone $memoBase)->where('status', Memo::STATUS_DRAFT)->count(),
            'scheduled' => (clone $memoBase)->where('status', Memo::STATUS_SCHEDULED)->count(),
            'by_type'   => (clone $memoBase)
                                ->select('type', DB::raw('count(*) as total'))
                                ->groupBy('type')
                                ->pluck('total', 'type'),
        ];

        // ── Users overview ───────────────────────────────────────────────────
        $userStats = [
            'total'    => User::count(),
            'by_role'  => User::select('role', DB::raw('count(*) as total'))
                               ->groupBy('role')
                               ->pluck('total', 'role'),
        ];

        return view('admin.reports.index', compact(
            'range',
            'workRequestStats',
            'concretePouringStats',
            'memoStats',
            'userStats',
        ));
    }

    // =========================================================================
    // WORK REQUESTS MODULE  —  GET /admin/reports/work-requests
    // =========================================================================

    public function workRequests(Request $request)
    {
        $range    = $this->resolveRange($request);
        $filters  = $this->wrFilters($request);
        $query    = $this->buildWrQuery($range, $filters);

        $workRequests      = (clone $query)->with(['assignedProvincialEngineer', 'assignedByAdmin'])->get();
        $summary           = $this->buildWrSummary($workRequests);
        $contractorBreakdown = $this->buildWrContractorBreakdown($workRequests);
        $reviewerBreakdown   = $this->buildWrReviewerBreakdown($workRequests);
        $monthlyTrend        = $this->buildWrMonthlyTrend($range);
        $statusBreakdown     = $workRequests->groupBy('status')->map->count();

        $contractors = WorkRequest::select('contractor_name')->distinct()
                                  ->whereNotNull('contractor_name')
                                  ->orderBy('contractor_name')
                                  ->pluck('contractor_name');

        return view('admin.reports.work-requests', compact(
            'range', 'filters',
            'workRequests', 'summary',
            'contractorBreakdown', 'reviewerBreakdown',
            'monthlyTrend', 'statusBreakdown',
            'contractors',
        ));
    }

    public function workRequestsPdf(Request $request)
    {
        $range        = $this->resolveRange($request);
        $filters      = $this->wrFilters($request);
        $workRequests = $this->buildWrQuery($range, $filters)
                             ->with(['assignedProvincialEngineer', 'assignedByAdmin'])
                             ->get();
        $summary      = $this->buildWrSummary($workRequests);

        $pdf      = $this->pdfService->workRequestsReport($workRequests, $summary, $range, $filters);
        $filename = 'work-requests-report-' . $range['from']->format('Ymd') . '-' . $range['to']->format('Ymd') . '.pdf';

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function workRequestsExcel(Request $request)
    {
        $range        = $this->resolveRange($request);
        $filters      = $this->wrFilters($request);
        $workRequests = $this->buildWrQuery($range, $filters)
                             ->with(['assignedProvincialEngineer', 'assignedByAdmin'])
                             ->get();
        $summary      = $this->buildWrSummary($workRequests);

        $filename = 'work-requests-report-' . $range['from']->format('Ymd') . '-' . $range['to']->format('Ymd') . '.xlsx';
        $path     = $this->excelService->workRequestsReport($workRequests, $summary, $range, $filters);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    // =========================================================================
    // CONCRETE POURINGS MODULE  —  GET /admin/reports/concrete-pourings
    // =========================================================================

    public function concretePourings(Request $request)
    {
        $range   = $this->resolveRange($request);
        $filters = $this->cpFilters($request);
        $query   = $this->buildCpQuery($range, $filters);

        $concretePourings    = (clone $query)->with(['requestedBy', 'meMtqaChecker', 'residentEngineer', 'notedByEngineer'])->get();
        $summary             = $this->buildCpSummary($concretePourings);
        $contractorBreakdown = $this->buildCpContractorBreakdown($concretePourings);
        $checklistStats      = $this->buildCpChecklistStats($concretePourings);
        $monthlyTrend        = $this->buildCpMonthlyTrend($range);
        $volumeByContractor  = $concretePourings->groupBy('contractor')
                                                ->map(fn ($g) => round($g->sum('estimated_volume'), 2));

        $contractors = ConcretePouring::select('contractor')->distinct()
                                      ->whereNotNull('contractor')
                                      ->orderBy('contractor')
                                      ->pluck('contractor');

        return view('admin.reports.concrete-pourings', compact(
            'range', 'filters',
            'concretePourings', 'summary',
            'contractorBreakdown', 'checklistStats',
            'monthlyTrend', 'volumeByContractor',
            'contractors',
        ));
    }

    public function concretePouringsPdf(Request $request)
    {
        $range           = $this->resolveRange($request);
        $filters         = $this->cpFilters($request);
        $concretePourings = $this->buildCpQuery($range, $filters)
                                 ->with(['requestedBy', 'meMtqaChecker', 'residentEngineer', 'notedByEngineer'])
                                 ->get();
        $summary         = $this->buildCpSummary($concretePourings);

        $pdf      = $this->pdfService->concretePouringsReport($concretePourings, $summary, $range, $filters);
        $filename = 'concrete-pourings-report-' . $range['from']->format('Ymd') . '-' . $range['to']->format('Ymd') . '.pdf';

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function concretePouringsExcel(Request $request)
    {
        $range           = $this->resolveRange($request);
        $filters         = $this->cpFilters($request);
        $concretePourings = $this->buildCpQuery($range, $filters)
                                 ->with(['requestedBy', 'meMtqaChecker', 'residentEngineer', 'notedByEngineer'])
                                 ->get();
        $summary         = $this->buildCpSummary($concretePourings);

        $filename = 'concrete-pourings-report-' . $range['from']->format('Ymd') . '-' . $range['to']->format('Ymd') . '.xlsx';
        $path     = $this->excelService->concretePouringsReport($concretePourings, $summary, $range, $filters);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    // =========================================================================
    // MEMOS MODULE  —  GET /admin/reports/memos
    // =========================================================================

    public function memos(Request $request)
    {
        $range   = $this->resolveRange($request);
        $filters = $this->memoFilters($request);
        $query   = $this->buildMemoQuery($range, $filters);

        $memos         = (clone $query)->with(['sender', 'memoRecipients'])->get();
        $summary       = $this->buildMemoSummary($memos);
        $typeBreakdown = $this->buildMemoTypeBreakdown($memos);
        $monthlyTrend  = $this->buildMemoMonthlyTrend($range);
        $readRates     = $memos->map(fn ($m) => [
            'id'        => $m->id,
            'subject'   => $m->subject,
            'sent_at'   => $m->sent_at,
            'total'     => $m->memoRecipients->count(),
            'read'      => $m->memoRecipients->whereNotNull('read_at')->count(),
            'read_rate' => $m->recipient_count > 0
                               ? round(($m->memoRecipients->whereNotNull('read_at')->count() / $m->memoRecipients->count()) * 100)
                               : 0,
        ]);

        return view('admin.reports.memos', compact(
            'range', 'filters',
            'memos', 'summary',
            'typeBreakdown', 'monthlyTrend',
            'readRates',
        ));
    }

    public function memosPdf(Request $request)
    {
        $range   = $this->resolveRange($request);
        $filters = $this->memoFilters($request);
        $memos   = $this->buildMemoQuery($range, $filters)
                        ->with(['sender', 'memoRecipients'])
                        ->get();
        $summary = $this->buildMemoSummary($memos);

        $pdf      = $this->pdfService->memosReport($memos, $summary, $range, $filters);
        $filename = 'memos-report-' . $range['from']->format('Ymd') . '-' . $range['to']->format('Ymd') . '.pdf';

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function memosExcel(Request $request)
    {
        $range   = $this->resolveRange($request);
        $filters = $this->memoFilters($request);
        $memos   = $this->buildMemoQuery($range, $filters)
                        ->with(['sender', 'memoRecipients'])
                        ->get();
        $summary = $this->buildMemoSummary($memos);

        $filename = 'memos-report-' . $range['from']->format('Ymd') . '-' . $range['to']->format('Ymd') . '.xlsx';
        $path     = $this->excelService->memosReport($memos, $summary, $range, $filters);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    // =========================================================================
    // COMBINED / OVERVIEW MODULE  —  GET /admin/reports/overview
    // =========================================================================

    public function overview(Request $request)
    {
        $range = $this->resolveRange($request);

        $wrCount  = WorkRequest::whereBetween('created_at', [$range['from'], $range['to']])->count();
        $cpCount  = ConcretePouring::whereBetween('created_at', [$range['from'], $range['to']])->count();
        $memoCount = Memo::whereBetween('created_at', [$range['from'], $range['to']])->count();

        $wrApproved = WorkRequest::whereBetween('created_at', [$range['from'], $range['to']])
                                 ->where('status', WorkRequest::STATUS_APPROVED)->count();
        $cpApproved = ConcretePouring::whereBetween('created_at', [$range['from'], $range['to']])
                                     ->where('status', 'approved')->count();

        return view('admin.reports.overview', compact(
            'range', 'wrCount', 'cpCount', 'memoCount', 'wrApproved', 'cpApproved',
        ));
    }

    public function overviewPdf(Request $request)
    {
        $range = $this->resolveRange($request);

        $workRequests     = WorkRequest::whereBetween('created_at', [$range['from'], $range['to']])->get();
        $concretePourings = ConcretePouring::whereBetween('created_at', [$range['from'], $range['to']])->get();
        $memos            = Memo::whereBetween('created_at', [$range['from'], $range['to']])->with('memoRecipients')->get();

        $wrSummary  = $this->buildWrSummary($workRequests);
        $cpSummary  = $this->buildCpSummary($concretePourings);
        $memoSummary = $this->buildMemoSummary($memos);

        $pdf      = $this->pdfService->overviewReport($workRequests, $wrSummary, $concretePourings, $cpSummary, $memos, $memoSummary, $range);
        $filename = 'overview-report-' . $range['from']->format('Ymd') . '-' . $range['to']->format('Ymd') . '.pdf';

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function overviewExcel(Request $request)
    {
        $range = $this->resolveRange($request);

        $workRequests     = WorkRequest::whereBetween('created_at', [$range['from'], $range['to']])->get();
        $concretePourings = ConcretePouring::whereBetween('created_at', [$range['from'], $range['to']])->get();
        $memos            = Memo::whereBetween('created_at', [$range['from'], $range['to']])->with('memoRecipients')->get();

        $wrSummary  = $this->buildWrSummary($workRequests);
        $cpSummary  = $this->buildCpSummary($concretePourings);
        $memoSummary = $this->buildMemoSummary($memos);

        $filename = 'overview-report-' . $range['from']->format('Ymd') . '-' . $range['to']->format('Ymd') . '.xlsx';
        $path     = $this->excelService->overviewReport(
            $workRequests, $wrSummary,
            $concretePourings, $cpSummary,
            $memos, $memoSummary,
            $range,
        );

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    // =========================================================================
    // PRIVATE — DATE RANGE RESOLVER
    // =========================================================================

    /**
     * Resolve date range from request.
     * Supports: preset shortcuts (this_month, last_month, this_year, last_7_days, last_30_days)
     *           or explicit date_from / date_to pair.
     */
    private function resolveRange(Request $request): array
    {
        $preset = $request->input('preset');

        [$from, $to] = match ($preset) {
            'this_month'  => [now()->startOfMonth(), now()->endOfMonth()],
            'last_month'  => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
            'this_year'   => [now()->startOfYear(), now()->endOfYear()],
            'last_7_days' => [now()->subDays(6)->startOfDay(), now()->endOfDay()],
            'last_30_days'=> [now()->subDays(29)->startOfDay(), now()->endOfDay()],
            'last_quarter'=> [now()->subQuarter()->firstOfQuarter(), now()->subQuarter()->lastOfQuarter()],
            default       => [
                Carbon::parse($request->input('date_from', now()->startOfMonth()->format('Y-m-d')))->startOfDay(),
                Carbon::parse($request->input('date_to',   now()->endOfMonth()->format('Y-m-d')))->endOfDay(),
            ],
        };

        return [
            'from'          => $from,
            'to'            => $to,
            'preset'        => $preset,
            'from_display'  => $from->format('M d, Y'),
            'to_display'    => $to->format('M d, Y'),
            'label'         => $from->format('M d, Y') . ' – ' . $to->format('M d, Y'),
        ];
    }

    // =========================================================================
    // PRIVATE — FILTER RESOLVERS
    // =========================================================================

    private function wrFilters(Request $request): array
    {
        return [
            'status'     => $request->input('status'),
            'contractor' => $request->input('contractor'),
            'step'       => $request->input('step'),
        ];
    }

    private function cpFilters(Request $request): array
    {
        return [
            'status'     => $request->input('status'),
            'contractor' => $request->input('contractor'),
        ];
    }

    private function memoFilters(Request $request): array
    {
        return [
            'status' => $request->input('status'),
            'type'   => $request->input('type'),
        ];
    }

    // =========================================================================
    // PRIVATE — QUERY BUILDERS
    // =========================================================================

    private function buildWrQuery(array $range, array $filters)
    {
        $q = WorkRequest::whereBetween('created_at', [$range['from'], $range['to']]);
        if (!empty($filters['status']))     $q->where('status', $filters['status']);
        if (!empty($filters['contractor'])) $q->where('contractor_name', $filters['contractor']);
        if (!empty($filters['step']))       $q->where('current_review_step', $filters['step']);
        return $q;
    }

    private function buildCpQuery(array $range, array $filters)
    {
        $q = ConcretePouring::whereBetween('created_at', [$range['from'], $range['to']]);
        if (!empty($filters['status']))     $q->where('status', $filters['status']);
        if (!empty($filters['contractor'])) $q->where('contractor', $filters['contractor']);
        return $q;
    }

    private function buildMemoQuery(array $range, array $filters)
    {
        $q = Memo::whereBetween('created_at', [$range['from'], $range['to']]);
        if (!empty($filters['status'])) $q->where('status', $filters['status']);
        if (!empty($filters['type']))   $q->where('type', $filters['type']);
        return $q;
    }

    // =========================================================================
    // PRIVATE — SUMMARY BUILDERS
    // =========================================================================

    private function buildWrSummary($workRequests): array
    {
        return [
            'total'        => $workRequests->count(),
            'approved'     => $workRequests->where('status', WorkRequest::STATUS_APPROVED)->count(),
            'rejected'     => $workRequests->where('status', WorkRequest::STATUS_REJECTED)->count(),
            'pending'      => $workRequests->whereNotIn('status', [
                                 WorkRequest::STATUS_APPROVED, WorkRequest::STATUS_REJECTED,
                             ])->count(),
            'in_review'    => $workRequests->where('status', WorkRequest::STATUS_IN_REVIEW)->count(),
            'approval_rate'=> $workRequests->count() > 0
                                 ? round(($workRequests->where('status', WorkRequest::STATUS_APPROVED)->count() / $workRequests->count()) * 100, 1)
                                 : 0,
        ];
    }

    private function buildWrSummary2($workRequests): array
    {
        return $this->buildWrSummary($workRequests);
    }

    private function buildCpSummary($concretePourings): array
    {
        return [
            'total'                   => $concretePourings->count(),
            'approved'                => $concretePourings->where('status', 'approved')->count(),
            'disapproved'             => $concretePourings->where('status', 'disapproved')->count(),
            'pending'                 => $concretePourings->where('status', 'requested')->count(),
            'total_volume'            => round($concretePourings->sum('estimated_volume'), 2),
            'avg_volume'              => round($concretePourings->avg('estimated_volume') ?? 0, 2),
            'avg_checklist_completion'=> round($concretePourings->avg(fn ($p) => $p->checklist_progress) ?? 0, 1),
            'approval_rate'           => $concretePourings->count() > 0
                                            ? round(($concretePourings->where('status', 'approved')->count() / $concretePourings->count()) * 100, 1)
                                            : 0,
        ];
    }

    private function buildMemoSummary($memos): array
    {
        return [
            'total'        => $memos->count(),
            'sent'         => $memos->where('status', Memo::STATUS_SENT)->count(),
            'draft'        => $memos->where('status', Memo::STATUS_DRAFT)->count(),
            'scheduled'    => $memos->where('status', Memo::STATUS_SCHEDULED)->count(),
            'cancelled'    => $memos->where('status', Memo::STATUS_CANCELLED)->count(),
            'total_recipients' => $memos->sum(fn ($m) => $m->memoRecipients->count()),
            'total_read'   => $memos->sum(fn ($m) => $m->memoRecipients->whereNotNull('read_at')->count()),
            'avg_read_rate'=> $memos->where('status', Memo::STATUS_SENT)->count() > 0
                                 ? round($memos->where('status', Memo::STATUS_SENT)->avg('read_rate') ?? 0, 1)
                                 : 0,
        ];
    }

    // =========================================================================
    // PRIVATE — BREAKDOWN BUILDERS
    // =========================================================================

    private function buildWrContractorBreakdown($workRequests): \Illuminate\Support\Collection
    {
        return $workRequests->groupBy('contractor_name')->map(fn ($g, $name) => [
            'name'       => $name ?: '(Unknown)',
            'total'      => $g->count(),
            'approved'   => $g->where('status', WorkRequest::STATUS_APPROVED)->count(),
            'rejected'   => $g->where('status', WorkRequest::STATUS_REJECTED)->count(),
            'pending'    => $g->whereNotIn('status', [WorkRequest::STATUS_APPROVED, WorkRequest::STATUS_REJECTED])->count(),
        ])->sortByDesc('total');
    }

    private function buildWrReviewerBreakdown($workRequests): \Illuminate\Support\Collection
    {
        return $workRequests->groupBy('current_review_step')->map(fn ($g, $step) => [
            'step'  => $step ?: 'Complete',
            'count' => $g->count(),
        ])->sortByDesc('count');
    }

    private function buildCpContractorBreakdown($concretePourings): \Illuminate\Support\Collection
    {
        return $concretePourings->groupBy('contractor')->map(fn ($g, $name) => [
            'name'         => $name ?: '(Unknown)',
            'total'        => $g->count(),
            'approved'     => $g->where('status', 'approved')->count(),
            'disapproved'  => $g->where('status', 'disapproved')->count(),
            'pending'      => $g->where('status', 'requested')->count(),
            'total_volume' => round($g->sum('estimated_volume'), 2),
        ])->sortByDesc('total');
    }

    private function buildCpChecklistStats($concretePourings): array
    {
        $checklistFields = [
            'concrete_vibrator', 'field_density_test', 'protective_covering_materials',
            'beam_cylinder_molds', 'warning_signs_barricades', 'curing_materials',
            'concrete_saw', 'slump_cones', 'concrete_block_spacer', 'plumbness',
            'finishing_tools_equipment', 'quality_of_materials', 'line_grade_alignment',
            'lighting_system', 'required_construction_equipment', 'electrical_layout',
            'rebar_sizes_spacing', 'plumbing_layout', 'rebars_installation',
            'falseworks_formworks',
        ];

        $stats = [];
        $total = $concretePourings->count();

        foreach ($checklistFields as $field) {
            $checked = $concretePourings->where($field, true)->count();
            $stats[$field] = [
                'label'   => ucfirst(str_replace('_', ' ', $field)),
                'checked' => $checked,
                'total'   => $total,
                'rate'    => $total > 0 ? round(($checked / $total) * 100, 1) : 0,
            ];
        }

        return $stats;
    }

    private function buildMemoTypeBreakdown($memos): \Illuminate\Support\Collection
    {
        return $memos->groupBy('type')->map(fn ($g, $type) => [
            'type'  => $type,
            'label' => Memo::types()[$type] ?? ucfirst($type),
            'total' => $g->count(),
            'sent'  => $g->where('status', Memo::STATUS_SENT)->count(),
        ])->sortByDesc('total');
    }

    // =========================================================================
    // PRIVATE — MONTHLY TREND BUILDERS (from DB for efficiency)
    // =========================================================================

    private function buildWrMonthlyTrend(array $range): \Illuminate\Support\Collection
    {
        return WorkRequest::whereBetween('created_at', [$range['from'], $range['to']])
            ->select(DB::raw("DATE_FORMAT(created_at,'%Y-%m') as month"), DB::raw('count(*) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function buildCpMonthlyTrend(array $range): \Illuminate\Support\Collection
    {
        return ConcretePouring::whereBetween('created_at', [$range['from'], $range['to']])
            ->select(
                DB::raw("DATE_FORMAT(created_at,'%Y-%m') as month"),
                DB::raw('count(*) as total'),
                DB::raw('sum(estimated_volume) as volume')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function buildMemoMonthlyTrend(array $range): \Illuminate\Support\Collection
    {
        return Memo::whereBetween('created_at', [$range['from'], $range['to']])
            ->select(DB::raw("DATE_FORMAT(created_at,'%Y-%m') as month"), DB::raw('count(*) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
}