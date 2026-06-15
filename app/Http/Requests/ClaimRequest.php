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
            'claim_notes' => ['required', 'string', 'min:20', 'max:5000'],
            'contact_phone' => ['required', 'string', 'min:10', 'max:20', 'regex:/^[+0-9][0-9\s.-]{8,18}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'claim_notes.required' => 'La description de votre réclamation est obligatoire.',
            'claim_notes.string' => 'La description doit être une chaîne de caractères valide.',
            'claim_notes.min' => 'Détaillez votre réclamation (au moins :min caractères) : éléments distinctifs, circonstances, preuves d\'appartenance…',
            'claim_notes.max' => 'La description ne peut pas dépasser :max caractères.',

            'contact_phone.required' => 'Votre numéro de téléphone est obligatoire.',
            'contact_phone.min' => 'Le numéro de téléphone doit contenir au moins :min caractères.',
            'contact_phone.max' => 'Le numéro de téléphone ne peut pas dépasser :max caractères.',
            'contact_phone.regex' => 'Le format du numéro de téléphone n\'est pas valide.',
        ];
    }
}
