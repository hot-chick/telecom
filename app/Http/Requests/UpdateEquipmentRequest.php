<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\SerialNumberRule;
use App\Models\EquipmentType;

class UpdateEquipmentRequest extends FormRequest
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
    public function rules()
    {
        return [
            'equipment_type_id' => 'exists:equipment_types,id',
            'serial_number' => ['string', new SerialNumberRule($this->getSerialMask())],
            'description' => 'nullable|string',
        ];
    }

    /**
     * Получает маску серийного номера для проверки.
     *
     * @return string
     */

    protected function getSerialMask(): string
    {
        // Логика для получения маски на основе типа оборудования
        $equipmentType = EquipmentType::find($this->equipment_type_id);
        return $equipmentType ? $equipmentType->serial_mask : '';
    }
}
