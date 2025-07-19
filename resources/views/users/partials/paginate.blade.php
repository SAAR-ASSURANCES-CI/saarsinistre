<div class="flex items-center justify-between mt-6">
    <!-- Pagination pour les gestionnaires -->
    <div id="gestionnaires-pagination" class="hidden w-full">
        <div class="flex items-center justify-between">
            <div class="flex items-center text-sm text-gray-700">
                <span>
                    Affichage de <span class="font-medium">{{ $gestionnaires->firstItem() }}</span>
                    à <span class="font-medium">{{ $gestionnaires->lastItem() }}</span> sur
                    <span class="font-medium">{{ $gestionnaires->total() }}</span> résultats
                </span>
            </div>

            <div class="flex space-x-2">
                <div class="flex items-center space-x-2">
                    <a href="{{ $gestionnaires->previousPageUrl() }}"
                        class="px-3 py-1 border rounded-lg {{ !$gestionnaires->onFirstPage() ? 'bg-white hover:bg-gray-50' : 'bg-gray-100 cursor-not-allowed' }}"
                        @if ($gestionnaires->onFirstPage()) disabled @endif>
                        &larr; Préc.
                    </a>
                    <span class="px-3 py-1 bg-saar-blue text-white rounded-lg">
                        {{ $gestionnaires->currentPage() }}
                    </span>
                    <a href="{{ $gestionnaires->nextPageUrl() }}"
                        class="px-3 py-1 border rounded-lg {{ $gestionnaires->hasMorePages() ? 'bg-white hover:bg-gray-50' : 'bg-gray-100 cursor-not-allowed' }}"
                        @if (!$gestionnaires->hasMorePages()) disabled @endif>
                        Suiv. &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination pour les assurés-->
    <div id="assures-pagination" class="hidden w-full">
        <div class="flex items-center justify-between">
            <div class="flex items-center text-sm text-gray-700">
                <span>
                    Affichage de <span class="font-medium">{{ $assures->firstItem() }}</span>
                    à <span class="font-medium">{{ $assures->lastItem() }}</span> sur
                    <span class="font-medium">{{ $assures->total() }}</span> résultats
                </span>
            </div>

            <div class="flex space-x-2">
                <div class="flex items-center space-x-2">
                    <a href="{{ $assures->previousPageUrl() }}"
                        class="px-3 py-1 border rounded-lg {{ !$assures->onFirstPage() ? 'bg-white hover:bg-gray-50' : 'bg-gray-100 cursor-not-allowed' }}"
                        @if ($assures->onFirstPage()) disabled @endif>
                        &larr; Préc.
                    </a>
                    </a>
                    <span class="px-3 py-1 bg-saar-blue text-white rounded-lg">
                        {{ $assures->currentPage() }}
                    </span>
                    <a href="{{ $assures->nextPageUrl() }}"
                        class="px-3 py-1 border rounded-lg {{ $assures->hasMorePages() ? 'bg-white hover:bg-gray-50' : 'bg-gray-100 cursor-not-allowed' }}"
                        @if (!$assures->hasMorePages()) disabled @endif>
                        Suiv. &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
