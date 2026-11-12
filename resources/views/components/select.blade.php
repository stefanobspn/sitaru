@props([
    "name",
    "legend" => "",
    "placeholder" => "Pilih salah satu...",
    "options" => [],
    "required" => false
])

@php
    $errorKey = $attributes->wire('model')->value();
@endphp

<fieldset class="fieldset">
    <legend class="fieldset-legend text-sm">
        {{ $legend }}
        @if ($required)
            <span class="text-red-500 -ml-1">*</span>
        @endif
    </legend>
    <select {{ $attributes->merge(["name" => $name, "class" => "select w-full"]) }}>
        <option value="" disabled selected>{{ $placeholder }}</option>
        @foreach ($options as $option)
            <option> {{$option}} </option>
        @endforeach
    </select>

    @error($errorKey)
        <p class="label text-error text-sm text-wrap">{{ $message }}</p>
    @enderror
</fieldset>
