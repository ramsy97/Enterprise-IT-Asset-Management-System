<x-app-layout>
    <x-slot name="title">Asset Locations</x-slot>

    <x-page-header
        title="Asset Locations"
        subtitle="Manage the physical locations where assets are stored or deployed." />

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-error/30 bg-error/10 p-4">
            <ul class="list-inside list-disc space-y-1 text-body-sm text-on-error-container">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-card title="Add Location">
            <form method="POST" action="{{ route('locations.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="label-field">Name <span class="text-error">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required class="input-field" placeholder="e.g. HQ - Desk 42">
                </div>
                <div>
                    <label for="building" class="label-field">Building</label>
                    <input type="text" id="building" name="building" value="{{ old('building') }}" class="input-field" placeholder="Optional">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="floor" class="label-field">Floor</label>
                        <input type="text" id="floor" name="floor" value="{{ old('floor') }}" class="input-field" placeholder="e.g. 3F">
                    </div>
                    <div>
                        <label for="room" class="label-field">Room</label>
                        <input type="text" id="room" name="room" value="{{ old('room') }}" class="input-field" placeholder="e.g. 305">
                    </div>
                </div>
                <div>
                    <label for="city" class="label-field">City</label>
                    <input type="text" id="city" name="city" value="{{ old('city') }}" class="input-field" placeholder="Optional">
                </div>
                <button type="submit" class="btn-primary w-full justify-center">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Add Location
                </button>
            </form>
        </x-card>

        <div class="lg:col-span-2">
            <x-card padding="false">
                <table class="w-full text-left">
                    <thead class="border-b border-outline-variant bg-[#F1F5F9]">
                        <tr>
                            <th class="px-4 py-3 text-label-md text-on-surface">Name</th>
                            <th class="px-4 py-3 text-label-md text-on-surface">Place</th>
                            <th class="px-4 py-3 text-right text-label-md text-on-surface">Assets</th>
                            <th class="px-4 py-3 text-center text-label-md text-on-surface">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/30 text-body-sm text-on-surface">
                        @forelse ($locations as $location)
                            <tr class="group transition-colors hover:bg-[#F8FAFC]">
                                <td class="px-4 py-3 font-medium text-on-surface">{{ $location->name }}</td>
                                <td class="px-4 py-3 text-on-surface-variant">
                                    @php
                                        $parts = array_filter([$location->building, $location->floor, $location->room, $location->city]);
                                    @endphp
                                    {{ implode(' · ', $parts) ?: '—' }}
                                </td>
                                <td class="px-4 py-3 text-right text-on-surface-variant">{{ $location->assets_count }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                        <button type="button" title="Edit" class="rounded p-1 text-on-surface-variant hover:text-secondary"
                                                @click="$dispatch('edit-location', { id: {{ $location->id }}, name: '{{ addslashes($location->name) }}', building: '{{ addslashes($location->building ?? '') }}', floor: '{{ addslashes($location->floor ?? '') }}', room: '{{ addslashes($location->room ?? '') }}', city: '{{ addslashes($location->city ?? '') }}' })">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>
                                        @if ($location->assets_count === 0)
                                            <form method="POST" action="{{ route('locations.destroy', $location) }}" onsubmit="return confirm('Delete location {{ $location->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Delete" class="rounded p-1 text-error hover:bg-error/10">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-on-surface-variant">No locations yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-card>
        </div>
    </div>

    <div class="hidden" x-data="{
        open(id, name, building, floor, room, city) {
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-building').value = building;
            document.getElementById('edit-floor').value = floor;
            document.getElementById('edit-room').value = room;
            document.getElementById('edit-city').value = city;
            document.getElementById('edit-form').action = '{{ url('locations') }}/' + id;
            this.$nextTick(() => this.$refs.dialog?.showModal());
        },
    }" x-on:edit-location.window="open($event.detail.id, $event.detail.name, $event.detail.building, $event.detail.floor, $event.detail.room, $event.detail.city)">
        <dialog x-ref="dialog" class="card w-full max-w-md p-0 backdrop:bg-black/40">
            <form id="edit-form" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <h3 class="text-headline-md text-on-surface">Edit Location</h3>
                <div class="mt-5 space-y-4">
                    <div>
                        <label for="edit-name" class="label-field">Name</label>
                        <input type="text" id="edit-name" name="name" required class="input-field">
                    </div>
                    <div>
                        <label for="edit-building" class="label-field">Building</label>
                        <input type="text" id="edit-building" name="building" class="input-field">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="edit-floor" class="label-field">Floor</label>
                            <input type="text" id="edit-floor" name="floor" class="input-field">
                        </div>
                        <div>
                            <label for="edit-room" class="label-field">Room</label>
                            <input type="text" id="edit-room" name="room" class="input-field">
                        </div>
                    </div>
                    <div>
                        <label for="edit-city" class="label-field">City</label>
                        <input type="text" id="edit-city" name="city" class="input-field">
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button" class="btn-secondary" @click="$refs.dialog.close()">Cancel</button>
                    <button type="submit" class="btn-primary">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Save
                    </button>
                </div>
            </form>
        </dialog>
    </div>
</x-app-layout>
