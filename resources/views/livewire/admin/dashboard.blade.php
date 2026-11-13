<div class="flex h-screen bg-slate-50">
    <x-sidebar-dashboard :active-menu="$activeMenu"/>

    <main class="flex-1 overflow-y-auto p-6">
        @if ($activeMenu === 'beranda')
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Beranda</h1>

                {{-- some stat shit here--}}
            </div>
        @elseif ($activeMenu === 'manage')
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Manajemen SPMB </h1>
                <button class="btn btn-primary mt-5">Tambah</button>
                <x-dashboard-table-admin/>
            </div>
        @endif
    </main>
</div>
