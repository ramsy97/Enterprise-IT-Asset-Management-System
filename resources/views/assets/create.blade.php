<x-app-layout>
    <x-slot name="title">Register Asset</x-slot>

    <x-page-header
        title="Register Asset"
        subtitle="Add a new asset to the inventory. A unique asset ID and QR code are generated automatically." />

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-error/30 bg-error/10 p-4">
            <ul class="list-inside list-disc space-y-1 text-body-sm text-on-error-container">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('assets.store') }}" class="space-y-6">
        @csrf

        <x-card title="Asset Information">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="asset_name" class="label-field">Asset Name <span class="text-error">*</span></label>
                    <input type="text" id="asset_name" name="asset_name" value="{{ old('asset_name') }}" required class="input-field" placeholder="e.g. Dell Latitude 5420">
                </div>

                <div>
                    <label for="asset_category_id" class="label-field">Category <span class="text-error">*</span></label>
                    <select id="asset_category_id" name="asset_category_id" required class="input-field">
                        <option value="">Select category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('asset_category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="asset_location_id" class="label-field">Location <span class="text-error">*</span></label>
                    <select id="asset_location_id" name="asset_location_id" required class="input-field">
                        <option value="">Select location</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}" @selected(old('asset_location_id') == $location->id)>{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="brand" class="label-field">Brand</label>
                    <input type="text" id="brand" name="brand" value="{{ old('brand') }}" class="input-field" placeholder="e.g. Dell">
                </div>

                <div>
                    <label for="model" class="label-field">Model</label>
                    <input type="text" id="model" name="model" value="{{ old('model') }}" class="input-field" placeholder="e.g. Latitude 5420">
                </div>

                <div>
                    <label for="serial_number" class="label-field">Serial Number</label>
                    <input type="text" id="serial_number" name="serial_number" value="{{ old('serial_number') }}" class="input-field font-mono" placeholder="e.g. DL-992-X1">
                </div>

                <div>
                    <label for="purchase_date" class="label-field">Purchase Date</label>
                    <input type="date" id="purchase_date" name="purchase_date" value="{{ old('purchase_date') }}" class="input-field">
                </div>

                <div>
                    <label for="purchase_price" class="label-field">Purchase Price</label>
                    <input type="number" id="purchase_price" name="purchase_price" value="{{ old('purchase_price') }}" min="0" step="0.01" class="input-field" placeholder="0.00">
                </div>

                <div>
                    <label for="warranty_expires_at" class="label-field">Warranty Expiry</label>
                    <input type="date" id="warranty_expires_at" name="warranty_expires_at" value="{{ old('warranty_expires_at') }}" class="input-field">
                </div>

                <div>
                    <label for="status" class="label-field">Status <span class="text-error">*</span></label>
                    <select id="status" name="status" required class="input-field">
                        @foreach (\App\Enums\AssetStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(old('status', 'available') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="current_holder_id" class="label-field">Assign To</label>
                    <select id="current_holder_id" name="current_holder_id" class="input-field">
                        <option value="">Unassigned</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(old('current_holder_id') == $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </x-card>

        <x-card title="Additional Notes">
            <label for="notes" class="label-field">Notes</label>
            <textarea id="notes" name="notes" rows="3" class="input-field" placeholder="Any additional details...">{{ old('notes') }}</textarea>
        </x-card>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Register Asset
            </button>
            <a href="{{ route('assets.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</x-app-layout>
