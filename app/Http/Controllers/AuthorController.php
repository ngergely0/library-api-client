<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\AuthorRequest;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        $needle = $request->get('needle');

        try {
            // Fetch all authors and filter on client side to support ID search
            $response = Http::api()->get('authors');

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Ismeretlen hiba történt.';
                // Return view with error instead of redirecting to avoid loop
                return view('authors.index', [
                    'entities' => [], 
                    'isAuthenticated' => $this->isAuthenticated()
                ])->with('error', "Hiba történt a lekérdezés során: $message");
            }

            $entities = ResponseHelper::getData($response);

            if ($needle) {
                $needle = strtolower($needle);
                $entities = array_filter($entities, function($author) use ($needle) {
                    return str_contains(strtolower($author['name'] ?? ''), $needle) || 
                           strval($author['id'] ?? '') === $needle;
                });
            }

            return view('authors.index', ['entities' => $entities, 'isAuthenticated' => $this->isAuthenticated()]);

        } catch (\Exception $e) {
            // Return view with error instead of redirecting to avoid loop
            return view('authors.index', [
                'entities' => [], 
                'isAuthenticated' => $this->isAuthenticated()
            ])->with('error', "Nem sikerült betölteni a szerzőket: " . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $response = Http::api()
                ->withToken($this->token)
                ->get("/authors/$id");

            if ($response->failed()) {
                $message = $response->json('message') ?? 'A szerző nem található vagy hiba történt.';
                return redirect()
                    ->route('authors.index')
                    ->with('error', "Hiba: $message");
            }

            $body = $response->json();
            $entity = $body['author'] ?? null;

            if (!$entity) {
                return redirect()
                    ->route('authors.index')
                    ->with('error', "A szerző adatai nem érhetők el.");
            }

            return view('authors.show', ['entity' => $entity, 'isAuthenticated' => $this->isAuthenticated()]);

        } catch (\Exception $e) {
            return redirect()
                ->route('authors.index')
                ->with('error', "Nem sikerült betölteni a szerző adatait: " . $e->getMessage());
        }
    }

    public function create()
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login')->with('error', 'Be kell jelentkezni a művelet végrehajtásához.');
        }

        return view('authors.create');
    }

    public function store(AuthorRequest $request)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login')->with('error', 'Be kell jelentkezni a művelet végrehajtásához.');
        }

        $name = $request->get('name');
        $nationality = $request->get('nationality');
        $age = $request->get('age');
        $gender = $request->get('gender');

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->post('/authors', [
                    'name' => $name,
                    'nationality' => $nationality,
                    'age' => $age,
                    'gender' => $gender
                ]);

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült létrehozni a szerzőt.';
                return redirect()
                    ->route('authors.index')
                    ->with('error', "Hiba: $message");
            }

            return redirect()
                ->route('authors.index')
                ->with('success', "$name szerző sikeresen létrehozva!");

        } catch (\Exception $e) {
            return redirect()
                ->route('authors.index')
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
                ->get("/authors/$id");

            if ($response->failed()) {
                $message = $response->json('message') ?? 'A szerző nem található vagy hiba történt.';
                return redirect()
                    ->route('authors.index')
                    ->with('error', "Hiba: $message");
            }

            $body = $response->json();
            $entity = $body['author'] ?? null;

            if (!$entity) {
                return redirect()
                    ->route('authors.index')
                    ->with('error', "A szerző adatai nem érhetők el.");
            }

            return view('authors.edit', ['entity' => $entity]);

        } catch (\Exception $e) {
            return redirect()
                ->route('authors.index')
                ->with('error', "Nem sikerült betölteni a szerző szerkesztő nézetét: " . $e->getMessage());
        }
    }

    public function update(AuthorRequest $request, $id)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login')->with('error', 'Be kell jelentkezni a művelet végrehajtásához.');
        }

        $name = $request->get('name');
        $nationality = $request->get('nationality');
        $age = $request->get('age');
        $gender = $request->get('gender');

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->put("/authors/$id", [
                    'name' => $name,
                    'nationality' => $nationality,
                    'age' => $age,
                    'gender' => $gender
                ]);

            if ($response->successful()) {
                return redirect()
                    ->route('authors.index')
                    ->with('success', "$name szerző sikeresen frissítve!");
            }

            $errorMessage = $response->json('message') ?? 'Ismeretlen hiba történt.';
            return redirect()
                ->route('authors.index')
                ->with('error', "Hiba történt: $errorMessage");

        } catch (\Exception $e) {
            return redirect()
                ->route('authors.index')
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
                ->delete("/authors/$id");

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült törölni a szerzőt.';
                return redirect()
                    ->route('authors.index')
                    ->with('error', "Hiba: $message");
            }

            $body = $response->json();
            $name = $body['name'] ?? 'Ismeretlen';

            return redirect()
                ->route('authors.index')
                ->with('success', "$name szerző sikeresen törölve!");

        } catch (\Exception $e) {
            return redirect()
                ->route('authors.index')
                ->with('error', "Nem sikerült kommunikálni az API-val: " . $e->getMessage());
        }
    }

    public function books($id)
    {
        try {
            $response = Http::api()
                ->withToken($this->token)
                ->get("/authors/$id/books");

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült betölteni a könyveket.';
                return redirect()
                    ->route('authors.index')
                    ->with('error', "Hiba: $message");
            }

            $body = $response->json();
            $author = $body['author'] ?? null;
            $books = $body['books'] ?? [];

            return view('authors.books', [
                'author' => $author,
                'books' => $books,
                'isAuthenticated' => $this->isAuthenticated()
            ]);

        } catch (\Exception $e) {
            return redirect()
                ->route('authors.index')
                ->with('error', "Nem sikerült betölteni a könyveket: " . $e->getMessage());
        }
    }

    public function exportCsv(Request $request)
    {
        $exportService = new ExportService();
        
        // Get filtered data
        $needle = $request->get('needle');
        $url = $needle ? "authors?needle=" . urlencode($needle) : "authors";
        
        try {
            $response = Http::api()->get($url);
            
            if ($response->failed()) {
                return redirect()
                    ->route('authors.index')
                    ->with('error', 'Nem sikerült exportálni az adatokat.');
            }
            
            $entities = ResponseHelper::getData($response);
            
            // Transform data to ensure keys match config
            $transformedData = array_map(function($author) {
                return [
                    'id' => $author['id'] ?? '',
                    'name' => $author['name'] ?? '',
                    'nationality' => $author['nationality'] ?? '',
                    'age' => $author['age'] ?? '',
                    'gender' => $author['gender'] ?? '',
                ];
            }, $entities);

            $headers = $exportService->getColumnConfig('authors');
            
            return $exportService->exportToCsv(
                $transformedData, 
                'szerzok_' . date('Y-m-d_His'), 
                $headers
            );
            
        } catch (\Exception $e) {
            return redirect()
                ->route('authors.index')
                ->with('error', "Export hiba: " . $e->getMessage());
        }
    }

    public function exportPdf(Request $request)
    {
        $exportService = new ExportService();
        
        // Get filtered data
        $needle = $request->get('needle');
        $url = $needle ? "authors?needle=" . urlencode($needle) : "authors";
        
        try {
            $response = Http::api()->get($url);
            
            if ($response->failed()) {
                return redirect()
                    ->route('authors.index')
                    ->with('error', 'Nem sikerült exportálni az adatokat.');
            }
            
            $entities = ResponseHelper::getData($response);
            
            // Transform data to ensure keys match config
            $transformedData = array_map(function($author) {
                return [
                    'id' => $author['id'] ?? '',
                    'name' => $author['name'] ?? '',
                    'nationality' => $author['nationality'] ?? '',
                    'age' => $author['age'] ?? '',
                    'gender' => $author['gender'] ?? '',
                ];
            }, $entities);

            $headers = $exportService->getColumnConfig('authors');
            $title = $exportService->getTitle('authors');
            $logoPath = public_path('images/logo.png');
            
            return $exportService->exportToPdf(
                $transformedData,
                'szerzok_' . date('Y-m-d_His'),
                $title,
                $headers,
                $logoPath
            );
            
        } catch (\Exception $e) {
            return redirect()
                ->route('authors.index')
                ->with('error', "Export hiba: " . $e->getMessage());
        }
    }
}
