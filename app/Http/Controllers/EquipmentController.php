<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentType;
use Illuminate\Http\Request;
use App\Http\Resources\EquipmentResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipment::with('equipmentType');

        if ($search = $request->query('q')) {
            $query->where('serial_number', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        return EquipmentResource::collection($query->paginate(2));
    }

    public function show($id)
    {
        $equipment = Equipment::with('equipmentType')->findOrFail($id);
        return new EquipmentResource($equipment);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'equipment_type_id' => 'required|exists:equipment_types,id',
            'serial_number' => 'required|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        $validatedData = $validator->validated();

        Log::info('Валидированные данные:', $validatedData);

        $equipmentType = EquipmentType::find($request->equipment_type_id);

        if (!$this->validateSerialNumber($request->serial_number, $equipmentType->serial_mask)) {
            return response()->json(['errors' => ['Invalid serial number format for selected equipment type.']], 422);
        }
        try {
            $equipment = Equipment::create($validatedData);
        } catch (\Exception $e) {
            Log::error('Ошибка при создании записи Equipment:', ['exception' => $e->getMessage()]);
            return response()->json(['errors' => ['Ошибка при создании записи.']], 500);
        }
        return new EquipmentResource($equipment);
    }


    public function update(Request $request, $id)
    {
        $equipment = Equipment::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'equipment_type_id' => 'exists:equipment_types,id',
            'serial_number' => 'string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        $data = $request->only(['equipment_type_id', 'serial_number', 'description']);

        if (isset($data['serial_number']) && isset($data['equipment_type_id'])) {
            $equipmentType = EquipmentType::find($data['equipment_type_id']);

            if (!$this->validateSerialNumber($data['serial_number'], $equipmentType->serial_mask)) {
                return response()->json(['errors' => ['Invalid serial number format for selected equipment type.']], 422);
            }
        }

        $equipment->update($data);
        return new EquipmentResource($equipment);
    }

    public function destroy($id)
    {
        $equipment = Equipment::findOrFail($id); 

        if ($equipment) {
            $equipment->delete(); 
            Log::info("Оборудование с id $id удалено.");
            return response()->noContent();
        }

        return response()->json(['message' => 'Оборудование не найдено'], 404);
    }



    private function validateSerialNumber($serialNumber, $mask)
    {
        $pattern = $this->convertMaskToRegex($mask);
        return preg_match($pattern, $serialNumber);
    }

    private function convertMaskToRegex($mask)
    {
        $pattern = '/^';
        for ($i = 0; $i < strlen($mask); $i++) {
            switch ($mask[$i]) {
                case 'N':
                    $pattern .= '[0-9]';
                    break;
                case 'A':
                    $pattern .= '[A-Z]';
                    break;
                case 'a':
                    $pattern .= '[a-z]';
                    break;
                case 'X':
                    $pattern .= '[A-Z0-9]';
                    break;
                case 'Z':
                    $pattern .= '[-_@]';
                    break;
                default:
                    $pattern .= $mask[$i];
                    break;
            }
        }
        return $pattern . '$/';
    }
}
