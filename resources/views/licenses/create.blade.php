<x-app-layout>
    <x-slot name="title">Add License</x-slot>

    <x-page-header
        title="Add Software License"
        subtitle="Register a new software license. License keys are stored encrypted." />

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-error/30 bg-error/10 p-4">
            <ul class="list-inside list-disc space-y-1 text-body-sm text-on-error-container">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('licenses.store') }}" class="max-w-3xl">
        @csrf

        <x-card title="License Details">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="software_name" class="label-field">Software Name <span class="text-error">*</span></label>
                    <input type="text" id="software_name" name="software_name" value="{{ old('software_name') }}" required class="input-field" placeholder="e.g. Microsoft 365 Business">
                </div>

                <div>
                    <label for="vendor" class="label-field">Vendor</label>
                    <input type="text" id="vendor" name="vendor" value="{{ old('vendor') }}" class="input-field" placeholder="e.g. Microsoft">
                </div>

                <div>
                    <label for="license_key" class="label-field">License Key</label>
                    <input type="text" id="license_key" name="license_key" value="{{ old('license_key') }}" class="input-field font-mono" placeholder="Stored encrypted">
                </div>

                <div>
                    <label for="total_licenses" class="label-field">Total Seats <span class="text-error">*</span></label>
                    <input type="number" id="total_licenses" name="total_licenses" value="{{ old('total_licenses', 1) }}" min="1" required class="input-field">
                </div>

                <div>
                    <label for="used_licenses" class="label-field">Used Seats</label>
                    <input type="number" id="used_licenses" name="used_licenses" value="{{ old('used_licenses', 0) }}" min="0" class="input-field">
                </div>

                <div>
                    <label for="purchase_date" class="label-field">Purchase Date</label>
                    <input type="date" id="purchase_date" name="purchase_date" value="{{ old('purchase_date') }}" class="input-field">
                </div>

                <div>
                    <label for="purchase_cost" class="label-field">Purchase Cost</label>
                    <input type="number" id="purchase_cost" name="purchase_cost" value="{{ old('purchase_cost') }}" min="0" step="0.01" class="input-field">
                </div>

                <div>
                    <label for="expires_at" class="label-field">Expires At</label>
                    <input type="date" id="expires_at" name="expires_at" value="{{ old('expires_at') }}" class="input-field">
                </div>
            </div>
        </x-card>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="btn-primary">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Save License
            </button>
            <a href="{{ route('licenses.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</x-app-layout>
