<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'claim_notes' => ['required', 'string', 'min:3', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'claim_notes.required' => 'Les notes de réclamation sont obligatoires.',
            'claim_notes.string' => 'Les notes doivent être une chaîne de caractères valide.',
            'claim_notes.min' => 'Vos explications doivent contenir au moins :min caractères.',
            'claim_notes.max' => 'Vos explications ne peuvent pas dépasser :max caractères.',
        ];
    }
}