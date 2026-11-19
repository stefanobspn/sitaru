<div>
    <x-registration-wizard-header :availableSteps="$availableSteps" :currentStep="$currentStep" />

    <main class="flex w-full flex-col items-center px-3 py-6 sm:px-4 sm:py-12">
        <x-registration-wizard-title :availableSteps="$availableSteps" :currentStep="$currentStep" class="{{ $maxWidth }}" />
        <div class="w-full min-w-0 {{ $maxWidth }} rounded-sm border border-slate-300 bg-white p-4 sm:p-6 md:p-8">
            @if (! $isPengajuanAkunOpen && $currentStep < 6)
                <div class="text-center py-6">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full {{ $pengajuanAkunStatus === 'closed' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }} mb-4">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">
                        {{ $pengajuanAkunStatus === 'closed' ? 'Tahap Pengajuan Akun Telah Ditutup' : 'Tahap Pengajuan Akun Belum Dibuka' }}
                    </h2>

                    <p class="text-sm text-gray-600 mt-2 max-w-md mx-auto leading-relaxed">
                        {{ $agendaErrorMessage }}
                    </p>

                    <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                        <a href="{{ route('index') }}" class="btn btn-outline btn-sm text-xs border-slate-300 w-full sm:w-auto">
                            Kembali ke Beranda
                        </a>
                        <a href="{{ route('siswa') }}" class="btn btn-primary btn-sm text-white text-xs w-full sm:w-auto">
                            Masuk ke Akun Siswa
                        </a>
                    </div>
                </div>
            @else
                @include("livewire.registration-wizard.steps.step-$currentStep")
            @endif
        </div>
    </main>
</div>
