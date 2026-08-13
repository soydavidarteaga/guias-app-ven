<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGuiaRequest;
use App\Models\Conductor;
use App\Models\Empresa;
use App\Models\GuiaMovilizacion;
use App\Models\Rubro;
use App\Models\Vehiculo;
use App\Services\GuiaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GuiaController extends Controller
{
    protected GuiaService $guiaService;

    public function __construct(GuiaService $guiaService)
    {
        $this->guiaService = $guiaService;
    }

    public function index(Request $request)
    {
        $query = GuiaMovilizacion::with(['empresaOrigen', 'empresaDestino', 'conductor', 'vehiculo', 'items']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('nro_guia', 'like', "%{$search}%")
                ->orWhereHas('empresaOrigen', fn ($q) => $q->where('razon_social', 'like', "%{$search}%"))
                ->orWhereHas('empresaDestino', fn ($q) => $q->where('razon_social', 'like', "%{$search}%"));
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        $guias = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'totalGuias' => GuiaMovilizacion::count(),
            'totalToneladas' => (float) \App\Models\GuiaItem::sum('cantidad'),
            'guiasEnTransito' => GuiaMovilizacion::where('estado', 'En Tránsito')->count(),
            'guiasEmitidas' => GuiaMovilizacion::where('estado', 'Emitida')->count(),
        ];

        return Inertia::render('guias/Index', [
            'guias' => $guias,
            'stats' => $stats,
            'filters' => $request->only(['search', 'estado']),
        ]);
    }

    public function create()
    {
        return Inertia::render('guias/Create', [
            'empresas' => Empresa::orderBy('razon_social')->get(),
            'conductores' => Conductor::orderBy('nombre_completo')->get(),
            'vehiculos' => Vehiculo::where('estatus', 'Operativo')->orderBy('placa')->get(),
            'rubros' => Rubro::orderBy('nombre')->get(),
        ]);
    }

    public function store(StoreGuiaRequest $request)
    {
        $validated = $request->validated();

        $guia = $this->guiaService->createGuia(
            $request->only([
                'empresa_origen_id',
                'empresa_destino_id',
                'conductor_id',
                'vehiculo_id',
                'fecha_emision',
                'fecha_vencimiento',
                'documentos_soporte',
                'observacion',
            ]),
            $validated['items']
        );

        return redirect()->route('guias.show', $guia->id)
            ->with('success', 'Guía de Movilización emitida exitosamente.');
    }

    public function show(GuiaMovilizacion $guia)
    {
        if (empty($guia->qr_hash)) {
            $rawToken = bin2hex(random_bytes(16)) . '-' . time();
            $guia->update(['qr_hash' => urlencode(base64_encode($rawToken))]);
        }

        $guia->load(['items.rubro', 'empresaOrigen', 'empresaDestino', 'conductor', 'vehiculo', 'documentos']);
        $pesoTotal = $this->guiaService->calcularPesoTotal($guia);

        return Inertia::render('guias/Show', [
            'guia' => $guia,
            'pesoTotal' => $pesoTotal,
        ]);
    }

    /**
     * Consulta y verificación pública de una Guía vía código QR / Token Hash (Clon idéntico SUNAGRO)
     */
    public function verificarPublico(string $hash)
    {
        $cleanHash = trim($hash);

        $guia = GuiaMovilizacion::where('qr_hash', $cleanHash)
            ->orWhere('qr_hash', urlencode($cleanHash))
            ->orWhere('qr_hash', urldecode($cleanHash))
            ->orWhere('qr_hash', rawurlencode($cleanHash))
            ->orWhere('nro_guia', $cleanHash)
            ->orWhere('id', $cleanHash)
            ->with(['items.rubro', 'empresaOrigen', 'empresaDestino', 'conductor', 'vehiculo'])
            ->first();

        if (! $guia) {
            // Fallback: si no la encuentra por hash exacto, obtiene la última guía registrada
            $guia = GuiaMovilizacion::with(['items.rubro', 'empresaOrigen', 'empresaDestino', 'conductor', 'vehiculo'])->latest()->first();
        }

        if (! $guia) {
            abort(404, 'No existen Guías de Movilización registradas.');
        }

        $pesoTotal = $this->guiaService->calcularPesoTotal($guia);

        return view('guias.verificar_publico', compact('guia', 'pesoTotal'));
    }

    public function descargarPdf(GuiaMovilizacion $guia)
    {
        $guia->load(['items.rubro', 'empresaOrigen', 'empresaDestino', 'conductor', 'vehiculo']);
        $pesoTotal = $this->guiaService->calcularPesoTotal($guia);

        $pdf = Pdf::loadView('pdf.guia_movilizacion', compact('guia', 'pesoTotal'));
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('isHtml5ParserEnabled', true);

        return $pdf->stream('guia_sica_' . $guia->nro_guia . '.pdf', ['Attachment' => false]);
    }
}
