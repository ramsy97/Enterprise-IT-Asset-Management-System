<x-app-layout>
    <x-slot name="title">Edit Audit</x-slot>

    <x-page-header
        title="Edit Audit"
        subtitle="Update the audit record." />

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-error/30 bg-error/10 p-4">
            <ul class="list-inside list-disc space-y-1 text-body-sm text-on-error-container">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('audits.update', $audit) }}" enctype="multipart/form-data" class="max-w-3xl">
        @csrf
        @method('PUT')

        <x-card title="Audit Details">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="asset_id" class="label-field">Asset <span class="text-error">*</span></label>
                    <select id="asset_id" name="asset_id" required class="input-field">
                        @foreach ($assets as $asset)
                            <option value="{{ $asset->id }}" @selected(old('asset_id', $audit->asset_id) == $asset->id)>
                                {{ $asset->asset_code }} — {{ $asset->asset_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="audit_date" class="label-field">Audit Date <span class="text-error">*</span></label>
                    <input type="date" id="audit_date" name="audit_date" value="{{ old('audit_date', $audit->audit_date?->format('Y-m-d')) }}" required class="input-field">
                </div>

                <div>
                    <label for="status" class="label-field">Status <span class="text-error">*</span></label>
                    <select id="status" name="status" required class="input-field">
                        @foreach (\App\Enums\AuditStatus::cases() as $status)
                            <option value="{{ $status->value }}" @selected(old('status', $audit->status?->value) === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="condition" class="label-field">Condition</label>
                    <input type="text" id="condition" name="condition" value="{{ old('condition', $audit->condition) }}" class="input-field">
                </div>

                <div>
                    <label for="location_match" class="label-field">Location Match</label>
                    <select id="location_match" name="location_match" class="input-field">
                        <option value="1" @selected(old('location_match', $audit->location_match ? '1' : '0') === '1')>Yes</option>
                        <option value="0" @selected(old('location_match', $audit->location_match ? '1' : '0') === '0')>No</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="findings" class="label-field">Findings</label>
                    <textarea id="findings" name="findings" rows="3" class="input-field">{{ old('findings', $audit->findings) }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="evidence" class="label-field">Evidence Photo</label>
                    <input type="file" id="evidence" name="evidence" accept="image/jpeg,image/png,image/gif,image/webp" class="input-field">
                    <p class="mt-1 text-body-sm text-on-surface-variant">Leave empty to keep the current evidence.</p>
                </div>
            </div>
        </x-card>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="btn-primary">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Save Changes
            </button>
            <a href="{{ route('audits.show', $audit) }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</x-app-layout>
