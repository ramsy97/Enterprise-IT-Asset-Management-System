<x-app-layout>
    <x-slot name="title">Maintenance Calendar</x-slot>

    <x-page-header
        title="Maintenance Calendar"
        subtitle="Visual overview of scheduled maintenance.">

        <x-slot name="actions">
            <a href="{{ route('maintenance.index') }}" class="btn-secondary">
                <span class="material-symbols-outlined text-[18px]">list</span>
                List View
            </a>
        </x-slot>
    </x-page-header>

    <x-card>
        <div x-data="{
            events: @js($events),
            current: new Date(),
            monthYear() { return this.current.toLocaleString('en-US', { month: 'long', year: 'numeric' }); },
            prev() { this.current = new Date(this.current.getFullYear(), this.current.getMonth() - 1, 1); },
            next() { this.current = new Date(this.current.getFullYear(), this.current.getMonth() + 1, 1); },
            today() { this.current = new Date(); },
            daysInMonth() { return new Date(this.current.getFullYear(), this.current.getMonth() + 1, 0).getDate(); },
            firstDay() { return new Date(this.current.getFullYear(), this.current.getMonth(), 1).getDay(); },
            eventsFor(day) {
                const y = this.current.getFullYear();
                const m = String(this.current.getMonth() + 1).padStart(2, '0');
                const iso = `${y}-${m}-${String(day).padStart(2, '0')}`;
                return this.events.filter((e) => e.start === iso);
            },
        }">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-headline-md text-on-surface" x-text="monthYear()"></h3>
                <div class="flex items-center gap-2">
                    <button type="button" class="btn-secondary !px-3 !py-1.5" @click="prev()">
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </button>
                    <button type="button" class="btn-secondary !px-3 !py-1.5" @click="today()">Today</button>
                    <button type="button" class="btn-secondary !px-3 !py-1.5" @click="next()">
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-7 gap-px overflow-hidden rounded-xl border border-outline-variant bg-outline-variant">
                @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                    <div class="bg-surface-container-low px-3 py-2 text-center text-label-md uppercase tracking-wider text-on-surface-variant">{{ $day }}</div>
                @endforeach

                <template x-for="blank in firstDay()" :key="'b'+blank">
                    <div class="bg-surface-container-lowest p-2"></div>
                </template>

                <template x-for="day in daysInMonth()" :key="day">
                    <div class="min-h-[96px] bg-surface-container-lowest p-2">
                        <p class="mb-1 text-label-md font-semibold text-on-surface-variant" x-text="day"></p>
                        <template x-for="event in eventsFor(day)" :key="event.id">
                            <a :href="event.url"
                               class="mb-1 block truncate rounded px-1.5 py-0.5 text-[11px] font-medium"
                               :class="{
                                   'bg-amber-500/10 text-amber-700': event.status === 'scheduled' || event.status === 'in_progress',
                                   'bg-[#10b981]/10 text-[#067a4f]': event.status === 'completed',
                                   'bg-error/10 text-error': event.status === 'cancelled',
                               }"
                               x-text="event.title"></a>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </x-card>
</x-app-layout>
