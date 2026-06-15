<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ItemRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $minFoundDate = now()->subYear()->format('Y-m-d');

        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'admin_details' => ['required', 'string', 'min:20', 'max:5000'],
            'found_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'before_or_equal:today',
                'after_or_equal:' . $minFoundDate,
            ],
            'image_path' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'location_id' => ['required', 'exists:locations,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'contact_phone' => ['required', 'string', 'min:10', 'max:20', 'regex:/^[+0-9][0-9\s.-]{8,18}$/'],
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

            'admin_details.required' => 'Le message détaillé pour l\'administration est obligatoire.',
            'admin_details.min' => 'Décrivez l\'objet en détail (au moins :min caractères) : état, marque, particularités, circonstances de la trouvaille…',
            'admin_details.max' => 'Le message détaillé ne peut pas dépasser :max caractères.',

            'found_date.required' => 'La date de découverte est obligatoire.',
            'found_date.date' => 'La date saisie n\'est pas valide.',
            'found_date.date_format' => 'La date doit être au format JJ/MM/AAAA.',
            'found_date.before_or_equal' => 'La date de découverte ne peut pas être dans le futur.',
            'found_date.after_or_equal' => 'La date de découverte ne peut pas remonter à plus d\'un an.',

            'image_path.image' => 'Le fichier doit être une image.',
            'image_path.mimes' => 'L\'image doit être au format :values.',
            'image_path.max' => 'L\'image ne doit pas dépasser 4 Mo.',

            'location_id.required' => 'Veuillez sélectionner un lieu.',
            'location_id.exists' => 'Le lieu sélectionné n\'existe pas dans notre base de données.',

            'category_id.required' => 'Veuillez sélectionner une catégorie.',
            'category_id.exists' => 'La catégorie sélectionnée n\'existe pas dans notre base de données.',

            'contact_phone.required' => 'Votre numéro de téléphone est obligatoire.',
            'contact_phone.min' => 'Le numéro de téléphone doit contenir au moins :min caractères.',
            'contact_phone.max' => 'Le numéro de téléphone ne peut pas dépasser :max caractères.',
            'contact_phone.regex' => 'Le format du numéro de téléphone n\'est pas valide.',
        ];
    }
}
