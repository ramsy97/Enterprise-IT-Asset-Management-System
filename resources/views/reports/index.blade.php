<x-app-layout>
    <x-slot name="title">Report Center</x-slot>

    <x-page-header
        title="Report Center"
        subtitle="Generate and export reports across the asset lifecycle." />

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <x-card title="Asset Reports">
            <p class="text-body-md text-on-surface-variant">Full inventory with category, location, holder, status, and warranty details.</p>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="{{ route('reports.assets') }}" class="btn-primary">
                    <span class="material-symbols-outlined text-[18px]">table_view</span>
                    View Report
                </a>
                <a href="{{ route('reports.assets.excel') }}" class="btn-secondary">
                    <span class="material-symbols-outlined text-[18px]">table_view</span>
                    Excel
                </a>
                <a href="{{ route('reports.assets.pdf') }}" class="btn-secondary">
                    <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
                    PDF
                </a>
            </div>
        </x-card>

        <x-card title="Maintenance Reports">
            <p class="text-body-md text-on-surface-variant">Maintenance activity by type, status, technician, and cost.</p>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="{{ route('reports.maintenance') }}" class="btn-primary">
                    <span class="material-symbols-outlined text-[18px]">table_view</span>
                    View Report
                </a>
                <a href="{{ route('reports.maintenance.excel') }}" class="btn-secondary">
                    <span class="material-symbols-outlined text-[18px]">table_view</span>
                    Excel
                </a>
                <a href="{{ route('reports.maintenance.pdf') }}" class="btn-secondary">
                    <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
                    PDF
                </a>
            </div>
        </x-card>

        <x-card title="Audit Reports">
            <p class="text-body-md text-on-surface-variant">Audit outcomes, condition, and location match results.</p>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="{{ route('reports.audits') }}" class="btn-primary">
                    <span class="material-symbols-outlined text-[18px]">table_view</span>
                    View Report
                </a>
                <a href="{{ route('reports.audits.excel') }}" class="btn-secondary">
                    <span class="material-symbols-outlined text-[18px]">table_view</span>
                    Excel
                </a>
                <a href="{{ route('reports.audits.pdf') }}" class="btn-secondary">
                    <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
                    PDF
                </a>
            </div>
        </x-card>

        <x-card title="License Reports">
            <p class="text-body-md text-on-surface-variant">License inventory, seat usage, and expiration tracking.</p>
            <div class="mt-5 flex flex-wrap gap-3">
                <a href="{{ route('reports.licenses') }}" class="btn-primary">
                    <span class="material-symbols-outlined text-[18px]">table_view</span>
                    View Report
                </a>
                <a href="{{ route('reports.licenses.excel') }}" class="btn-secondary">
                    <span class="material-symbols-outlined text-[18px]">table_view</span>
                    Excel
                </a>
                <a href="{{ route('reports.licenses.pdf') }}" class="btn-secondary">
                    <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
                    PDF
                </a>
            </div>
        </x-card>
    </div>
</x-app-layout>
