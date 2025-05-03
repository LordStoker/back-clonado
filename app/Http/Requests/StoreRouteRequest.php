<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRouteRequest extends FormRequest
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
            'description' => 'nullable|string',
            'distance' => 'required|numeric|min:0',
            'duration' => 'required|numeric|min:0',
            'difficulty_id' => 'required|exists:difficulties,id',
            'landscape_id' => 'required|exists:landscapes,id',
            'terrain_id' => 'required|exists:terrains,id',
            'country_id' => 'required|exists:countries,id',
            'user_id' => 'required|exists:users,id',
            'route_map' => 'required|string' // almacenamos las coordenadas como JSON (string)
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre de la ruta es obligatorio.',
            'description.string' => 'La descripción debe ser una cadena de texto.',
            'distance.required' => 'La distancia es obligatoria.',
            'distance.numeric' => 'La distancia debe ser un número.',
            'duration.required' => 'La duración es obligatoria.',
            'duration.numeric' => 'La duración debe ser un número.',
            'difficulty_id.required' => 'La dificultad es obligatoria.',
            'landscape_id.required' => 'El paisaje es obligatorio.',
            'terrain_id.required' => 'El terreno es obligatorio.',
            'country_id.required' => 'El país es obligatorio.',
            'user_id.required' => 'El usuario es obligatorio.',
            'route_map.required' => 'El mapa de la ruta es obligatorio.'
        ];
    }
    
}
