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
            font-size: 9px;
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
            border-bottom: 0.5 solid #000;
            padding: 3px 5px;
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
        .border-0 { border: none !important; }
        
        .footer-signature {
            position: absolute;
            bottom: 0px;
            left: 0;
            width: 100%;
            text-align: center;
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
    } catch (\Throwable $e) {}
@endphp

@foreach($copias as $index => $copia)

<div class="page-container">

    <!-- SECCIÓN 0: BANNER HEADER INSTITUCIONAL + CÓDIGO QR -->
    <table class="border-0" style="margin-bottom: 2px;">
        <tr>
            <td style="width: 90%; vertical-align: start; padding: 0;" class="border-0">
                @if($headerBase64)
                    <img src="{{ $headerBase64 }}" style="width: 100%; height: auto; display: block;" />
                @endif
            </td>
            <td style="width: 10%; text-align: right; vertical-align: middle; padding: 0;" class="border-0">
                @if($qrBase64)
                    <img src="{{ $qrBase64 }}" style="width: 90px; height: 90px; display: block; margin-left: auto;" />
                @else
                    <img src="{{ $qrApiUrl }}" style="width: 90px; height: 90px; display: block; margin-left: auto;" />
                @endif
            </td>
        </tr>
    </table>

    <!-- TIPO DE COPIA Y HASH MONOSPACE -->
    <table class="border-0" style="margin-bottom: 3px;">
        <tr>
            <td style="text-align: left; font-weight: bold; font-size: 9.5px; padding: 0; text-transform: none;" class="border-0">
                {{ $copia['tipo'] }}
            </td>
            <td style="text-align: right; font-family: monospace; font-size: 8.5px; padding: 0;" class="border-0">
                {{ substr($guia->qr_hash, 0, 36) }}
            </td>
        </tr>
    </table>

    <!-- SECCIÓN 1: CABECERA PRINCIPAL Y FOLIO EN TABLA UNIFICADA -->
    <table>
        <tr class="bg-header">
            <td style="width: 75%; font-size: 10px; padding: 4px;">
                GUIA DE SEGUIMIENTO Y CONTROL DE PRODUCTOS ALIMENTICIOS TERMINADOS
            </td>
            <td style="width: 25%; font-size: 10px; padding: 4px;text-transform: none;">
                (1) Nro. GUIA
            </td>
        </tr>
        <tr>
            <td style="font-size: 7px; text-align: center; vertical-align: middle; padding: 4px 6px;height: 60px;">
                <div>EN CASO DE COMPROBARSE ALTERACIONES EN LOS DATOS O QUE LOS MISMOS SON FALSOS, LA GUÍA SERÁ ANULADA Y SE APLICARÁN LAS SANCIONES CORRESPONDIENTES</div>
                <div style="margin-top: 2px;">NOTA: Esta guía NO SUPRIME la existencia de otros documentos requeridos para la movilización de productos alimenticios (Permisos Sanitarios, Facturas, Recibos, Etc.)</div>
            </td>
            <td style="text-align: center; font-size: 18px; vertical-align: middle;">
                {{ $guia->nro_guia }}
            </td>
        </tr>
    </table>

    <!-- SECCIÓN 2: (4) DATOS DE LA EMPRESA QUE DESPACHA -->
    <table>
        <tr class="bg-header">
            <td colspan="3" style="width: 75%;">(4) DATOS DE LA EMPRESA QUE DESPACHA ({{ $guia->empresaOrigen->codigo_sica ?? '715118' }})</td>
            <td class="bg-sub-header" colspan="3" style="width: 25%;">(2) FECHA DE EMISIÓN</td>
        </tr>
        <tr class="bg-sub-header" style="font-size: 8px;">
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
        <tr class="bg-sub-header" style="font-size: 8px;">
            <td>ESTADO</td>
            <td>CIUDAD</td>
            <td>PARROQUIA</td>
            <td colspan="3" style="background: #fff;">{{ $guia->fecha_emision->format('h:i:s A') }}</td>
        </tr>
        <tr class="text-center">
            <td class="text-left">{{ $guia->empresaOrigen->estado ?? 'CARABOBO' }}</td>
            <td class="text-left">{{ $guia->empresaOrigen->ciudad ?? 'VALENCIA' }}</td>
            <td class="text-left">{{ $guia->empresaOrigen->parroquia ?? 'URBANA RAFAEL URDANETA' }}</td>
            <td colspan="3" class="bg-sub-header" style="font-size: 8px;">(3) FECHA DE VENCIMIENTO</td>
        </tr>
        <tr class="bg-sub-header" style="font-size: 8px;">
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
        <tr>
            <td class="bg-sub-header" style="width: 20%; font-size: 8px;">(8) DIRECCIÓN</td>
            <td colspan="5" class="text-left" style="font-size: 8px;">{{ $guia->empresaOrigen->direccion }}</td>
        </tr>
    </table>

    <!-- SECCIÓN 3: TABLA DE RUBROS / PRODUCTOS -->
    <table>
        <tr class="bg-sub-header" style="font-size: 8px;">
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
        <tr class="bg-sub-header" style="font-size: 8px;">
            <td colspan="5">OBSERVACIÓN</td>
        </tr>
        <tr>
            <td colspan="5" class="text-left" style="font-size: 8px;">SACOS 25KG</td>
        </tr>
    </table>

    <!-- SECCIÓN 4: DATOS DEL TRANSPORTE -->
    <table>
        <tr class="bg-header">
            <td colspan="3">DATOS DE TRANSPORTE</td>
        </tr>
        <tr class="text-center font-bold" style="font-size: 8px;">
            <td class="bg-sub-header" colspan="3" style="text-transform: none;">Datos del Transporte Registrados en el SICA</td>
        </tr>
        <tr class="bg-sub-header" style="font-size: 8px;">
            <td style="width: 45%;">CONDUCTOR</td>
            <td style="width: 35%;">VEHÍCULO</td>
            <td style="width: 20%;">ESTATUS</td>
        </tr>
        <tr class="text-center">
            <td class="text-left">{{ $guia->conductor->nombre_completo }} - {{ $guia->conductor->cedula }}</td>
            <td class="text-left">{{ $guia->vehiculo->tipo }} - {{ $guia->vehiculo->placa }} -</td>
            <td class="text-left">{{ $guia->vehiculo->estatus ?? 'OPERATIVO' }}</td>
        </tr>
        <tr class="bg-sub-header" style="font-size: 8px;">
            <td colspan="3">FACTURAS U ORDENES QUE SOPORTAN EL DESPACHO</td>
        </tr>
        <tr>
            <td colspan="3" class="text-left" style="font-size: 8px;">NE 1172 FACT. N 2683 PRECINTO 38468059/1804022</td>
        </tr>
    </table>

    <!-- SECCIÓN 5: (14) DATOS DE LA EMPRESA QUE RECIBE -->
    <table>
        <tr class="bg-header">
            <td colspan="3">(14) DATOS DE LA EMPRESA QUE RECIBE ({{ $guia->empresaDestino->codigo_sica ?? '636729' }})</td>
        </tr>
        <tr class="bg-sub-header" style="font-size: 8px;">
            <td style="width: 40%;">(15) RAZON SOCIAL</td>
            <td style="width: 25%;">(16) R.I.F / C.I</td>
            <td style="width: 35%;">(17) PERSONA AUTORIZADA</td>
        </tr>
        <tr class="text-center">
            <td class="text-left">{{ $guia->empresaDestino->razon_social }}</td>
            <td class="text-left">{{ $guia->empresaDestino->rif }}</td>
            <td class="text-left">{{ $guia->empresaDestino->persona_autorizada ?? 'JORGE CAMPOS' }}</td>
        </tr>
        <tr class="bg-sub-header" style="font-size: 8px;">
            <td>ESTADO</td>
            <td>CIUDAD</td>
            <td>PARROQUIA</td>
        </tr>
        <tr class="text-center">
            <td>{{ $guia->empresaDestino->estado ?? 'LARA' }}</td>
            <td>{{ $guia->empresaDestino->ciudad ?? 'BARQUISIMETO' }}</td>
            <td>{{ $guia->empresaDestino->parroquia ?? 'SANTA ROSA' }}</td>
        </tr>
        <tr>
            <td class="bg-sub-header" style="width: 20%; font-size: 8px;">TELÉFONOS</td>
            <td colspan="2" class="text-left">{{ $guia->empresaDestino->telefonos ?? '0414-5185687 0414-5185687' }}</td>
        </tr>
        <tr>
            <td class="bg-sub-header" style="width: 20%; font-size: 8px;">(18) DIRECCIÓN</td>
            <td colspan="2" class="text-left" style="font-size: 8px;">{{ $guia->empresaDestino->direccion }}</td>
        </tr>
    </table>

    <!-- SECCIÓN 6: PIE LEGAL Y FIRMAS ANCLADAS AL FINAL -->
    <div class="footer-signature">
        <div style="font-size: 7px; text-align: justify; margin-bottom: 6px; line-height: 1.15;">
            EL SUPERINTENDENTE NACIONAL DE GESTIÓN AGROALIMENTARIA, AUTORIZA EXPRESAMENTE AL TITULAR DE ESTA GUÍA DE MOVILIZACIÓN, EL TRASLADO DE LOS RUBROS DESCRITOS EN LA MISMA, DESDE EL SITIO DE ORIGEN HASTA SU DESTINO DENTRO DEL ÁMBITO DEL TERRITORIO NACIONAL, SEGÚN LO ESTABLECIDO EN LA RESOLUCIÓN D/M NRO 025-12 DE FECHA 14 DE JUNIO DE 2012, PUBLICADA EN GACETA NRO 39.949 DE FECHA 21 DE JUNIO DEL 2012. DEBE ANEXAR LA GUÍA DE DESPACHO.<br>
            <strong>NOTA: DE ESTE FORMATO O GUÍA EXISTEN UNA (01) COPIA BENEFICIARIO Y UNA (01) COPIA TRANSPORTE. DEBE SER SELLADA Y FIRMADA EN LAS ALCABALAS DURANTE EL TRÁNSITO. LOS DATOS DE ORIGEN Y DESTINO DEBEN CORRESPONDER CON LOS DATOS DE LA FACTURA O NOTA DE ENTREGA.</strong>
        </div>

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
