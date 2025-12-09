<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $needle = $request->get('needle');

        try {
            $url = $needle ? "categories?needle=" . urlencode($needle) : "categories";

            $response = Http::api()->get($url);

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Ismeretlen hiba történt.';
                return redirect()
                    ->route('categories.index')
                    ->with('error', "Hiba történt a lekérdezés során: $message");
            }

            $entities = ResponseHelper::getData($response);

            return view('categories.index', ['entities' => $entities, 'isAuthenticated' => $this->isAuthenticated()]);

        } catch (\Exception $e) {
            return redirect()
                ->route('categories.index')
                ->with('error', "Nem sikerült betölteni a kategóriákat: " . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $response = Http::api()
                ->withToken($this->token)
                ->get("/categories/$id");

            if ($response->failed()) {
                $message = $response->json('message') ?? 'A kategória nem található vagy hiba történt.';
                return redirect()
                    ->route('categories.index')
                    ->with('error', "Hiba: $message");
            }

            $body = $response->json();
            $entity = $body['category'] ?? null;

            if (!$entity) {
                return redirect()
                    ->route('categories.index')
                    ->with('error', "A kategória adatai nem érhetők el.");
            }

            return view('categories.show', ['entity' => $entity, 'isAuthenticated' => $this->isAuthenticated()]);

        } catch (\Exception $e) {
            return redirect()
                ->route('categories.index')
                ->with('error', "Nem sikerült betölteni a kategória adatait: " . $e->getMessage());
        }
    }

    public function create()
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login')->with('error', 'Be kell jelentkezni a művelet végrehajtásához.');
        }

        return view('categories.create');
    }

    public function store(CategoryRequest $request)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login')->with('error', 'Be kell jelentkezni a művelet végrehajtásához.');
        }

        $name = $request->get('name');

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->post('/categories', ['name' => $name]);

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült létrehozni a kategóriát.';
                return redirect()
                    ->route('categories.index')
                    ->with('error', "Hiba: $message");
            }

            return redirect()
                ->route('categories.index')
                ->with('success', "$name kategória sikeresen létrehozva!");

        } catch (\Exception $e) {
            return redirect()
                ->route('categories.index')
                ->with('error', "Nem sikerült kommunikálni az API-val: " . $e->getMessage());
        }
    }

    public function edit($id)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login')->with('error', 'Be kell jelentkezni a művelet végrehajtásához.');
        }

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->get("/categories/$id");

            if ($response->failed()) {
                $message = $response->json('message') ?? 'A kategória nem található vagy hiba történt.';
                return redirect()
                    ->route('categories.index')
                    ->with('error', "Hiba: $message");
            }

            $body = $response->json();
            $entity = $body['category'] ?? null;

            if (!$entity) {
                return redirect()
                    ->route('categories.index')
                    ->with('error', "A kategória adatai nem érhetők el.");
            }

            return view('categories.edit', ['entity' => $entity]);

        } catch (\Exception $e) {
            return redirect()
                ->route('categories.index')
                ->with('error', "Nem sikerült betölteni a kategória szerkesztő nézetét: " . $e->getMessage());
        }
    }

    public function update(CategoryRequest $request, $id)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login')->with('error', 'Be kell jelentkezni a művelet végrehajtásához.');
        }

        $name = $request->get('name');

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->put("/categories/$id", ['name' => $name]);

            if ($response->successful()) {
                return redirect()
                    ->route('categories.index')
                    ->with('success', "$name kategória sikeresen frissítve!");
            }

            $errorMessage = $response->json('message') ?? 'Ismeretlen hiba történt.';
            return redirect()
                ->route('categories.index')
                ->with('error', "Hiba történt: $errorMessage");

        } catch (\Exception $e) {
            return redirect()
                ->route('categories.index')
                ->with('error', "Nem sikerült frissíteni: " . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login')->with('error', 'Be kell jelentkezni a művelet végrehajtásához.');
        }

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->delete("/categories/$id");

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült törölni a kategóriát.';
                return redirect()
                    ->route('categories.index')
                    ->with('error', "Hiba: $message");
            }

            $body = $response->json();
            $name = $body['name'] ?? 'Ismeretlen';

            return redirect()
                ->route('categories.index')
                ->with('success', "$name kategória sikeresen törölve!");

        } catch (\Exception $e) {
            return redirect()
                ->route('categories.index')
                ->with('error', "Nem sikerült kommunikálni az API-val: " . $e->getMessage());
        }
    }

    public function books($id)
    {
        try {
            $response = Http::api()
                ->withToken($this->token)
                ->get("/categories/$id/books");

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült betölteni a könyveket.';
                return redirect()
                    ->route('categories.index')
                    ->with('error', "Hiba: $message");
            }

            $body = $response->json();
            $category = $body['category'] ?? null;
            $books = $body['books'] ?? [];

            return view('categories.books', [
                'category' => $category,
                'books' => $books,
                'isAuthenticated' => $this->isAuthenticated()
            ]);

        } catch (\Exception $e) {
            return redirect()
                ->route('categories.index')
                ->with('error', "Nem sikerült betölteni a könyveket: " . $e->getMessage());
        }
    }
}
