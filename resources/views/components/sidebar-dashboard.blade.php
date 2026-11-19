@props(['activeMenu' => 'beranda'])

<div class="flex h-auto w-full shrink-0 flex-row justify-between border-b border-slate-300 bg-white md:h-screen md:w-16 md:flex-col md:border-b-0 md:border-e">
    <div class="w-full md:w-auto">
        <ul class="flex justify-around gap-1 border-gray-100 px-2 py-2 md:flex-col md:space-y-1 md:border-t md:pt-4">
            <li>
                <a
                    wire:click.prevent="setMenu('beranda')"
                    href="#"
                    @class([
                        'group relative grid place-content-center rounded-lg px-4 py-2 transition-colors',
                        'bg-gray-100 text-gray-900' => $activeMenu === 'beranda',
                        'text-gray-700 hover:bg-gray-100 hover:text-gray-900' => $activeMenu !== 'beranda',
                    ])
                    aria-label="General"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="1.5em" height="1.5em" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0z" fill="none" />
                        <path fill="currentColor" d="M10 20v-6h4v6h5v-8h3L12 3L2 12h3v8z" />
                    </svg>

                    <span class="invisible pointer-events-none absolute inset-s-full top-1/2 ms-4 -translate-y-1/2 rounded-sm bg-gray-200 px-2 py-1.5 text-xs font-medium text-gray-700 group-hover:visible">
                        Beranda
                    </span>
                </a>
            </li>


            <li>
                <a
                    wire:click.prevent="setMenu('manage')"
                    href="#"
                    @class([
                        'group relative grid place-content-center rounded-lg px-4 py-2 transition-colors',
                        'bg-gray-100 text-gray-900' => $activeMenu === 'manage',
                        'text-gray-700 hover:bg-gray-100 hover:text-gray-900' => $activeMenu !== 'manage',
                    ])
                    aria-label="Teams"
                >
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>

                    <span class="invisible pointer-events-none absolute inset-s-full top-1/2 ms-4 -translate-y-1/2 rounded-sm bg-gray-200 px-2 py-1.5 text-xs font-medium text-gray-700 group-hover:visible">
                        Manajemen SPMB
                    </span>
                </a>
            </li>
        </ul>
    </div>

    <div class="border-s border-gray-100 bg-white p-2 md:sticky md:inset-x-0 md:bottom-0 md:border-s-0 md:border-t">
        <a
            wire:click="logout"
            href="#"
            class="group relative grid place-content-center rounded-lg px-4 py-2 text-gray-700 transition-colors hover:bg-gray-100 hover:text-gray-900"
            aria-label="Logout"
        >
            <svg
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                class="size-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                />
            </svg>

            <span
                class="invisible pointer-events-none absolute inset-s-full top-1/2 ms-4 -translate-y-1/2 rounded-sm bg-gray-200 px-2 py-1.5 text-xs font-medium text-gray-700 group-hover:visible"
            >
        Logout
      </span>
        </a>
    </div>
</div>
