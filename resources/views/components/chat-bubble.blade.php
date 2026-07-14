@props(['role' => 'user', 'content' => ''])

<div class="flex {{ $role === 'user' ? 'justify-end' : 'justify-start' }}">
    <div class="max-w-[80%] {{ $role === 'user'
        ? 'bg-amber-500 text-white rounded-2xl rounded-br-sm px-4 py-3'
        : 'bg-white border border-stone-200 text-stone-700 rounded-2xl rounded-bl-sm px-4 py-3' }}">
        <div class="text-sm prose prose-stone max-w-none">
            {!! $content !!}
        </div>
    </div>
</div>