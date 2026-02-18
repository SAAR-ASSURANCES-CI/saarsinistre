<div id="sinistres-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6 mb-10">
    @foreach($sinistres as $sinistre)
        <div class="folder flex flex-col items-center cursor-pointer p-3 rounded hover:bg-gray-100 transition" data-sinistre-id="{{ $sinistre->id }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-yellow-400 mb-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12V6.75A2.25 2.25 0 014.5 4.5h3.379c.414 0 .81.17 1.102.474l1.366 1.421c.292.304.688.474 1.102.474H19.5a2.25 2.25 0 012.25 2.25v9A2.25 2.25 0 0119.5 20.25h-15A2.25 2.25 0 012.25 18V12z" />
            </svg>
            <span class="text-center text-sm font-medium">Sinistre {{ $sinistre->numero_sinistre }}</span>
            <span class="text-xs text-gray-400 mt-1">{{ $sinistre->documents->count() }} doc(s)</span>
        </div>
    @endforeach
</div>

<div class="mt-6" id="pagination-links">
    {{ $sinistres->links() }}
</div>
