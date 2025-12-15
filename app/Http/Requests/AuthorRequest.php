<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthorRequest extends FormRequest
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
            'nationality' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'gender' => 'required|string|in:male,female',
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
            'name.required' => 'A szerző neve kötelező.',
            'name.string' => 'A szerző nevének szövegnek kell lennie.',
            'name.max' => 'A szerző neve maximum 255 karakter lehet.',
            'nationality.required' => 'A nemzetiség megadása kötelező.',
            'nationality.string' => 'A nemzetiségnek szövegnek kell lennie.',
            'nationality.max' => 'A nemzetiség maximum 255 karakter lehet.',
            'age.required' => 'Az életkor megadása kötelező.',
            'age.integer' => 'Az életkornak egész számnak kell lennie.',
            'age.min' => 'Az életkor nem lehet negatív.',
            'gender.required' => 'A nem megadása kötelező.',
            'gender.in' => 'A nem csak "male" vagy "female" lehet.',
        ];
    }
}
