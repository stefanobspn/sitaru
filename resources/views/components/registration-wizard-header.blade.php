{{-- AI Generated --}}
@props([
    'availableSteps' => [],
    'currentStep' => $currentStep
])

@php
    $steps = $availableSteps;
    $currentStep = $currentStep ?? 1;
@endphp

<div class="bg-white p-5 w-full">
    <div class="flex items-center justify-between max-w-4xl mx-auto">
        @foreach ($steps as $stepNumber => $stepLabel)
            @php
                $isCompleted = $stepNumber < $currentStep;
                $isCurrent   = $stepNumber === $currentStep;
            @endphp

            {{-- Step Node --}}
            <div class="flex flex-col items-center flex-1 relative">

                {{-- Connector Line (draws to previous step) --}}
                @if (!$loop->first)
                    <div class="absolute top-5 -left-1/2 w-full h-0.75 -translate-y-1/2 z-0 {{ $stepNumber <= $currentStep ? 'bg-slate-400' : 'bg-slate-300' }}"></div>
                @endif

                {{-- Circle Badge --}}
                <div class="relative z-10 flex items-center justify-center w-10 h-10 rounded-full border-4 border-slate-500  transition-all duration-200
                    {{ $isCompleted ? 'bg-emerald-500' : ($isCurrent ? 'bg-sky-500' : 'bg-slate-200 border-slate-300') }}">

                    @if ($isCompleted)
                        {{-- Checkmark Icon --}}
                        <svg class="w-5 h-5 text-white stroke-[3.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    @elseif ($isCurrent)
                        {{-- Current Step Number --}}
                        <span class="text-white text-base font-bold">{{ $stepNumber }}</span>
                    @else
                        {{-- Pending Step Number --}}
                        <span class="text-slate-400 text-base font-bold">{{ $stepNumber }}</span>
                    @endif
                </div>

                {{-- Label --}}
                <span class="mt-3 text-xs md:text-sm font-medium text-center transition-colors duration-200
                    {{ $isCurrent ? 'text-slate-900 font-bold' : ($isCompleted ? 'text-slate-700' : 'text-slate-400') }}">
                    {{ $stepLabel['title'] }}
                </span>
            </div>
        @endforeach
    </div>
</div>
