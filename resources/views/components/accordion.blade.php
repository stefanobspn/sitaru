@props([
    'title',
    'content' => null,
])

<div class="collapse collapse-arrow rounded-sm bg-base-100 border border-slate-300 max-w-2xl w-full">
    <input type="checkbox" name="my-accordion-2"/>
    <div class="collapse-title font-semibold text-gray-800 text-sm md:text-base">{{ $title }}</div>
    <div class="collapse-content text-xs md:text-sm text-gray-600 leading-relaxed">
        {{ $content ?? $slot }}
    </div>
</div>
