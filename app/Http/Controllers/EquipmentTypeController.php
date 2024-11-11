<?php

namespace App\Http\Controllers;

use App\Models\EquipmentType;
use Illuminate\Http\Request;
use App\Http\Resources\EquipmentTypeResource;

class EquipmentTypeController extends Controller
{
    /**
     * Отображает список типов оборудования с возможностью фильтрации по параметрам.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $types = EquipmentType::query();

        if ($search = $request->query('q')) {
            $types->where('name', 'like', "%{$search}%");
        }

        return EquipmentTypeResource::collection($types->paginate(10));
    }

    /**
     * Отображает информацию о конкретном типе оборудования по его ID.
     *
     * @param int $id
     * @return EquipmentTypeResource
     */
    public function show($id)
    {
        $type = EquipmentType::findOrFail($id);
        return new EquipmentTypeResource($type);
    }

    /**
     * Создаёт новый тип оборудования на основе переданных данных.
     *
     * @param Request $request
     * @return EquipmentTypeResource
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'serial_mask' => 'required|string',
        ]);

        $type = EquipmentType::create($validated);
        return new EquipmentTypeResource($type);
    }

    /**
     * Обновляет информацию о существующем типе оборудования по его ID.
     *
     * @param Request $request
     * @param int $id
     * @return EquipmentTypeResource
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $type = EquipmentType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'serial_mask' => 'string',
        ]);

        $type->update($validated);
        return new EquipmentTypeResource($type);
    }

    /**
     * Удаляет тип оборудования по его ID.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $type = EquipmentType::findOrFail($id);
        $type->delete();
        return response()->noContent();
    }
}
