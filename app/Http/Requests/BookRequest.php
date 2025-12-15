<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'author_id' => 'required|integer',
            'category_id' => 'required|integer',
            'isbn' => 'nullable|string|max:20',
            'price' => 'required|integer|min:0',
            'publication_date' => 'required|date',
            'edition' => 'required|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'A könyv címe kötelező.',
            'name.string' => 'A könyv címének szövegnek kell lennie.',
            'name.max' => 'A könyv címe maximum 255 karakter lehet.',
            'author_id.required' => 'A szerző kiválasztása kötelező.',
            'author_id.integer' => 'Érvénytelen szerző azonosító.',
            'category_id.required' => 'A kategória kiválasztása kötelező.',
            'category_id.integer' => 'Érvénytelen kategória azonosító.',
            'isbn.string' => 'Az ISBN szövegnek kell lennie.',
            'isbn.max' => 'Az ISBN maximum 20 karakter lehet.',
            'price.required' => 'Az ár megadása kötelező.',
            'price.integer' => 'Az árnak egész számnak kell lennie.',
            'price.min' => 'Az ár nem lehet negatív.',
            'publication_date.required' => 'A kiadás dátuma kötelező.',
            'publication_date.date' => 'Érvénytelen dátum formátum.',
            'edition.required' => 'A kiadás megadása kötelező.',
            'edition.string' => 'A kiadásnak szövegnek kell lennie.',
            'edition.max' => 'A kiadás maximum 255 karakter lehet.',
        ];
    }
}
