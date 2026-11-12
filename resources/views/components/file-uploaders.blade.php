@props([
    'legend' => '',
    'label' => '',
    'required' => false
])

@php
    $errorKey = $attributes->wire('model')->value();
@endphp

<fieldset class="fieldset">
    <legend class="fieldset-legend text-sm font-semibold">
        {{ $legend }}
        @if($required)
            <span class="text-red-500 -ml-1">*</span>
        @endif
    </legend>
    <input {{ $attributes->merge(['type' => 'file', 'class' => 'file-input w-full', 'accept' => 'application/pdf']) }} />
    <label class="label">{{ $label }}</label>

    @error($errorKey)
    <p class="label text-error text-sm text-wrap">{{ $message }}</p>
    @enderror
</fieldset>
