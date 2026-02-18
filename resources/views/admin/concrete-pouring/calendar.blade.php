<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Concrete Pouring Calendar
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.concrete-pouring.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 dark:bg-gray-500 text-white text-xs font-semibold rounded-md hover:bg-gray-700">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Month Navigation --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 flex justify-between items-center">
                    <div class="flex gap-2">
                        <select name="month" id="month" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm" onchange="updateCalendar()">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create(null, $m, 1)->format('F') }}
                                </option>
                            @endfor
                        </select>
                        <select name="year" id="year" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-orange-500 dark:focus:border-orange-600 focus:ring-orange-500 dark:focus:ring-orange-600 shadow-sm" onchange="updateCalendar()">
                            @for($y = now()->year - 2; $y <= now()->year + 2; $y++)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}
                    </h3>
                    <div></div>
                </div>
            </div>

            {{-- Calendar --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    {{-- Weekday Headers --}}
                    <div class="grid grid-cols-7 gap-2 mb-4">
                        @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                            <div class="bg-gray-100 dark:bg-gray-700 p-3 text-center font-semibold text-gray-700 dark:text-gray-300">
                                {{ substr($day, 0, 3) }}
                            </div>
                        @endforeach
                    </div>

                    {{-- Calendar Days --}}
                    <div class="grid grid-cols-7 gap-2">
                        @php
                            $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
                            $endDate = \Carbon\Carbon::create($year, $month, 1)->endOfMonth();
                            $currentDate = $startDate->copy();
                            $firstDayOfWeek = $startDate->dayOfWeek;
                        @endphp

                        {{-- Empty cells for days before the start of the month --}}
                        @for($i = 0; $i < $firstDayOfWeek; $i++)
                            <div class="bg-gray-50 dark:bg-gray-700 p-2 min-h-[120px]"></div>
                        @endfor

                        {{-- Days of the month --}}
                        @while($currentDate->lte($endDate))
                            @php
                                $dateKey = $currentDate->format('Y-m-d');
                                $pourings = $calendarData[$dateKey] ?? collect();
                            @endphp
                            <div class="border border-gray-200 dark:border-gray-700 p-2 min-h-[120px] rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <div class="font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                    {{ $currentDate->day }}
                                </div>
                                <div class="space-y-1">
                                    @forelse($pourings as $pouring)
                                        <a href="{{ route('admin.concrete-pouring.show', $pouring) }}" 
                                           class="block text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 rounded px-2 py-1 hover:bg-blue-200 dark:hover:bg-blue-800 truncate"
                                           title="{{ $pouring->project_name }}">
                                            {{ substr($pouring->project_name, 0, 15) }}...
                                        </a>
                                    @empty
                                        <p class="text-xs text-gray-400 dark:text-gray-500">No pourings</p>
                                    @endforelse
                                </div>
                            </div>
                            @php $currentDate->addDay(); @endphp
                        @endwhile

                        {{-- Empty cells for days after the end of the month --}}
                        @php
                            $remainingDays = 42 - ($firstDayOfWeek + $endDate->day);
                        @endphp
                        @for($i = 0; $i < $remainingDays; $i++)
                            <div class="bg-gray-50 dark:bg-gray-700 p-2 min-h-[120px]"></div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateCalendar() {
            const month = document.getElementById('month').value;
            const year = document.getElementById('year').value;
            window.location.href = `{{ route('admin.concrete-pouring.calendar') }}?month=` + month + `&year=` + year;
        }
    </script>
</x-app-layout>
