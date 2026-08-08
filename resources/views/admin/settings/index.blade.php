<x-app-layout>
    <x-slot name="title">Settings</x-slot>

    <x-page-header
        title="System Settings"
        subtitle="Company profile used in reports, PDFs, and notifications." />

    @if (session('success'))
        <div class="mb-6 rounded-xl border border-success/30 bg-success/10 p-4 text-body-sm text-on-success-container">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-error/30 bg-error/10 p-4">
            <ul class="list-inside list-disc space-y-1 text-body-sm text-on-error-container">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-3xl">
        @csrf

        <x-card title="Company Profile">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="company_name" class="label-field">Company Name <span class="text-error">*</span></label>
                    <input type="text" id="company_name" name="company_name" value="{{ old('company_name', $settings['company_name'] ?? '') }}" required class="input-field">
                </div>

                <div class="md:col-span-2">
                    <label for="company_address" class="label-field">Company Address</label>
                    <input type="text" id="company_address" name="company_address" value="{{ old('company_address', $settings['company_address'] ?? '') }}" class="input-field">
                </div>

                <div>
                    <label for="company_phone" class="label-field">Phone</label>
                    <input type="text" id="company_phone" name="company_phone" value="{{ old('company_phone', $settings['company_phone'] ?? '') }}" class="input-field">
                </div>

                <div>
                    <label for="currency" class="label-field">Currency Code <span class="text-error">*</span></label>
                    <input type="text" id="currency" name="currency" value="{{ old('currency', $settings['currency'] ?? 'IDR') }}" required maxlength="10" class="input-field">
                    <p class="mt-1 text-body-sm text-on-surface-variant">Currency code shown in cost columns (e.g. IDR, USD, EUR).</p>
                </div>
            </div>
        </x-card>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="btn-primary">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Save Settings
            </button>
        </div>
    </form>
</x-app-layout>
