@php
    $stepRecommendations = $workRequest->recommendationsForStep($step);
@endphp

@if($stepRecommendations->isNotEmpty())
    <div class="recommendation-history mt-3" style="padding-top: 8px; border-top: 1px dashed rgba(0,0,0,0.08);">
        <h6 class="text-sm font-semibold text-gray-700 mb-2">
            Recommendation History — {{ $stepRecommendations->first()->step_label }}
        </h6>
        <ol class="space-y-2">
            @foreach($stepRecommendations as $index => $rec)
                <li class="flex items-start gap-3 p-2 rounded-lg border
                    {{ $rec->is_signed ? 'border-green-200 bg-green-50' : 'border-amber-200 bg-amber-50' }}">
                    <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full text-xs font-bold
                        {{ $rec->is_signed ? 'bg-green-600 text-white' : 'bg-amber-500 text-white' }}">
                        {{ $index + 1 }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-800">{{ $rec->recommendation_text }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs text-gray-500">
                                {{ $rec->user->name ?? 'Unknown' }} —
                                {{ $rec->created_at->format('M d, Y g:i A') }}
                            </span>
                            @if($rec->is_signed)
                                <span class="inline-flex items-center text-xs font-medium text-green-700">
                                    ✓ Signed &amp; Final
                                </span>
                            @else
                                <span class="inline-flex items-center text-xs font-medium text-amber-700">
                                    ⚠ Sent back for revision
                                </span>
                            @endif
                        </div>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
@endif