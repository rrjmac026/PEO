{{-- resources/views/admin/work-requests/decision.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">
                Final Decision — {{ $workRequest->name_of_project }}
            </h2>
            <a href="{{ route('admin.work-requests.show', $workRequest) }}"
               class="text-sm text-gray-500 hover:text-gray-700">
                ← Back to Request
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Summary card --}}
        <div class="bg-white shadow rounded-lg p-5 mb-6 text-sm space-y-2">
            <div class="flex justify-between">
                <span class="text-gray-500">Project</span>
                <span class="font-medium">{{ $workRequest->name_of_project }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Contractor</span>
                <span>{{ $workRequest->contractor_name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Location</span>
                <span>{{ $workRequest->project_location }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">All reviewers completed</span>
                <span class="text-green-600 font-medium">✓ Yes</span>
            </div>
        </div>

        <form action="{{ route('admin.work-requests.store-decision', $workRequest) }}" method="POST"
              class="bg-white shadow rounded-lg p-6 space-y-6">
            @csrf

            {{-- Decision radio --}}
            <div>
                <p class="text-sm font-medium text-gray-700 mb-3">Decision <span class="text-red-500">*</span></p>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="decision" value="approved"
                               {{ old('decision') === 'approved' ? 'checked' : '' }}
                               class="text-green-600 focus:ring-green-500">
                        <span class="text-sm font-medium text-green-700">✓ Approve</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="decision" value="rejected"
                               {{ old('decision') === 'rejected' ? 'checked' : '' }}
                               class="text-red-600 focus:ring-red-500">
                        <span class="text-sm font-medium text-red-700">✗ Reject</span>
                    </label>
                </div>
                @error('decision')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remarks --}}
            <div>
                <label for="decision_remarks"
                       class="block text-sm font-medium text-gray-700 mb-1">
                    Remarks
                    <span class="text-gray-400 font-normal">(required if rejecting)</span>
                </label>
                <textarea id="decision_remarks" name="decision_remarks" rows="4"
                          class="w-full border-gray-300 rounded-md shadow-sm text-sm
                                 focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="Add any notes or reasons for your decision...">{{ old('decision_remarks') }}</textarea>
                @error('decision_remarks')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t">
                <a href="{{ route('admin.work-requests.show', $workRequest) }}"
                   class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-md
                               hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Submit Decision
                </button>
            </div>
        </form>
    </div>
</x-app-layout>