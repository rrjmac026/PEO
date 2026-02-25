<div class="wri-filter-card">
    <form method="GET" action="{{ route('reviewer.work-requests.index') }}"
          class="flex flex-wrap gap-4 items-end">

        <div class="flex-1 min-w-[200px]">
            <label class="wri-label block mb-1">Search</label>
            <input type="text" name="search"
                   value="{{ request('search') }}"
                   placeholder="Project name, location, contractor..."
                   class="wri-input w-full" />
        </div>

        <div class="min-w-[150px]">
            <label class="wri-label block mb-1">Status</label>
            <select name="status" class="wri-select w-full">
                <option value="">All Status</option>
                @foreach(['draft','submitted','inspected','reviewed','approved','rejected','accepted'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                        {{ ucfirst($s) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="min-w-[150px]">
            <label class="wri-label block mb-1">Date From</label>
            <input type="date" name="date_from"
                   value="{{ request('date_from') }}"
                   class="wri-input w-full" />
        </div>

        <div class="min-w-[150px]">
            <label class="wri-label block mb-1">Date To</label>
            <input type="date" name="date_to"
                   value="{{ request('date_to') }}"
                   class="wri-input w-full" />
        </div>

        <div class="flex gap-2">
            <button type="submit" class="wri-btn wri-btn-primary">Filter</button>
            <a href="{{ route('reviewer.work-requests.index') }}" class="wri-btn wri-btn-secondary">Reset</a>
        </div>
    </form>
</div>