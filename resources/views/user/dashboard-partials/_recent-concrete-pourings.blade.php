{{-- ============================================================
     Recent Concrete Pouring Requests Table (Employee only)
     ============================================================ --}}
@if(Auth::user()->employee)
    <div>
        <h2 class="db-section-heading">
            <i class="fas fa-cement text-orange-500 dark:text-orange-400"></i>
            Recent Pouring Requests
        </h2>

        <div class="db-table-container">
            <div class="db-table-header">
                <div class="db-table-title">Latest Pouring Submissions</div>
                <a href="{{ route('user.concrete-pouring.index') }}" class="db-table-link">
                    View All <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            <table class="db-table">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Contractor</th>
                        <th>Pouring Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentConcretePourings as $pouring)
                        <tr>
                            <td>
                                <p class="font-medium">{{ $pouring->project_name }}</p>
                                <p class="db-table-secondary">#{{ str_pad($pouring->id, 6, '0', STR_PAD_LEFT) }}</p>
                            </td>
                            <td>{{ $pouring->contractor }}</td>
                            <td>{{ $pouring->pouring_datetime?->format('M d, Y') ?? '—' }}</td>
                            <td>
                                <span class="db-badge {{ strtolower($pouring->status) }}">
                                    {{ ucfirst($pouring->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('user.concrete-pouring.show', $pouring) }}" class="db-table-link">
                                    View <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="db-empty-state">
                                    <p class="db-empty-message">No concrete pouring requests yet.</p>
                                    <a href="{{ route('user.concrete-pouring.create') }}" class="db-empty-action">
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
@endif