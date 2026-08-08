<x-app-layout>
    <x-slot name="title">Asset Categories</x-slot>

    <x-page-header
        title="Asset Categories"
        subtitle="Manage the categories used to classify assets. The code prefix is used in generated asset IDs." />

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
        <x-card title="Add Category">
            <form method="POST" action="{{ route('categories.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="label-field">Name <span class="text-error">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required class="input-field" placeholder="e.g. Printer">
                </div>
                <div>
                    <label for="code_prefix" class="label-field">Code Prefix <span class="text-error">*</span></label>
                    <input type="text" id="code_prefix" name="code_prefix" value="{{ old('code_prefix') }}" required maxlength="4" class="input-field font-mono uppercase" placeholder="e.g. PRT">
                </div>
                <button type="submit" class="btn-primary w-full justify-center">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Add Category
                </button>
            </form>
        </x-card>

        <div class="lg:col-span-2">
            <x-card padding="false">
                <table class="w-full text-left">
                    <thead class="border-b border-outline-variant bg-[#F1F5F9]">
                        <tr>
                            <th class="px-4 py-3 text-label-md text-on-surface">Name</th>
                            <th class="px-4 py-3 text-label-md text-on-surface">Code Prefix</th>
                            <th class="px-4 py-3 text-right text-label-md text-on-surface">Assets</th>
                            <th class="px-4 py-3 text-center text-label-md text-on-surface">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/30 text-body-sm text-on-surface">
                        @forelse ($categories as $category)
                            <tr class="group transition-colors hover:bg-[#F8FAFC]">
                                <td class="px-4 py-3 font-medium text-on-surface">{{ $category->name }}</td>
                                <td class="px-4 py-3"><span class="font-mono text-mono text-on-surface-variant">{{ $category->code_prefix }}</span></td>
                                <td class="px-4 py-3 text-right text-on-surface-variant">{{ $category->assets_count }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                        <button type="button" title="Edit" class="rounded p-1 text-on-surface-variant hover:text-secondary"
                                                @click="$dispatch('edit-category', { id: {{ $category->id }}, name: '{{ addslashes($category->name) }}', prefix: '{{ $category->code_prefix }}' })">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>
                                        @if ($category->assets_count === 0)
                                            <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Delete category {{ $category->name }}?')">
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
                                <td colspan="4" class="px-4 py-10 text-center text-on-surface-variant">No categories yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-card>
        </div>
    </div>

    <div class="hidden" x-data="{
        edit: null,
        open(id, name, prefix) {
            this.edit = { id, name, prefix };
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-prefix').value = prefix;
            document.getElementById('edit-form').action = '{{ url('categories') }}/' + id;
            this.$nextTick(() => this.$refs.dialog?.showModal());
        },
    }" x-on:edit-category.window="open($event.detail.id, $event.detail.name, $event.detail.prefix)">
        <dialog x-ref="dialog" class="card w-full max-w-md p-0 backdrop:bg-black/40" @click.self="close()">
            <form id="edit-form" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <h3 class="text-headline-md text-on-surface">Edit Category</h3>
                <div class="mt-5 space-y-4">
                    <div>
                        <label for="edit-name" class="label-field">Name</label>
                        <input type="text" id="edit-name" name="name" required class="input-field">
                    </div>
                    <div>
                        <label for="edit-prefix" class="label-field">Code Prefix</label>
                        <input type="text" id="edit-prefix" name="code_prefix" required maxlength="4" class="input-field font-mono uppercase">
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
