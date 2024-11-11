<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EquipmentIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'q' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'description' => 'nullable|string',
            'equipment_type_id' => 'nullable|integer|exists:equipment_types,id',
        ];
    }
}
