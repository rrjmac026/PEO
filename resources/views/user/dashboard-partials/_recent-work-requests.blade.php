{{-- ============================================================
     Recent Work Requests Table
     ============================================================ --}}
<div>
    <h2 class="db-section-heading">
        <i class="fas fa-history text-orange-500 dark:text-orange-400"></i>
        Recent Work Requests
    </h2>
    <div class="db-table-container">
        <div class="db-table-header">
            <div class="db-table-title">Latest Submissions</div>
            <a href="{{ route('user.work-requests.index') }}" class="db-table-link">
                View All <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>

        <table class="db-table">
            <thead>
                <tr>
                    <th>Project</th>
                    <th>Location</th>
                    <th>Start Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentWorkRequests as $workRequest)
                    <tr>
                        <td>
                            <p class="font-medium">{{ $workRequest->name_of_project }}</p>
                            <p class="db-table-secondary">#{{ str_pad($workRequest->id, 6, '0', STR_PAD_LEFT) }}</p>
                        </td>
                        <td>{{ $workRequest->project_location }}</td>
                        <td>{{ $workRequest->requested_work_start_date?->format('M d, Y') ?? '—' }}</td>
                        <td>
                            <span class="db-badge {{ strtolower($workRequest->status) }}">
                                {{ ucfirst($workRequest->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('user.work-requests.show', $workRequest) }}" class="db-table-link">
                                View <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="db-empty-state">
                                <p class="db-empty-message">No work requests yet.</p>
                                <a href="{{ route('user.work-requests.create') }}" class="db-empty-action">
                                    Create your first one →
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>