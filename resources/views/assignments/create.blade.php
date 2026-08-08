<x-app-layout>
    <x-slot name="title">New Assignment</x-slot>

    <x-page-header
        title="New Assignment"
        subtitle="Request an asset for an employee. The request will be routed for approval." />

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-error/30 bg-error/10 p-4">
            <ul class="list-inside list-disc space-y-1 text-body-sm text-on-error-container">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('assignments.store') }}" class="max-w-3xl">
        @csrf

        <x-card title="Assignment Details">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="asset_id" class="label-field">Asset <span class="text-error">*</span></label>
                    <select id="asset_id" name="asset_id" required class="input-field">
                        <option value="">Select an available asset</option>
                        @foreach ($assets as $asset)
                            <option value="{{ $asset->id }}" @selected(old('asset_id') == $asset->id)>{{ $asset->asset_code }} — {{ $asset->asset_name }} ({{ $asset->location?->name ?? 'No location' }})</option>
                        @endforeach
                    </select>
                    @error('asset_id')
                        <p class="mt-1 text-body-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="employee_id" class="label-field">Employee <span class="text-error">*</span></label>
                    <select id="employee_id" name="employee_id" required class="input-field">
                        <option value="">Select employee</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" @selected(old('employee_id') == $employee->id)>{{ $employee->name }}</option>
                        @endforeach
                    </select>
                    @error('employee_id')
                        <p class="mt-1 text-body-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="request_date" class="label-field">Request Date</label>
                    <input type="date" id="request_date" name="request_date" value="{{ old('request_date', now()->toDateString()) }}" class="input-field">
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="label-field">Notes</label>
                    <textarea id="notes" name="notes" rows="3" class="input-field" placeholder="Reason for assignment, expected duration, etc.">{{ old('notes') }}</textarea>
                </div>
            </div>
        </x-card>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="btn-primary">
                <span class="material-symbols-outlined text-[18px]">send</span>
                Submit Request
            </button>
            <a href="{{ route('assignments.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</x-app-layout>
