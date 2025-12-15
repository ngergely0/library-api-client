<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Könyvtár API Kliens') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Üdvözöljük a Könyvtár API Kliens alkalmazásban!</h3>
                    
                    <p class="mb-4">
                        Ez az alkalmazás lehetővé teszi a könyvtári adatok (szerzők, könyvek, kategóriák) kezelését.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                        <a href="{{ route('authors.index') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Szerzők</h5>
                            <p class="font-normal text-gray-700">Böngésszen a szerzők között, vagy adjon hozzá újakat.</p>
                        </a>

                        <a href="{{ route('books.index') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Könyvek</h5>
                            <p class="font-normal text-gray-700">Keresse meg kedvenc könyveit, vagy rögzítsen újakat.</p>
                        </a>

                        <a href="{{ route('categories.index') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Kategóriák</h5>
                            <p class="font-normal text-gray-700">Rendszerezze a könyveket kategóriák szerint.</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
