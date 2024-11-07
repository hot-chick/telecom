<?php

namespace App\Http\Controllers;

use App\Models\EquipmentType;
use Illuminate\Http\Request;
use App\Http\Resources\EquipmentTypeResource;

class EquipmentTypeController extends Controller
{
    public function index(Request $request)
    {
        $types = EquipmentType::query();

        if ($search = $request->query('q')) {
            $types->where('name', 'like', "%{$search}%");
        }

        return EquipmentTypeResource::collection($types->paginate(10));
    }

    public function show($id)
    {
        $type = EquipmentType::findOrFail($id);
        return new EquipmentTypeResource($type);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'serial_mask' => 'required|string',
        ]);

        $type = EquipmentType::create($validated);
        return new EquipmentTypeResource($type);
    }

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

    public function destroy($id)
    {
        $type = EquipmentType::findOrFail($id);
        $type->delete();
        return response()->noContent();
    }
}
