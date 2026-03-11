{{-- resources/views/admin/work-requests/assign.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">
                Assign Engineers — {{ $workRequest->name_of_project }}
            </h2>
            <a href="{{ route('admin.work-requests.show', $workRequest) }}"
               class="text-sm text-gray-500 hover:text-gray-700">
                ← Back to Request
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Info card --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-sm text-blue-800">
            <strong>How this works:</strong> Engineers will be notified to review this request
            <em>in order</em>. Only assign roles that are required — leave a slot blank to skip it.
            The sequence is fixed:
            Site Inspector → Surveyor → Resident Engineer → MTQA →
            Engineer IV → Engineer III → Provincial Engineer → <strong>Admin final decision</strong>.
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg p-4 mb-6 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.work-requests.assign', $workRequest) }}" method="POST"
              class="bg-white shadow rounded-lg divide-y divide-gray-100">
            @csrf

            @php
                $slots = [
                    ['label' => 'Site Inspector',       'name' => 'assigned_site_inspector_id',      'users' => $siteInspectors,      'step' => 1],
                    ['label' => 'Surveyor',              'name' => 'assigned_surveyor_id',             'users' => $surveyors,           'step' => 2],
                    ['label' => 'Resident Engineer',     'name' => 'assigned_resident_engineer_id',   'users' => $residentEngineers,   'step' => 3],
                    ['label' => 'MTQA',                  'name' => 'assigned_mtqa_id',                'users' => $mtqas,               'step' => 4],
                    ['label' => 'Engineer IV',           'name' => 'assigned_engineer_iv_id',         'users' => $engineersIv,         'step' => 5],
                    ['label' => 'Engineer III',          'name' => 'assigned_engineer_iii_id',        'users' => $engineersIii,        'step' => 6],
                    ['label' => 'Provincial Engineer',   'name' => 'assigned_provincial_engineer_id', 'users' => $provincialEngineers, 'step' => 7],
                ];
            @endphp

            @foreach ($slots as $slot)
                <div class="flex items-center gap-4 px-6 py-4">
                    {{-- Step badge --}}
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-100 text-indigo-700
                                text-xs font-bold flex items-center justify-center">
                        {{ $slot['step'] }}
                    </div>

                    {{-- Label --}}
                    <div class="w-48 flex-shrink-0">
                        <label for="{{ $slot['name'] }}"
                               class="block text-sm font-medium text-gray-700">
                            {{ $slot['label'] }}
                        </label>
                        <span class="text-xs text-gray-400">Leave blank to skip</span>
                    </div>

                    {{-- Select --}}
                    <div class="flex-1">
                        <select name="{{ $slot['name'] }}"
                                id="{{ $slot['name'] }}"
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm
                                       focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">— Skip this step —</option>
                            @foreach ($slot['users'] as $u)
                                <option value="{{ $u->id }}"
                                    {{ old($slot['name'], $workRequest->{$slot['name']}) == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                        @error($slot['name'])
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endforeach

            {{-- Step 8: Admin final (automatic, no selection needed) --}}
            <div class="flex items-center gap-4 px-6 py-4 bg-gray-50 rounded-b-lg">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 text-green-700
                            text-xs font-bold flex items-center justify-center">
                    8
                </div>
                <div class="w-48 flex-shrink-0">
                    <p class="text-sm font-medium text-gray-700">Admin Final Decision</p>
                    <span class="text-xs text-gray-400">Always last</span>
                </div>
                <div class="flex-1 text-sm text-gray-500 italic">
                    Automatically assigned to admin after all reviewers complete
                </div>
            </div>

            <div class="px-6 py-4 flex justify-end gap-3">
                <a href="{{ route('admin.work-requests.show', $workRequest) }}"
                   class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-md
                          hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit"
                        class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md
                               hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Save Assignments & Start Review
                </button>
            </div>
        </form>
    </div>
</x-app-layout>