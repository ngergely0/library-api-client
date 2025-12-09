<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kategória adatai') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Név: {{ $entity['name'] }}</h3>
                        <p class="text-sm text-gray-500">ID: {{ $entity['id'] }}</p>
                    </div>

                    <div class="flex items-center gap-4">
                        <a href="{{ route('categories.books', $entity['id']) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Könyvei
                        </a>
                        
                        @if($isAuthenticated ?? false)
                            <a href="{{ route('categories.edit', $entity['id']) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                Szerkesztés
                            </a>
                        @endif
                        
                        <a href="{{ route('categories.index') }}" class="text-gray-600 hover:text-gray-900">Vissza a listához</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
