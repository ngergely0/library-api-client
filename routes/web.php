<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Author routes
Route::get("/authors", [AuthorController::class, "index"])->name('authors.index');
Route::get("/authors/create", [AuthorController::class, "create"])->name('authors.create');
Route::post("/authors", [AuthorController::class, "store"])->name('authors.store');
Route::get("/authors/export/csv", [AuthorController::class, "exportCsv"])->name('authors.export.csv');
Route::get("/authors/export/pdf", [AuthorController::class, "exportPdf"])->name('authors.export.pdf');
Route::get("/authors/{id}", [AuthorController::class, "show"])->name('authors.show');
Route::get("/authors/{id}/edit", [AuthorController::class, "edit"])->name('authors.edit');
Route::put("/authors/{id}", [AuthorController::class, "update"])->name('authors.update');
Route::delete("/authors/{id}", [AuthorController::class, "destroy"])->name('authors.destroy');
Route::get("/authors/{id}/books", [AuthorController::class, "books"])->name('authors.books');

// Book routes
Route::get("/books", [BookController::class, "index"])->name('books.index');
Route::get("/books/create", [BookController::class, "create"])->name('books.create');
Route::post("/books", [BookController::class, "store"])->name('books.store');
Route::get("/books/export/csv", [BookController::class, "exportCsv"])->name('books.export.csv');
Route::get("/books/export/pdf", [BookController::class, "exportPdf"])->name('books.export.pdf');
Route::get("/books/{id}", [BookController::class, "show"])->name('books.show');
Route::get("/books/{id}/edit", [BookController::class, "edit"])->name('books.edit');
Route::put("/books/{id}", [BookController::class, "update"])->name('books.update');
Route::delete("/books/{id}", [BookController::class, "destroy"])->name('books.destroy');


// Category routes
Route::get("/categories", [CategoryController::class, "index"])->name('categories.index');
Route::get("/categories/create", [CategoryController::class, "create"])->name('categories.create');
Route::post("/categories", [CategoryController::class, "store"])->name('categories.store');
Route::get("/categories/export/csv", [CategoryController::class, "exportCsv"])->name('categories.export.csv');
Route::get("/categories/export/pdf", [CategoryController::class, "exportPdf"])->name('categories.export.pdf');
Route::get("/categories/{id}", [CategoryController::class, "show"])->name('categories.show');
Route::get("/categories/{id}/edit", [CategoryController::class, "edit"])->name('categories.edit');
Route::put("/categories/{id}", [CategoryController::class, "update"])->name('categories.update');
Route::delete("/categories/{id}", [CategoryController::class, "destroy"])->name('categories.destroy');
Route::get("/categories/{id}/books", [CategoryController::class, "books"])->name('categories.books');

require __DIR__.'/auth.php';
