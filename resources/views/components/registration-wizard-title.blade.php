@props([
    'availableSteps' => [],
    'currentStep' => 1
])

@php
    $title = $availableSteps[$currentStep]['title'] ?? '';
    $desc = $availableSteps[$currentStep]['desc'] ?? null;
@endphp

<div {{ $attributes->merge(['class' => "w-full"])}}>
    <div class="mb-6 text-left">
        <span class="text-sm font-semibold text-gray-500 block mb-2">
            Langkah {{ $currentStep }}
        </span>
        <h1 class="text-3xl font-bold">{{ $title }}</h1>
        @if ($desc)
            <p class="text-sm text-gray-500 mt-1">{{ $desc }}</p>
        @endif
        @if($currentStep != 6)
            <div class="mt-2 text-blue-500 underline cursor-pointer">
                <span wire:click="previousStep">Kembali</span>
            </div>
        @endif

    </div>
</div>
