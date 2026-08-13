<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehiculoRequest;
use App\Http\Requests\UpdateVehiculoRequest;
use App\Models\GuiaMovilizacion;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VehiculoController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehiculo::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('tipo', 'like', "%{$search}%")
                  ->orWhere('placa', 'like', "%{$search}%")
                  ->orWhere('estatus', 'like', "%{$search}%");
            });
        }

        $vehiculos = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('vehiculos/Index', [
            'vehiculos' => $vehiculos,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(StoreVehiculoRequest $request)
    {
        $vehiculo = Vehiculo::create($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Vehículo registrado exitosamente.',
                'data' => $vehiculo
            ], 201);
        }

        return redirect()->back()->with('success', 'Vehículo registrado exitosamente.');
    }

    public function update(UpdateVehiculoRequest $request, Vehiculo $vehiculo)
    {
        $vehiculo->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Vehículo actualizado exitosamente.',
                'data' => $vehiculo
            ]);
        }

        return redirect()->back()->with('success', 'Vehículo actualizado exitosamente.');
    }

    public function destroy(Vehiculo $vehiculo)
    {
        if (GuiaMovilizacion::where('vehiculo_id', $vehiculo->id)->exists()) {
            if (request()->wantsJson()) {
                return response()->json([
                    'error' => 'No se puede eliminar el vehículo porque tiene Guías de Movilización asociadas.'
                ], 422);
            }
            return redirect()->back()->withErrors([
                'error' => 'No se puede eliminar el vehículo porque tiene Guías de Movilización asociadas.'
            ]);
        }

        $vehiculo->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Vehículo eliminado exitosamente.']);
        }

        return redirect()->back()->with('success', 'Vehículo eliminado exitosamente.');
    }
}
