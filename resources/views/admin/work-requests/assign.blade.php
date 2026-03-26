{{-- resources/views/admin/work-requests/assign.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">
                Assign Reviewers — {{ $workRequest->name_of_project }}
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
            <strong>How this works:</strong> Reviewers will be notified to review this request
            <em>in order</em>. Only assign roles that are required — leave a slot blank to skip it.
            The sequence is fixed:<br>
            <span class="mt-2 block font-medium">
                Site Inspector → Surveyor → Resident Engineer → MTQA →
                Engineer IV → Engineer III → <strong>Provincial Engineer (Final Decision)</strong>
            </span>
            <p class="mt-2 text-blue-600">
                📌 The <strong>Provincial Engineer</strong> makes the final approve/reject decision.
                Once approved, the <strong>MTQA</strong> is notified and can print the document.
            </p>
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
                    ['label' => 'Site Inspector',      'name' => 'assigned_site_inspector_id',     'users' => $siteInspectors,      'step' => 1, 'color' => 'indigo'],
                    ['label' => 'Surveyor',             'name' => 'assigned_surveyor_id',            'users' => $surveyors,           'step' => 2, 'color' => 'indigo'],
                    ['label' => 'Resident Engineer',    'name' => 'assigned_resident_engineer_id',  'users' => $residentEngineers,   'step' => 3, 'color' => 'indigo'],
                    ['label' => 'MTQA',                 'name' => 'assigned_mtqa_id',               'users' => $mtqas,               'step' => 4, 'color' => 'indigo'],
                    ['label' => 'Engineer IV',          'name' => 'assigned_engineer_iv_id',        'users' => $engineersIv,         'step' => 5, 'color' => 'indigo'],
                    ['label' => 'Engineer III',         'name' => 'assigned_engineer_iii_id',       'users' => $engineersIii,        'step' => 6, 'color' => 'indigo'],
                    ['label' => 'Provincial Engineer',  'name' => 'assigned_provincial_engineer_id','users' => $provincialEngineers, 'step' => 7, 'color' => 'green'],
                ];
            @endphp

            @foreach ($slots as $slot)
                <div class="flex items-center gap-4 px-6 py-4">
                    {{-- Step badge --}}
                    <div class="flex-shrink-0 w-8 h-8 rounded-full
                                {{ $slot['color'] === 'green' ? 'bg-green-100 text-green-700' : 'bg-indigo-100 text-indigo-700' }}
                                text-xs font-bold flex items-center justify-center">
                        {{ $slot['step'] }}
                    </div>

                    {{-- Label --}}
                    <div class="w-52 flex-shrink-0">
                        <label for="{{ $slot['name'] }}"
                               class="block text-sm font-medium {{ $slot['color'] === 'green' ? 'text-green-700' : 'text-gray-700' }}">
                            {{ $slot['label'] }}
                            @if($slot['step'] === 7)
                                <span class="ml-1 text-xs font-bold text-green-600 bg-green-100 px-2 py-0.5 rounded-full">FINAL DECISION</span>
                            @endif
                        </label>
                        @if($slot['step'] === 7)
                            <span class="text-xs text-green-600 font-medium">Approves or rejects</span>
                        @else
                            <span class="text-xs text-gray-400">Leave blank to skip</span>
                        @endif
                    </div>

                    {{-- Select --}}
                    <div class="flex-1">
                        <select name="{{ $slot['name'] }}"
                                id="{{ $slot['name'] }}"
                                class="w-full border-gray-300 rounded-md shadow-sm text-sm
                                       {{ $slot['color'] === 'green' ? 'focus:ring-green-500 focus:border-green-500' : 'focus:ring-indigo-500 focus:border-indigo-500' }}">
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

            {{-- Note: Admin does NOT have a final decision step anymore --}}
            <div class="flex items-center gap-4 px-6 py-4 bg-gray-50 rounded-b-lg">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-100 text-gray-500
                            text-xs font-bold flex items-center justify-center">
                    <i class="fas fa-info text-xs"></i>
                </div>
                <div class="flex-1 text-sm text-gray-500">
                    <strong>Note:</strong> Admin assigns reviewers only. The
                    <strong>Provincial Engineer</strong> makes the final approve/reject decision.
                    After approval, <strong>MTQA</strong> is notified to print the document.
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