<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConductorRequest;
use App\Http\Requests\UpdateConductorRequest;
use App\Models\Conductor;
use App\Models\GuiaMovilizacion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConductorController extends Controller
{
    public function index(Request $request)
    {
        $query = Conductor::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre_completo', 'like', "%{$search}%")
                  ->orWhere('cedula', 'like', "%{$search}%")
                  ->orWhere('telefono', 'like', "%{$search}%");
            });
        }

        $conductores = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('conductores/Index', [
            'conductores' => $conductores,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(StoreConductorRequest $request)
    {
        $conductor = Conductor::create($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Conductor registrado exitosamente.',
                'data' => $conductor
            ], 201);
        }

        return redirect()->back()->with('success', 'Conductor registrado exitosamente.');
    }

    public function update(UpdateConductorRequest $request, Conductor $conductor)
    {
        $conductor->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Conductor actualizado exitosamente.',
                'data' => $conductor
            ]);
        }

        return redirect()->back()->with('success', 'Conductor actualizado exitosamente.');
    }

    public function destroy(Conductor $conductor)
    {
        if (GuiaMovilizacion::where('conductor_id', $conductor->id)->exists()) {
            if (request()->wantsJson()) {
                return response()->json([
                    'error' => 'No se puede eliminar el conductor porque tiene Guías de Movilización asociadas.'
                ], 422);
            }
            return redirect()->back()->withErrors([
                'error' => 'No se puede eliminar el conductor porque tiene Guías de Movilización asociadas.'
            ]);
        }

        $conductor->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Conductor eliminado exitosamente.']);
        }

        return redirect()->back()->with('success', 'Conductor eliminado exitosamente.');
    }
}
