<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Szerző adatai') }}
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

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nemzetiség</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $entity['nationality'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Életkor</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $entity['age'] ?? '-' }} év</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nem</p>
                            <p class="mt-1 text-sm text-gray-900">
                                @if(isset($entity['gender']))
                                    {{ $entity['gender'] === 'male' ? 'Férfi' : ($entity['gender'] === 'female' ? 'Nő' : $entity['gender']) }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <a href="{{ route('authors.books', $entity['id']) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Könyvei
                        </a>
                        
                        @if($isAuthenticated ?? false)
                            <a href="{{ route('authors.edit', $entity['id']) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                Szerkesztés
                            </a>
                        @endif
                        
                        <a href="{{ route('authors.index') }}" class="text-gray-600 hover:text-gray-900">Vissza a listához</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
