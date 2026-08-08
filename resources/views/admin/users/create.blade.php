<x-app-layout>
    <x-slot name="title">Add User</x-slot>

    <x-page-header
        title="Add User"
        subtitle="Create a new user account and assign a role." />

    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-error/30 bg-error/10 p-4">
            <ul class="list-inside list-disc space-y-1 text-body-sm text-on-error-container">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.store') }}" class="max-w-3xl">
        @csrf

        <x-card title="Account Details">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="name" class="label-field">Full Name <span class="text-error">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required class="input-field">
                </div>

                <div class="md:col-span-2">
                    <label for="email" class="label-field">Email <span class="text-error">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required class="input-field">
                </div>

                <div>
                    <label for="password" class="label-field">Password <span class="text-error">*</span></label>
                    <input type="password" id="password" name="password" required class="input-field">
                </div>

                <div>
                    <label for="password_confirmation" class="label-field">Confirm Password <span class="text-error">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required class="input-field">
                </div>

                <div>
                    <label for="role" class="label-field">Role <span class="text-error">*</span></label>
                    <select id="role" name="role" required class="input-field">
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" @selected(old('role') === $role->name)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="department" class="label-field">Department</label>
                    <input type="text" id="department" name="department" value="{{ old('department') }}" class="input-field">
                </div>

                <div>
                    <label for="position" class="label-field">Position</label>
                    <input type="text" id="position" name="position" value="{{ old('position') }}" class="input-field">
                </div>

                <div>
                    <label for="phone" class="label-field">Phone</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="input-field">
                </div>
            </div>
        </x-card>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="btn-primary">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                Create User
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</x-app-layout>
