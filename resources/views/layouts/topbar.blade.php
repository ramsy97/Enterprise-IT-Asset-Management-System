<header class="sticky top-0 z-40 flex h-16 items-center justify-between border-b border-outline-variant bg-surface-container-lowest px-6 md:px-8">
    <div class="flex items-center gap-4">
        <button type="button" class="text-on-surface-variant hover:text-on-surface md:hidden" @click="$dispatch('open-sidebar')">
            <span class="material-symbols-outlined">menu</span>
        </button>
        <form method="GET" action="{{ route('assets.index') }}" class="relative hidden w-80 lg:block">
            <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-on-surface-variant/60">search</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search assets, serial, holder..."
                   class="w-full rounded-xl border border-outline-variant bg-surface-container-low py-2 pl-10 pr-3 text-sm text-on-surface placeholder:text-on-surface-variant/60 focus:border-secondary focus:ring-2 focus:ring-secondary/20">
        </form>
    </div>

    <div class="flex items-center gap-4">
        <a href="{{ route('assignments.create') }}" class="text-on-surface-variant hover:text-on-surface" title="New assignment">
            <span class="material-symbols-outlined">add_circle</span>
        </a>
        <div class="h-5 w-px bg-outline-variant"></div>

        <form method="POST" action="{{ route('logout') }}" class="flex items-center">
            @csrf
            <button type="submit" class="flex items-center gap-2 text-on-surface-variant transition-colors hover:text-on-surface">
                <span class="material-symbols-outlined text-[20px]">logout</span>
                <span class="hidden text-body-sm font-medium sm:inline">Sign out</span>
            </button>
        </form>
    </div>
</header>
