<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRubroRequest;
use App\Http\Requests\UpdateRubroRequest;
use App\Models\GuiaItem;
use App\Models\Rubro;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RubroController extends Controller
{
    public function index(Request $request)
    {
        $query = Rubro::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo_arancelario', 'like', "%{$search}%")
                  ->orWhere('presentacion', 'like', "%{$search}%");
            });
        }

        $rubros = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('rubros/Index', [
            'rubros' => $rubros,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(StoreRubroRequest $request)
    {
        $rubro = Rubro::create($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Rubro registrado exitosamente.',
                'data' => $rubro
            ], 201);
        }

        return redirect()->back()->with('success', 'Rubro registrado exitosamente.');
    }

    public function update(UpdateRubroRequest $request, Rubro $rubro)
    {
        $rubro->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Rubro actualizado exitosamente.',
                'data' => $rubro
            ]);
        }

        return redirect()->back()->with('success', 'Rubro actualizado exitosamente.');
    }

    public function destroy(Rubro $rubro)
    {
        if (GuiaItem::where('rubro_id', $rubro->id)->exists()) {
            if (request()->wantsJson()) {
                return response()->json([
                    'error' => 'No se puede eliminar el rubro porque está asociado a Guías de Movilización emitidas.'
                ], 422);
            }
            return redirect()->back()->withErrors([
                'error' => 'No se puede eliminar el rubro porque está asociado a Guías de Movilización emitidas.'
            ]);
        }

        $rubro->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Rubro eliminado exitosamente.']);
        }

        return redirect()->back()->with('success', 'Rubro eliminado exitosamente.');
    }
}
