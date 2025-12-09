<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Könyv adatai') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $entity['name'] ?? 'Ismeretlen' }}</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Szerző</p>
                                <p class="text-lg font-medium">
                                    @if(isset($entity['author_id']))
                                        <a href="{{ route('authors.show', $entity['author_id']) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $entity['author']['name'] ?? 'Ismeretlen' }}
                                        </a>
                                    @else
                                        Ismeretlen
                                    @endif
                                </p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-500">Kategória</p>
                                <p class="text-lg font-medium">
                                    @if(isset($entity['category_id']))
                                        <a href="{{ route('categories.show', $entity['category_id']) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $entity['category']['name'] ?? 'Ismeretlen' }}
                                        </a>
                                    @else
                                        Ismeretlen
                                    @endif
                                </p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-500">ISBN</p>
                                <p class="text-lg font-medium">{{ $entity['isbn'] ?? '-' }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-500">ID</p>
                                <p class="text-lg font-medium">{{ $entity['id'] ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mt-6">
                        @if($isAuthenticated ?? false)
                            <a href="{{ route('books.edit', $entity['id']) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                Szerkesztés
                            </a>
                        @endif
                        
                        <a href="{{ route('books.index') }}" class="text-gray-600 hover:text-gray-900">Vissza a listához</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
