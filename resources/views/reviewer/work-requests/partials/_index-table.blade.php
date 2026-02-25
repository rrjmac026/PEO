@php
$roleColumnHeader = [
    'site_inspector'    => 'Inspected',
    'surveyor'          => 'Surveyed',
    'resident_engineer' => 'Reviewed',
    'engineeriv'        => 'Checked',
    'engineeriii'       => 'Recommended',
    'provincial_engineer'=> 'Approved',
];
@endphp

<div class="wri-table-wrapper">
    <table class="wri-table">
        <thead class="wri-table-head">
            <tr>
                <th class="wri-th">Project</th>
                <th class="wri-th">Location</th>
                <th class="wri-th">Contractor</th>
                <th class="wri-th">Start Date</th>
                <th class="wri-th">Status</th>
                @if(isset($roleColumnHeader[$role]))
                    <th class="wri-th">{{ $roleColumnHeader[$role] }}</th>
                @endif
                <th class="wri-th">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($workRequests as $workRequest)
                @include('reviewer.work-requests.partials._index-table-row', [
                    'workRequest' => $workRequest,
                    'role'        => $role,
                ])
            @empty
                <tr class="wri-table-row">
                    <td colspan="8" class="wri-td text-center py-12">
                        <p style="color:var(--wri-muted);">No work requests found.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>