<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'found_date' => ['required', 'date'],
            'image_path' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'location_id' => ['required', 'exists:locations,id'],
            'category_id' => ['required', 'exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom de l\'objet est obligatoire.',
            'name.string' => 'Le nom doit être une chaîne de caractères valide.',
            'name.min' => 'Le nom doit contenir au moins :min caractères.',
            'name.max' => 'Le nom ne peut pas dépasser :max caractères.',

            'description.max' => 'La description ne peut pas dépasser :max caractères.',

            'found_date.required' => 'La date de découverte est obligatoire.',
            'found_date.date' => 'La date saisie n\'est pas valide.',

            'image_path.image' => 'Le fichier doit être une image.',
            'image_path.mimes' => 'L\'image doit être au format :values.',
            'image_path.max' => 'L\'image ne doit pas dépasser 4 Mo.',

            'location_id.required' => 'Veuillez sélectionner un lieu.',
            'location_id.exists' => 'Le lieu sélectionné n\'existe pas dans notre base de données.',

            'category_id.required' => 'Veuillez sélectionner une catégorie.',
            'category_id.exists' => 'La catégorie sélectionnée n\'existe pas dans notre base de données.',
        ];
    }
}