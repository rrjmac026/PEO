@php
$roleStatusMap = [
    'site_inspector'    => ['field' => 'inspected_by_site_inspector', 'done' => '✓ Done',  'pending' => 'Pending'],
    'surveyor'          => ['field' => 'surveyor_name',               'done' => '✓ Done',  'pending' => 'Pending'],
    'resident_engineer' => ['field' => 'resident_engineer_name',      'done' => '✓ Done',  'pending' => 'Pending'],
    'engineeriv'        => ['field' => 'checked_by_mtqa',             'done' => '✓ Done',  'pending' => 'Pending'],
    'engineeriii'       => ['field' => 'recommending_approval_by',    'done' => '✓ Done',  'pending' => 'Pending'],
    'provincial_engineer'=> ['field' => 'approved_notes',             'done' => '✓ Noted', 'pending' => 'Not yet'],
];
$roleStatus = $roleStatusMap[$role] ?? null;
@endphp

<tr class="wri-table-row">
    {{-- Project --}}
    <td class="wri-td">
        <div class="wri-project-name">{{ $workRequest->name_of_project }}</div>
        <div class="wri-project-id">#{{ str_pad($workRequest->id, 6, '0', STR_PAD_LEFT) }}</div>
        @if($workRequest->reference_number)
            <div class="wri-project-id">Ref: {{ $workRequest->reference_number }}</div>
        @endif
    </td>

    {{-- Location --}}
    <td class="wri-td">{{ $workRequest->project_location }}</td>

    {{-- Contractor --}}
    <td class="wri-td">{{ $workRequest->contractor_name ?? '—' }}</td>

    {{-- Start Date --}}
    <td class="wri-td">{{ $workRequest->requested_work_start_date?->format('M d, Y') ?? '—' }}</td>

    {{-- Status Badge --}}
    <td class="wri-td">
        <span class="wri-badge {{ $workRequest->status }}">
            {{ ucfirst($workRequest->status) }}
        </span>
    </td>

    {{-- Role-specific completion column --}}
    @if($roleStatus)
        <td class="wri-td">
            @if($workRequest->{$roleStatus['field']})
                <span class="wri-status-done">{{ $roleStatus['done'] }}</span>
            @else
                <span class="wri-status-pending">{{ $roleStatus['pending'] }}</span>
            @endif
        </td>
    @endif

    {{-- Actions --}}
    <td class="wri-td">
        <a href="{{ route('reviewer.work-requests.show', $workRequest) }}" class="wri-link">
            <i class="fas fa-eye"></i> View
        </a>
    </td>
</tr>