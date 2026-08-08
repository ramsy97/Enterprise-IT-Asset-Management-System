<x-app-layout>
    <x-slot name="title">Schedule Maintenance</x-slot>

    <x-page-header
        title="Schedule Maintenance"
        subtitle="Create a maintenance record for an asset." />

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-error/30 bg-error/10 p-4">
            <ul class="list-inside list-disc space-y-1 text-body-sm text-on-error-container">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('maintenance.store') }}" class="max-w-3xl">
        @csrf

        <x-card title="Maintenance Details">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="asset_id" class="label-field">Asset <span class="text-error">*</span></label>
                    <select id="asset_id" name="asset_id" required class="input-field">
                        <option value="">Select asset</option>
                        @foreach ($assets as $asset)
                            <option value="{{ $asset->id }}" @selected(old('asset_id') == $asset->id)>{{ $asset->asset_code }} — {{ $asset->asset_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="type" class="label-field">Type <span class="text-error">*</span></label>
                    <select id="type" name="type" required class="input-field">
                        @foreach (\App\Enums\MaintenanceType::cases() as $type)
                            <option value="{{ $type->value }}" @selected(old('type') === $type->value)>{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="scheduled_date" class="label-field">Scheduled Date <span class="text-error">*</span></label>
                    <input type="date" id="scheduled_date" name="scheduled_date" value="{{ old('scheduled_date', now()->toDateString()) }}" required class="input-field">
                </div>

                <div>
                    <label for="technician_id" class="label-field">Technician</label>
                    <select id="technician_id" name="technician_id" class="input-field">
                        <option value="">Unassigned</option>
                        @foreach ($technicians as $technician)
                            <option value="{{ $technician->id }}" @selected(old('technician_id') == $technician->id)>{{ $technician->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="label-field">Status <span class="text-error">*</span></label>
                    <select id="status" name="status" required class="input-field">
                        @foreach (\App\Enums\MaintenanceStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(old('status', 'scheduled') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="cost" class="label-field">Estimated Cost</label>
                    <input type="number" id="cost" name="cost" value="{{ old('cost') }}" min="0" step="0.01" class="input-field" placeholder="0.00">
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="label-field">Description</label>
                    <textarea id="description" name="description" rows="3" class="input-field" placeholder="What maintenance is needed?">{{ old('description') }}</textarea>
                </div>
            </div>
        </x-card>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="btn-primary">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Save Maintenance
            </button>
            <a href="{{ route('maintenance.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</x-app-layout>
