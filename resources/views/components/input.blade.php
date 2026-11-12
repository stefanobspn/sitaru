@props([
    "name",
    "placeholder" => "",
    "legend" => "",
    "label" => "",
    "required" => false
])

@php
    $errorKey = $attributes->wire('model')->value();
@endphp

<fieldset class="fieldset w-full">
    <legend class="fieldset-legend font-medium text-sm">
        {{ $legend }}
        @if ($required)
            <span class="text-red-500 -ml-1">*</span>
        @endif
    </legend>
    <input {{ $attributes->merge(["name" => $name, "type" => "text", "class" => "input w-full", "placeholder" => $placeholder]) }}>
    <p class="label text-sm italic font-medium">{{ $label }}</p>

    @error($errorKey)
        <p class="label text-error text-sm text-wrap">{{ $message }}</p>
    @enderror
</fieldset>
