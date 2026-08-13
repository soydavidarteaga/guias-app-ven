<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmpresaRequest;
use App\Http\Requests\UpdateEmpresaRequest;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmpresaController extends Controller
{
    public function index(Request $request)
    {
        $query = Empresa::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('razon_social', 'like', "%{$search}%")
                  ->orWhere('rif', 'like', "%{$search}%")
                  ->orWhere('codigo_sica', 'like', "%{$search}%")
                  ->orWhere('ciudad', 'like', "%{$search}%")
                  ->orWhere('estado', 'like', "%{$search}%");
            });
        }

        $empresas = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('empresas/Index', [
            'empresas' => $empresas,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(StoreEmpresaRequest $request)
    {
        $empresa = Empresa::create($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Empresa registrada exitosamente.',
                'data' => $empresa
            ], 201);
        }

        return redirect()->back()->with('success', 'Empresa registrada exitosamente.');
    }

    public function update(UpdateEmpresaRequest $request, Empresa $empresa)
    {
        $empresa->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Empresa actualizada exitosamente.',
                'data' => $empresa
            ]);
        }

        return redirect()->back()->with('success', 'Empresa actualizada exitosamente.');
    }

    public function destroy(Empresa $empresa)
    {
        if ($empresa->guiasComoOrigen()->exists() || $empresa->guiasComoDestino()->exists()) {
            if (request()->wantsJson()) {
                return response()->json([
                    'error' => 'No se puede eliminar la empresa porque tiene Guías de Movilización asociadas.'
                ], 422);
            }
            return redirect()->back()->withErrors([
                'error' => 'No se puede eliminar la empresa porque tiene Guías de Movilización asociadas.'
            ]);
        }

        $empresa->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Empresa eliminada exitosamente.']);
        }

        return redirect()->back()->with('success', 'Empresa eliminada exitosamente.');
    }
}
