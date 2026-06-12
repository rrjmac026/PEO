{{-- resources/views/reviewer/dashboard-partials/recent-requests.blade.php --}}
<div>
    <h2 class="db-section-heading">
        <i class="fas fa-history text-blue-500 dark:text-blue-400"></i>
        Requests Needing Your Action
    </h2>

    <div class="db-panel">
        @forelse($stats['recent'] as $workRequest)
            <div class="db-activity-item">
                <div class="flex items-start gap-4">
                    <div class="db-act-icon blue"><i class="fas fa-file-circle-check"></i></div>
                    <div class="flex-1 min-w-0">
                        <p class="db-act-title">{{ $workRequest->name_of_project }}</p>
                        <p class="db-act-desc">{{ $workRequest->project_location }}</p>
                        <p class="db-act-time">Status: <span class="font-semibold">{{ ucfirst($workRequest->status) }}</span></p>
                    </div>
                    <div>
                        <a href="{{ route('reviewer.work-requests.show', $workRequest) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition text-sm font-semibold">
                            View <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="db-activity-item">
                <p class="text-center text-sm" style="color: var(--db-muted);">No pending requests at this time.</p>
            </div>
        @endforelse

        <div class="db-panel-foot">
            <button onclick="window.location = '{{ route('reviewer.work-requests.index') }}'">
                View All Requests <i class="fas fa-arrow-right text-xs"></i>
            </button>
        </div>
    </div>
</div>