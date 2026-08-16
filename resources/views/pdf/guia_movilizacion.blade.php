<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Guía de Movilización SICA - {{ $guia->nro_guia }}</title>
    <style>
        @page {
            margin: 12px 16px 12px 16px;
            size: letter portrait;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8px;
            color: #000;
            line-height: 1.2;
            text-transform: uppercase;
            margin: 0;
            padding: 0;
        }
        .page-break {
            page-break-after: always;
        }
        .page-container {
            position: relative;
            width: 100%;
            height: 980px; /* Expande al alto completo de hoja Carta */
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 2.5px 4.5px;
            vertical-align: middle;
        }
        .bg-header {
            background-color: rgb(108, 120, 115);
            color: #000;
            font-weight: bold;
            text-align: center;
        }
        .bg-sub-header {
            background-color: rgb(185, 191, 187);
            color: #000;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .border-0, table.border-0, table.border-0 td, table.border-0 th, tr.border-0 td, td.border-0 { border-top: none !important; }
        .border-none, table.border-none, table.border-none td, table.border-none th, tr.border-none td, td.border-none { border: none !important; }
        
        .footer-signature {
            position: absolute;
            left: 0;
            width: 100%;
            text-align: center;
        }
        .without-border, .without-border td, .without-border th, tr.without-border td, tr.without-border th {
            border-top: none !important;
        }
    </style>
</head>
<body>

@php
    $copias = [
        ['tipo' => 'Copia 01 Beneficiario'],
        ['tipo' => 'Copia 02 Transporte'],
    ];

    $headerImgPath = public_path('header-guia-pdf.png');
    $headerBase64 = file_exists($headerImgPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerImgPath))
        : '';

    $pieImgPath = public_path('pie-report.png');
    $pieBase64 = file_exists($pieImgPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($pieImgPath))
        : '';

    $targetPublicUrl = url('/guias/'.$guia->qr_hash);
    $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($targetPublicUrl);

    $qrBase64 = '';
    try {
        $arrContextOptions = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ];
        $qrRawContent = @file_get_contents($qrApiUrl, false, stream_context_create($arrContextOptions));
        if ($qrRawContent) {
            $qrBase64 = 'data:image/png;base64,' . base64_encode($qrRawContent);
        }
    } catch (\Throwable $e) {
        $qrBase64 = '';
    }
@endphp

@foreach($copias as $index => $copia)

<div class="page-container">

    <!-- SECCIÓN 0: BANNER HEADER INSTITUCIONAL + CÓDIGO QR -->
    <table class="border-none" style="border:0!important;margin-bottom: 2px;">
        <tr>
            <td style="width: 85%; vertical-align: start; padding: 0;" class="border-0">
                @if($headerBase64)
                    <img src="{{ $headerBase64 }}" style="width: 100%; height: auto; display: block;" />
                @endif
            </td>
            <td style="width: 15%; text-align: right; vertical-align: middle; padding: 5px;" class="border-0">
                @if($qrBase64)
                    <img src="{{ $qrBase64 }}" style="width: 120px; height: 120px; display: block; margin-left: auto;" />
                @else
                    <img src="{{ $qrApiUrl }}" style="width: 120px; height: 120px; display: block; margin-left: auto;" />
                @endif
            </td>
        </tr>
    </table>

    <!-- TIPO DE COPIA Y HASH MONOSPACE -->
    <table class="border-none" style="border:0!important;margin-bottom: 3px;">
        <tr>
            <td style="text-align: left; font-weight: bold; font-size: 8.5px; padding: 0; text-transform: none;" class="border-0">
                {{ $copia['tipo'] }}
            </td>
            <td style="text-align: right; font-size: 6.5px; font-family: Arial, Helvetica, sans-serif; font-weight: bold; padding: 0px; text-transform: none;" class="border-0">
                {{ substr($guia->qr_hash, 0, 30) }}
            </td>
        </tr>
    </table>

    <!-- SECCIÓN 1: CABECERA PRINCIPAL Y FOLIO EN TABLA UNIFICADA -->
    <table>
        <tr class="bg-header">
            <td style="width: 75%; font-size: 9px; padding: 3px;">
                GUIA DE SEGUIMIENTO Y CONTROL DE PRODUCTOS ALIMENTICIOS TERMINADOS
            </td>
            <td style="width: 25%; font-size: 9px; padding: 3px;text-transform: none;">
                (1) Nro. GUIA
            </td>
        </tr>
        <tr>
            <td style="font-size: 7.5px; text-align: center; margin: 0; height: 60px; line-height: 1.8;">
                <div>EN CASO DE COMPROBARSE ALTERACIONES EN LOS DATOS O QUE LOS MISMOS SON FALSOS, LA GUÍA SERÁ ANULADA Y SE APLICARÁN LAS SANCIONES CORRESPONDIENTES</div>
                <div style="margin-top: 2px; padding-bottom: 6px; text-transform: none;">NOTA: Esta guía NO SUPRIME la existencia de otros documentos requeridos para la movilización de productos alimenticios (Permisos Sanitarios, Facturas, Recibos, Etc.)</div>
            </td>
            <td style="text-align: center; font-size: 14px; vertical-align: middle;">
                {{ $guia->nro_guia }}
            </td>
        </tr>
    </table>

    <!-- SECCIÓN 2: (4) DATOS DE LA EMPRESA QUE DESPACHA -->
    <table>
        <tr class="bg-header without-border">
            <td colspan="3" style="width: 75%;">(4) DATOS DE LA EMPRESA QUE DESPACHA ({{ $guia->empresaOrigen->codigo_sica ?? '715118' }})</td>
            <td class="bg-sub-header" colspan="3" style="width: 25%;">(2) FECHA DE EMISIÓN</td>
        </tr>
        <tr class="bg-sub-header" style="font-size: 7.5px;">
            <td style="width: 35%;">(5) RAZÓN SOCIAL</td>
            <td style="width: 18%;">(6) R.I.F / C.I</td>
            <td style="width: 22%;">(7) PERSONA AUTORIZADA</td>
            <td style="width: 8%;">DÍA</td>
            <td style="width: 8%;">MES</td>
            <td style="width: 9%;">AÑO</td>
        </tr>
        <tr class="text-center">
            <td class="text-left">{{ $guia->empresaOrigen->razon_social }}</td>
            <td class="text-left">{{ $guia->empresaOrigen->rif }}</td>
            <td class="text-left">{{ $guia->empresaOrigen->persona_autorizada ?? 'ANTONIO GRATEROL' }}</td>
            <td>{{ $guia->fecha_emision->format('d') }}</td>
            <td>{{ $guia->fecha_emision->format('m') }}</td>
            <td>{{ $guia->fecha_emision->format('Y') }}</td>
        </tr>
        <tr class="bg-sub-header" style="font-size: 7.5px;">
            <td>ESTADO</td>
            <td>CIUDAD</td>
            <td>PARROQUIA</td>
            <td colspan="3" style="background: #fff; font-weight: normal;">{{ $guia->fecha_emision->format('h:i:s A') }}</td>
        </tr>
        <tr class="text-center">
            <td class="text-left">{{ $guia->empresaOrigen->estado ?? 'CARABOBO' }}</td>
            <td class="text-left">{{ $guia->empresaOrigen->ciudad ?? 'VALENCIA' }}</td>
            <td class="text-left">{{ $guia->empresaOrigen->parroquia ?? 'URBANA RAFAEL URDANETA' }}</td>
            <td colspan="3" class="bg-sub-header" style="font-size: 7.5px;">(3) FECHA DE VENCIMIENTO</td>
        </tr>
        <tr class="bg-sub-header" style="font-size: 7.5px;">
            <td colspan="3">TELÉFONOS</td>
            <td>DÍA</td>
            <td>MES</td>
            <td>AÑO</td>
        </tr>
        <tr class="text-center">
            <td colspan="3">{{ $guia->empresaOrigen->telefonos ?? '0424-2610767 0424-2610767' }}</td>
            <td>{{ $guia->fecha_vencimiento->format('d') }}</td>
            <td>{{ $guia->fecha_vencimiento->format('m') }}</td>
            <td>{{ $guia->fecha_vencimiento->format('Y') }}</td>
        </tr>
    </table>
    <table class="border-0">
        <tr>
            <td class="bg-sub-header" style="width: 20%; font-size: 7.5px;">(8) DIRECCIÓN</td>
            <td colspan="5" class="text-left" style="width: 80%; font-size: 7.5px;">{{ $guia->empresaOrigen->direccion }}</td>
        </tr>
    </table>

    <!-- SECCIÓN 3: TABLA DE RUBROS / PRODUCTOS -->
    <table class="border-0">
        <tr class="bg-sub-header" style="font-size: 7.5px;">
            <td style="width: 40%;">(10) RUBROS</td>
            <td style="width: 15%;">(11) CANT(TN)</td>
            <td style="width: 15%;">CODIGO ARANCELARIO</td>
            <td style="width: 15%;">(12) PRECIO(BS)</td>
            <td style="width: 15%;">(13) PRESENTACIÓN</td>
        </tr>
        @foreach($guia->items as $item)
        <tr class="text-center">
            <td class="text-left">{{ $item->rubro->nombre }}</td>
            <td class="text-right">{{ number_format($item->cantidad, 3) }}</td>
            <td>{{ $item->rubro->codigo_arancelario ?? '00000000' }}</td>
            <td class="text-right">{{ number_format($item->precio_unitario, 2) }}</td>
            <td class="text-left">{{ $item->rubro->presentacion ?? 'OTROS' }}</td>
        </tr>
        @endforeach
        <tr class="bg-sub-header" style="font-size: 7.5px;">
            <td colspan="5">OBSERVACIÓN</td>
        </tr>
        <tr>
            <td colspan="5" class="text-left" style="font-size: 7.5px;">{{ $guia->observacion ?? 'SACOS 25KG' }}</td>
        </tr>
    </table>

    <!-- SECCIÓN 4: DATOS DEL TRANSPORTE -->
    <table class="border-0" style="margin-bottom: 12px;">
        <tr class="bg-header">
            <td colspan="3">DATOS DE TRANSPORTE</td>
        </tr>
        <tr class="text-center font-bold" style="font-size: 7.5px;">
            <td class="bg-sub-header" colspan="3" style="text-transform: none;">Datos del Transporte Registrados en el SICA</td>
        </tr>
        <tr class="bg-sub-header" style="font-size: 7.5px;">
            <td style="width: 45%;">CONDUCTOR</td>
            <td style="width: 35%;">VEHÍCULO</td>
            <td style="width: 20%;">ESTATUS</td>
        </tr>
        <tr class="text-center">
            <td class="text-left">{{ $guia->conductor->nombre_completo }} - {{ $guia->conductor->cedula }}</td>
            <td class="text-left">{{ $guia->vehiculo->tipo }} - {{ $guia->vehiculo->placa }} -</td>
            <td class="text-left">{{ $guia->vehiculo->estatus ?? 'OPERATIVO' }}</td>
        </tr>
        <tr class="bg-sub-header" style="font-size: 7.5px;">
            <td colspan="3">FACTURAS U ORDENES QUE SOPORTAN EL DESPACHO</td>
        </tr>
        <tr>
            <td colspan="3" class="text-left" style="font-size: 7.5px;">{{ $guia->documentos_soporte ?? 'NE 1172 FACT. N 2683 PRECINTO 38468059/1804022' }}</td>
        </tr>
    </table>

    <!-- SECCIÓN 5: (14) DATOS DE LA EMPRESA QUE RECIBE -->
    <table>
        <tr class="bg-header">
            <td colspan="3">(14) DATOS DE LA EMPRESA QUE RECIBE ({{ $guia->empresaDestino->codigo_sica ?? '636729' }})</td>
        </tr>
        <tr class="bg-sub-header" style="font-size: 7.5px;">
            <td>(15) RAZON SOCIAL</td>
            <td>(16) R.I.F / C.I</td>
            <td>(17) PERSONA AUTORIZADA</td>
        </tr>
        <tr class="text-center">
            <td class="text-left">{{ $guia->empresaDestino->razon_social }}</td>
            <td class="text-left">{{ $guia->empresaDestino->rif }}</td>
            <td class="text-left">{{ $guia->empresaDestino->persona_autorizada ?? 'JORGE CAMPOS' }}</td>
        </tr>
    </table>
    <table class="border-0">
        <tr class="bg-sub-header" style="font-size: 7.5px;">
            <td>ESTADO</td>
            <td>CIUDAD</td>
            <td>PARROQUIA</td>
        </tr>
        <tr class="text-center">
            <td>{{ $guia->empresaDestino->estado ?? 'LARA' }}</td>
            <td>{{ $guia->empresaDestino->ciudad ?? 'BARQUISIMETO' }}</td>
            <td>{{ $guia->empresaDestino->parroquia ?? 'SANTA ROSA' }}</td>
        </tr>
    </table>
    <table class="border-0">
        <tr>
            <td class="bg-sub-header" style="font-size: 7.5px; font-weight: bold; text-align: center; width: 20%;">TELÉFONOS</td>
            <td colspan="2" class="text-left" style="font-size: 7.5px; width: 80%;">{{ $guia->empresaDestino->telefonos ?? '0414-5185687 0414-5185687' }}</td>
        </tr>
        <tr>
            <td class="bg-sub-header" style="font-size: 7.5px; font-weight: bold; text-align: center; width: 20%;">(18) DIRECCIÓN</td>
            <td colspan="2" class="text-left" style="font-size: 7.5px; width: 80%;">{{ $guia->empresaDestino->direccion }}</td>
        </tr>
    </table>

    <!-- SECCIÓN 6: PIE LEGAL Y FIRMAS ANCLADAS AL FINAL -->
    <div class="footer-signature" style="position: absolute;">
        <div style="font-size: 6.5px; text-align: justify; margin-bottom: 6px; line-height: 1.8;">
            EL SUPERINTENDENTE NACIONAL DE GESTIÓN AGROALIMENTARIA, AUTORIZA EXPRESAMENTE AL TITULAR DE ESTA GUÍA DE MOVILIZACIÓN, EL TRASLADO DE LOS RUBROS DESCRITOS EN LA MISMA, DESDE EL SITIO DE ORIGEN HASTA SU DESTINO DENTRO DEL ÁMBITO DEL TERRITORIO NACIONAL, SEGÚN LO ESTABLECIDO EN LA RESOLUCIÓN D/M NRO 025-12 DE FECHA 14 DE JUNIO DE 2012, PUBLICADA EN GACETA NRO 39.949 DE FECHA 21 DE JUNIO DEL 2012. DEBE ANEXAR LA GUÍA DE DESPACHO.<br>
            <strong>NOTA: DE ESTE FORMATO O GUÍA EXISTEN UNA (01) COPIA BENEFICIARIO Y UNA (01) COPIA TRANSPORTE. DEBE SER SELLADA Y FIRMADA EN LAS ALCABALAS DURANTE EL TRÁNSITO. LOS DATOS DE ORIGEN Y DESTINO DEBEN CORRESPONDER CON LOS DATOS DE LA FACTURA O NOTA DE ENTREGA.</strong>
        </div>
    </div>
    <div style="margin-top: 90px;">
        @if($pieBase64)
            <div style="text-align: center;">
                <img src="{{ $pieBase64 }}" style="width: 100%; height: auto; max-height: 300px; display: block; margin: 0 auto;" />
            </div>
        @endif
    </div>

</div>

@if($index === 0)
    <div class="page-break"></div>
@endif

@endforeach

</body>
</html>
