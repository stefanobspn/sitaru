<div>
    <x-registration-wizard-header :availableSteps="$availableSteps" :currentStep="$currentStep" />

    <main class="w-full flex flex-col items-center px-4 py-12">
        <x-registration-wizard-title :availableSteps="$availableSteps" :currentStep="$currentStep" class="{{ $maxWidth }}" />
        <div class="w-full {{ $maxWidth }} border border-slate-300 rounded-sm p-8">
            @include("livewire.registration-wizard.steps.step-$currentStep")
        </div>
    </main>
</div>
