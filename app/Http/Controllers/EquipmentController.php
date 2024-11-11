<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentType;
use Illuminate\Http\Request;
use App\Http\Resources\EquipmentResource;
use App\Http\Requests\UpdateEquipmentRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class EquipmentController extends Controller
{
    /**
     * Отображает список оборудования с возможностью фильтрации по параметрам.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = Equipment::with('equipmentType');

        if ($search = $request->query('q')) {
            $query->where('serial_number', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        if ($serialNumber = $request->query('serial_number')) {
            $query->where('serial_number', 'like', "%{$serialNumber}%");
        }

        if ($description = $request->query('description')) {
            $query->where('description', 'like', "%{$description}%");
        }

        if ($equipmentTypeId = $request->query('equipment_type_id')) {
            $query->where('equipment_type_id', $equipmentTypeId);
        }

        return EquipmentResource::collection($query->paginate(2));
    }

    /**
     * Отображает информацию о конкретном оборудовании по его ID.
     *
     * @param int $id
     * @return EquipmentResource
     */
    public function show($id)
    {
        $equipment = Equipment::with('equipmentType')->findOrFail($id);
        return new EquipmentResource($equipment);
    }

    /**
     * Создаёт новое оборудование на основе переданных данных.
     *
     * @param Request $request
     * @return EquipmentResource|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'equipment.*.equipment_type_id' => 'required|exists:equipment_types,id',
            'equipment.*.serial_number' => 'required|string',
            'equipment.*.description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        $validatedData = $validator->validated();

        $createdEquipment = [];

        foreach ($validatedData['equipment'] as $item) {
            $equipmentType = EquipmentType::find($item['equipment_type_id']);

            if (!$this->validateSerialNumber($item['serial_number'], $equipmentType->serial_mask)) {
                return response()->json(['errors' => ['Неверный формат серийного номера для типа оборудования ID ' . $item['equipment_type_id']]], 422);
            }
        }

        try {
            $createdEquipment = Equipment::insert($validatedData['equipment']);
        } catch (\Exception $e) {
            Log::error('Ошибка при массовом создании записей Equipment:', ['exception' => $e->getMessage()]);
            return response()->json(['errors' => ['Ошибка при создании записей.']], 500);
        }

        return response()->json(['data' => $createdEquipment], 201);
    }


    /**
     * Обновляет информацию о существующем оборудовании по его ID.
     *
     * @param UpdateEquipmentRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateEquipmentRequest $request, $id)
    {
        $equipment = Equipment::findOrFail($id);
        $equipment->update($request->validated());

        return response()->json(['data' => $equipment]);
    }

    /**
     * Удаляет оборудование по его ID.
     *
     * @param int $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
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

    /**
     * Проверяет соответствие серийного номера заданной маске.
     *
     * @param string $serialNumber
     * @param string $mask
     * @return bool
     */
    private function validateSerialNumber($serialNumber, $mask)
    {
        $pattern = $this->convertMaskToRegex($mask);
        return preg_match($pattern, $serialNumber);
    }

    /**
     * Конвертирует маску серийного номера в регулярное выражение.
     *
     * @param string $mask
     * @return string
     */
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
