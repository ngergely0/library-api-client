<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\BookRequest;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $needle = $request->get('needle');

        try {
            // Fetch all books and filter on client side to support ID search
            $response = Http::api()->get('books');

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Ismeretlen hiba történt.';
                return view('books.index', [
                    'entities' => [],
                    'isAuthenticated' => $this->isAuthenticated()
                ])->with('error', "Hiba történt a lekérdezés során: $message");
            }

            $entities = ResponseHelper::getData($response);

            if ($needle) {
                $needle = strtolower($needle);
                $entities = array_filter($entities, function($book) use ($needle) {
                    return str_contains(strtolower($book['name'] ?? ''), $needle) || 
                           strval($book['id'] ?? '') === $needle ||
                           str_contains(strtolower($book['author']['name'] ?? ''), $needle) ||
                           str_contains(strtolower($book['category']['name'] ?? ''), $needle) ||
                           str_contains(strval($book['isbn'] ?? ''), $needle);
                });
            }

            return view('books.index', ['entities' => $entities, 'isAuthenticated' => $this->isAuthenticated()]);

        } catch (\Exception $e) {
            return view('books.index', [
                'entities' => [],
                'isAuthenticated' => $this->isAuthenticated()
            ])->with('error', "Nem sikerült betölteni a könyveket: " . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $response = Http::api()
                ->withToken($this->token)
                ->get("/books/$id");

            if ($response->failed()) {
                $message = $response->json('message') ?? 'A könyv nem található vagy hiba történt.';
                return redirect()
                    ->route('books.index')
                    ->with('error', "Hiba: $message");
            }

            $body = $response->json();
            $entity = $body['book'] ?? null;

            if (!$entity) {
                return redirect()
                    ->route('books.index')
                    ->with('error', "A könyv adatai nem érhetők el.");
            }

            return view('books.show', ['entity' => $entity, 'isAuthenticated' => $this->isAuthenticated()]);

        } catch (\Exception $e) {
            return redirect()
                ->route('books.index')
                ->with('error', "Nem sikerült betölteni a könyv adatait: " . $e->getMessage());
        }
    }

    public function create()
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login')->with('error', 'Be kell jelentkezni a művelet végrehajtásához.');
        }

        try {
            // Fetch authors and categories for dropdowns
            $authorsResponse = Http::api()->get('/authors');
            $categoriesResponse = Http::api()->get('/categories');

            $authors = ResponseHelper::getData($authorsResponse);
            $categories = ResponseHelper::getData($categoriesResponse);

            return view('books.create', [
                'authors' => $authors,
                'categories' => $categories
            ]);

        } catch (\Exception $e) {
            return redirect()
                ->route('books.index')
                ->with('error', "Nem sikerült betölteni az űrlapot: " . $e->getMessage());
        }
    }

    public function store(BookRequest $request)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login')->with('error', 'Be kell jelentkezni a művelet végrehajtásához.');
        }

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->post('/books', [
                    'name' => $request->get('name'),
                    'author_id' => $request->get('author_id'),
                    'category_id' => $request->get('category_id'),
                    'isbn' => $request->get('isbn'),
                    'price' => $request->get('price'),
                    'publication_date' => $request->get('publication_date'),
                    'edition' => $request->get('edition'),
                ]);

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült létrehozni a könyvet.';
                return redirect()
                    ->route('books.index')
                    ->with('error', "Hiba: $message");
            }

            $name = $request->get('name');
            return redirect()
                ->route('books.index')
                ->with('success', "$name könyv sikeresen létrehozva!");

        } catch (\Exception $e) {
            return redirect()
                ->route('books.index')
                ->with('error', "Nem sikerült kommunikálni az API-val: " . $e->getMessage());
        }
    }

    public function edit($id)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login')->with('error', 'Be kell jelentkezni a művelet végrehajtásához.');
        }

        try {
            $bookResponse = Http::api()
                ->withToken($this->token)
                ->get("/books/$id");

            $authorsResponse = Http::api()->get('/authors');
            $categoriesResponse = Http::api()->get('/categories');

            if ($bookResponse->failed()) {
                $message = $bookResponse->json('message') ?? 'A könyv nem található vagy hiba történt.';
                return redirect()
                    ->route('books.index')
                    ->with('error', "Hiba: $message");
            }

            $body = $bookResponse->json();
            $entity = $body['book'] ?? null;

            if (!$entity) {
                return redirect()
                    ->route('books.index')
                    ->with('error', "A könyv adatai nem érhetők el.");
            }

            $authors = ResponseHelper::getData($authorsResponse);
            $categories = ResponseHelper::getData($categoriesResponse);

            return view('books.edit', [
                'entity' => $entity,
                'authors' => $authors,
                'categories' => $categories
            ]);

        } catch (\Exception $e) {
            return redirect()
                ->route('books.index')
                ->with('error', "Nem sikerült betölteni a könyv szerkesztő nézetét: " . $e->getMessage());
        }
    }

    public function update(BookRequest $request, $id)
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login')->with('error', 'Be kell jelentkezni a művelet végrehajtásához.');
        }

        try {
            $response = Http::api()
                ->withToken($this->token)
                ->put("/books/$id", [
                    'name' => $request->get('name'),
                    'author_id' => $request->get('author_id'),
                    'category_id' => $request->get('category_id'),
                    'isbn' => $request->get('isbn'),
                    'price' => $request->get('price'),
                    'publication_date' => $request->get('publication_date'),
                    'edition' => $request->get('edition'),
                ]);

            if ($response->successful()) {
                $name = $request->get('name');
                return redirect()
                    ->route('books.index')
                    ->with('success', "$name könyv sikeresen frissítve!");
            }

            $errorMessage = $response->json('message') ?? 'Ismeretlen hiba történt.';
            return redirect()
                ->route('books.index')
                ->with('error', "Hiba történt: $errorMessage");

        } catch (\Exception $e) {
            return redirect()
                ->route('books.index')
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
                ->delete("/books/$id");

            if ($response->failed()) {
                $message = $response->json('message') ?? 'Nem sikerült törölni a könyvet.';
                return redirect()
                    ->route('books.index')
                    ->with('error', "Hiba: $message");
            }

            $body = $response->json();
            $name = $body['name'] ?? 'Ismeretlen';

            return redirect()
                ->route('books.index')
                ->with('success', "$name könyv sikeresen törölve!");

        } catch (\Exception $e) {
            return redirect()
                ->route('books.index')
                ->with('error', "Nem sikerült kommunikálni az API-val: " . $e->getMessage());
        }
    }

    public function exportCsv(Request $request)
    {
        $exportService = new ExportService();
        
        // Get filtered data
        $needle = $request->get('needle');
        $url = $needle ? "books?needle=" . urlencode($needle) : "books";
        
        try {
            $response = Http::api()->get($url);
            
            if ($response->failed()) {
                return redirect()
                    ->route('books.index')
                    ->with('error', 'Nem sikerült exportálni az adatokat.');
            }
            
            $entities = ResponseHelper::getData($response);
            
            // Transform data to include author name and other details
            $transformedData = array_map(function($book) {
                return [
                    'id' => $book['id'] ?? '',
                    'title' => $book['name'] ?? '',
                    'author_name' => $book['author']['name'] ?? '',
                    'category_name' => $book['category']['name'] ?? '',
                    'year' => $book['isbn'] ?? '',
                    'price' => $book['price'] ?? '',
                    'publication_date' => $book['publication_date'] ?? '',
                    'edition' => $book['edition'] ?? '',
                ];
            }, $entities);
            
            $headers = $exportService->getColumnConfig('books');
            
            return $exportService->exportToCsv(
                $transformedData, 
                'konyvek_' . date('Y-m-d_His'), 
                $headers
            );
            
        } catch (\Exception $e) {
            return redirect()
                ->route('books.index')
                ->with('error', "Export hiba: " . $e->getMessage());
        }
    }

    public function exportPdf(Request $request)
    {
        $exportService = new ExportService();
        
        // Get filtered data
        $needle = $request->get('needle');
        $url = $needle ? "books?needle=" . urlencode($needle) : "books";
        
        try {
            $response = Http::api()->get($url);
            
            if ($response->failed()) {
                return redirect()
                    ->route('books.index')
                    ->with('error', 'Nem sikerült exportálni az adatokat.');
            }
            
            $entities = ResponseHelper::getData($response);
            
            // Transform data to include author name and other details
            $transformedData = array_map(function($book) {
                return [
                    'id' => $book['id'] ?? '',
                    'title' => $book['name'] ?? '',
                    'author_name' => $book['author']['name'] ?? '',
                    'category_name' => $book['category']['name'] ?? '',
                    'year' => $book['isbn'] ?? '',
                    'price' => $book['price'] ?? '',
                    'publication_date' => $book['publication_date'] ?? '',
                    'edition' => $book['edition'] ?? '',
                ];
            }, $entities);
            
            $headers = $exportService->getColumnConfig('books');
            $title = $exportService->getTitle('books');
            $logoPath = public_path('images/logo.png');
            
            return $exportService->exportToPdf(
                $transformedData,
                'konyvek_' . date('Y-m-d_His'),
                $title,
                $headers,
                $logoPath
            );
            
        } catch (\Exception $e) {
            return redirect()
                ->route('books.index')
                ->with('error', "Export hiba: " . $e->getMessage());
        }
    }
}
