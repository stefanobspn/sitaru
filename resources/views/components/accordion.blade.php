@props([
    'title',
    'content'
])

<div class="collapse collapse-arrow rounded-sm bg-base-100 border border-slate-300 max-w-2xl">
    <input type="checkbox" name="my-accordion-2"/>
    <div class="collapse-title font-semibold">{{ $title }}</div>
    <div class="collapse-content text-sm">{{ $content }}</div>
</div>
