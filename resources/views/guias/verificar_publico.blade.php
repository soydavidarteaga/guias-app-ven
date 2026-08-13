<!DOCTYPE html>
<html lang="es" data-textdirection="ltr">

<head>
    <!-- NO JAVASCRIPT -->
    <noscript>
        <meta http-equiv="refresh" content="0;url=https://sica.sunagro.gob.ve/dependencias">
    </noscript>

    <meta charset="utf-8">
    <!-- RESPONSIVO -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CANONICAL -->
    <link rel="canonical" href="https://sica.sunagro.gob.ve/login">

    <!-- DESCRIPTION -->
    <meta name="description"
        content="Ejecutar las políticas públicas relacionadas con el Sistema Nacional Integral Agroalimentario (SNIA), dictadas por el Ejecutivo Nacional, con objeto de garantizar la distribución justa y equitativa en materia de producción e importación de productos agroalimentarios en coordinación con los entes u órganos competentes, así como, asegurar la disponibilidad y acceso oportuno de alimentos inocuos, de calidad y cantidad suficiente a la población y al mercado nacional, contribuyendo con la seguridad y soberanía agroalimentaria del país.">

    <!-- KEYWORDS -->
    <meta name="Keywords" content="sunagro, Sistema Nacional Integral Agroalimentario, " />
    <meta name="theme-color" content="#4285f4" />

    <!-- CSRF TOKEN -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sunagro</title>

    <!-- WHATSAPP -->
    <meta property="og:image" content="https://sica.sunagro.gob.ve/img/sunagro_icon.webp" />
    <meta property="og:image:secure_url" content="https://sica.sunagro.gob.ve/img/sunagro_icon.webp" />
    <meta property="og:image:type" content="image/png" />
    <meta property="og:image:width" content="300" />
    <meta property="og:image:height" content="300" />

    <link rel="shortcut icon" type="image/x-icon" href="https://sica.sunagro.gob.ve/favicon.ico">

    <link href="https://sica.sunagro.gob.ve/css/sunagro-styles.min.css?v=1.0.4" rel="stylesheet" type="text/css">
    <link href="https://sica.sunagro.gob.ve/css/styles.css?v=1.0.4" rel="stylesheet" type="text/css">

    <style>
        [wire\:loading],
        [wire\:loading\.delay],
        [wire\:loading\.inline-block],
        [wire\:loading\.inline],
        [wire\:loading\.block],
        [wire\:loading\.flex],
        [wire\:loading\.table],
        [wire\:loading\.grid],
        [wire\:loading\.inline-flex] {
            display: none;
        }

        [wire\:loading\.delay\.shortest],
        [wire\:loading\.delay\.shorter],
        [wire\:loading\.delay\.short],
        [wire\:loading\.delay\.long],
        [wire\:loading\.delay\.longer],
        [wire\:loading\.delay\.longest] {
            display: none;
        }

        [wire\:offline] {
            display: none;
        }

        [wire\:dirty]:not(textarea):not(input):not(select) {
            display: none;
        }

        input:-webkit-autofill,
        select:-webkit-autofill,
        textarea:-webkit-autofill {
            animation-duration: 50000s;
            animation-name: livewireautofill;
        }

        @keyframes livewireautofill {
            from {}
        }
    </style>
</head>

<body style="background-color: #f8f9fa;">
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-12 col-md-10 mx-auto bg-white p-0 shadow-sm" style="max-width: 1100px;">
                        <!-- HEADER IMAGE (header-report.gif) -->
                        <div class="w-100">
                            <img src="https://sica.sunagro.gob.ve/img/header-report.gif" alt="Header SUNAGRO" class="w-100 d-block" onerror="this.src='/header-guia-publica.png'" />
                        </div>

                        <!-- 2. MAIN TITLE BANNER -->
                        <div class="text-white text-center font-weight-bold py-2 text-uppercase" style="background-color: #354A85; font-size: 14px; letter-spacing: 0.5px;">
                            CONSULTA DE DATOS DEL DESPACHO #{{ $guia->nro_guia }}
                        </div>

                        <!-- 3. TIPO TRANSACCIÓN BANNER -->
                        <div class="text-white text-center font-weight-bold py-1.5 text-uppercase" style="background-color: #F57C00; font-size: 12px;">
                            TIPO TRANSACCIÓN: DESPACHO
                        </div>

                        <!-- 4. SUMMARY DATA BAR GRID -->
                        <div class="row bg-light border-bottom text-center py-2 px-2 mx-0 font-weight-bold text-dark" style="font-size: 11px;">
                            <div class="col-6 col-md-3 py-1">
                                N GUÍA: <span class="text-black">{{ $guia->nro_guia }}</span>
                            </div>
                            <div class="col-6 col-md-3 py-1">
                                RECEPCIONADA: <span class="text-black">{{ $guia->estado === 'Completada' ? 'SI' : 'NO' }}</span>
                            </div>
                            <div class="col-6 col-md-3 py-1">
                                VENCE: <span class="text-black">{{ $guia->fecha_vencimiento->format('d-m-Y') }}</span>
                            </div>
                            <div class="col-6 col-md-3 py-1 text-truncate">
                                COG. SEGURIDAD: <span class="px-2 py-1 rounded text-primary font-mono" style="background: #90CAF9; color: #0D47A1; font-size: 10px;">{{ substr($guia->qr_hash, 0, 32) }}</span>
                            </div>
                        </div>

                        <!-- 5. APPROVAL BANNER -->
                        <div class="text-white text-center font-weight-bold py-1.5 text-uppercase" style="background-color: #00C853; font-size: 12px; letter-spacing: 0.5px;">
                            APROBADO POR SISTEMA EL {{ $guia->fecha_emision->format('15-07-2026 04:49:12 PM') }}
                        </div>

                        <!-- 6. EMPRESA ORIGEN & DESTINO GRID WITH TRANSPORTE -->
                        <div class="row mx-0 py-3 align-items-start">
                            <!-- LEFT COLUMN (EMPRESA ORIGEN + DATOS DEL TRANSPORTE) -->
                            <div class="col-12 col-md-5 pr-md-1 pl-md-3">
                                <!-- EMPRESA ORIGEN -->
                                <div class="text-white text-center font-weight-bold py-1.5 text-uppercase mb-0" style="background-color: #354A85; font-size: 12px;">
                                    EMPRESA ORIGEN
                                </div>
                                <table class="table table-sm mb-0" style="font-size: 11px; border-collapse: collapse;">
                                    <tbody>
                                        <tr style="background-color: #f4f4f4;">
                                            <td class="font-weight-bold text-right py-1 px-2 border-0" style="width: 25%;">CÓDIGO:</td>
                                            <td class="text-center py-1 px-2 border-0" style="width: 75%;">{{ $guia->empresaOrigen->codigo_sica ?? '715118' }}</td>
                                        </tr>
                                        <tr style="background-color: #ffffff;">
                                            <td class="font-weight-bold text-right py-1 px-2 border-0">RAZÓN:</td>
                                            <td class="text-center py-1 px-2 border-0 font-weight-bold">{{ $guia->empresaOrigen->razon_social }}</td>
                                        </tr>
                                        <tr style="background-color: #f4f4f4;">
                                            <td class="font-weight-bold text-right py-1 px-2 border-0">RIF:</td>
                                            <td class="text-center py-1 px-2 border-0">{{ $guia->empresaOrigen->rif }}</td>
                                        </tr>
                                        <tr style="background-color: #ffffff;">
                                            <td class="font-weight-bold text-right py-1 px-2 border-0">TIPO:</td>
                                            <td class="text-center py-1 px-2 border-0 text-uppercase">CENTRO RECEPCION PRODUCTO IMPORTADO</td>
                                        </tr>
                                        <tr style="background-color: #f4f4f4;">
                                            <td class="font-weight-bold text-right py-1 px-2 border-0">DIRECCIÓN:</td>
                                            <td class="text-center py-1 px-2 border-0 text-uppercase" style="font-size: 10px; line-height: 1.2;">{{ $guia->empresaOrigen->direccion }}</td>
                                        </tr>
                                        <tr style="background-color: #ffffff;">
                                            <td class="font-weight-bold text-right py-1 px-2 border-0">ESTADO:</td>
                                            <td class="text-center py-1 px-2 border-0 text-uppercase">{{ $guia->empresaOrigen->estado ?? 'CARABOBO' }}</td>
                                        </tr>
                                        <tr style="background-color: #f4f4f4;">
                                            <td class="font-weight-bold text-right py-1 px-2 border-0">MUNICIPIO:</td>
                                            <td class="text-center py-1 px-2 border-0">{{ $guia->empresaOrigen->ciudad ?? 'Valencia' }}</td>
                                        </tr>
                                        <tr style="background-color: #ffffff;">
                                            <td class="font-weight-bold text-right py-1 px-2 border-0">PARROQUIA:</td>
                                            <td class="text-center py-1 px-2 border-0">Urbana Rafael Urdaneta</td>
                                        </tr>
                                        <tr style="background-color: #f4f4f4;">
                                            <td class="font-weight-bold text-right py-1 px-2 border-0">CIUDAD:</td>
                                            <td class="text-center py-1 px-2 border-0 text-uppercase">{{ $guia->empresaOrigen->ciudad ?? 'VALENCIA' }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <!-- DATOS DEL TRANSPORTE (DIRECTLY UNDER EMPRESA ORIGEN IN LEFT COLUMN) -->
                                <div class="text-white text-center font-weight-bold py-1.5 text-uppercase mt-1 mb-0" style="background-color: #354A85; font-size: 12px;">
                                    DATOS DEL TRANSPORTE
                                </div>
                                <table class="table table-sm mb-0" style="font-size: 11px; border-collapse: collapse;">
                                    <tbody>
                                        <tr style="background-color: #f4f4f4;">
                                            <td class="font-weight-bold text-right py-1 px-2 border-0" style="width: 30%;">CONDUCTOR:</td>
                                            <td class="text-center py-1 px-2 border-0 text-uppercase" style="width: 70%;">
                                                [{{ $guia->conductor->cedula }}] - {{ $guia->conductor->nombre_completo }}
                                            </td>
                                        </tr>
                                        <tr style="background-color: #ffffff;">
                                            <td class="font-weight-bold text-right py-1 px-2 border-0">VEHÍCULO:</td>
                                            <td class="text-center py-1 px-2 border-0 text-uppercase">
                                                {{ $guia->vehiculo->tipo }} - {{ $guia->vehiculo->placa }} -
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- MIDDLE COLUMN (GREEN ARROW) -->
                            <div class="col-12 col-md-2 d-flex align-items-center justify-content-center py-3 py-md-0" style="min-height: 180px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="#00c853">
                                    <path d="M16 11H4v2h12v3l5-4-5-4v3z"/>
                                </svg>
                            </div>

                            <!-- RIGHT COLUMN (EMPRESA DESTINO) -->
                            <div class="col-12 col-md-5 pl-md-1 pr-md-3">
                                <div class="text-white text-center font-weight-bold py-1.5 text-uppercase mb-0" style="background-color: #354A85; font-size: 12px;">
                                    EMPRESA DESTINO
                                </div>
                                <table class="table table-sm mb-0" style="font-size: 11px; border-collapse: collapse;">
                                    <tbody>
                                        <tr style="background-color: #f4f4f4;">
                                            <td class="font-weight-bold text-right py-1 px-2 border-0" style="width: 25%;">CÓDIGO:</td>
                                            <td class="text-center py-1 px-2 border-0" style="width: 75%;">{{ $guia->empresaDestino->codigo_sica ?? '992357' }}</td>
                                        </tr>
                                        <tr style="background-color: #ffffff;">
                                            <td class="font-weight-bold text-right py-1 px-2 border-0">RAZÓN:</td>
                                            <td class="text-center py-1 px-2 border-0 font-weight-bold">{{ $guia->empresaDestino->razon_social }}</td>
                                        </tr>
                                        <tr style="background-color: #f4f4f4;">
                                            <td class="font-weight-bold text-right py-1 px-2 border-0">RIF:</td>
                                            <td class="text-center py-1 px-2 border-0">{{ $guia->empresaDestino->rif }}</td>
                                        </tr>
                                        <tr style="background-color: #ffffff;">
                                            <td class="font-weight-bold text-right py-1 px-2 border-0">TIPO:</td>
                                            <td class="text-center py-1 px-2 border-0 text-uppercase">AGROINDUSTRIA ABA</td>
                                        </tr>
                                        <tr style="background-color: #f4f4f4;">
                                            <td class="font-weight-bold text-right py-1 px-2 border-0">DIRECCIÓN:</td>
                                            <td class="text-center py-1 px-2 border-0 text-uppercase" style="font-size: 10px; line-height: 1.2;">{{ $guia->empresaDestino->direccion }}</td>
                                        </tr>
                                        <tr style="background-color: #ffffff;">
                                            <td class="font-weight-bold text-right py-1 px-2 border-0">ESTADO:</td>
                                            <td class="text-center py-1 px-2 border-0 text-uppercase">{{ $guia->empresaDestino->estado ?? 'CARABOBO' }}</td>
                                        </tr>
                                        <tr style="background-color: #f4f4f4;">
                                            <td class="font-weight-bold text-right py-1 px-2 border-0">MUNICIPIO:</td>
                                            <td class="text-center py-1 px-2 border-0">{{ $guia->empresaDestino->ciudad ?? 'Valencia' }}</td>
                                        </tr>
                                        <tr style="background-color: #ffffff;">
                                            <td class="font-weight-bold text-right py-1 px-2 border-0">PARROQUIA:</td>
                                            <td class="text-center py-1 px-2 border-0">Urbana Rafael Urdaneta</td>
                                        </tr>
                                        <tr style="background-color: #f4f4f4;">
                                            <td class="font-weight-bold text-right py-1 px-2 border-0">CIUDAD:</td>
                                            <td class="text-center py-1 px-2 border-0 text-uppercase">{{ $guia->empresaDestino->ciudad ?? 'VALENCIA' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- 8. OBSERVACIÓN GENERAL / WARNING BANNER -->
                        <div class="border-top border-bottom mt-2">
                            <div class="text-white text-center font-weight-bold py-1 text-uppercase" style="background-color: #F57C00; font-size: 12px;">
                                OBSERVACIÓN GENERAL DE LA GUÍA
                            </div>
                            <div class="p-3 text-center font-weight-bold text-uppercase" style="background-color: #FFF3E0; border-top: 1px solid #FFE0B2; border-bottom: 1px solid #FFE0B2; font-size: 11px; color: #E65100 !important;">
                                ⚠️ -ESTE CUADRO REFLEJA SOLO DATOS DE CONSULTA, LA IMPRESIÓN DEL MISMO NO ES VÁLIDO PARA MOVILIZAR LOS RUBROS- ⚠️
                            </div>
                        </div>

                        <!-- 9. RUBROS TABLE -->
                        <div>
                            <div class="text-white text-center font-weight-bold py-1.5 text-uppercase" style="background-color: #354A85; font-size: 12px;">
                                RUBROS
                            </div>
                            <table class="table table-sm table-striped text-center mb-0" style="font-size: 11px;">
                                <thead class="bg-light font-weight-bold">
                                    <tr>
                                        <th class="text-left pl-4 border-0">RUBRO</th>
                                        <th class="border-0">CANTIDAD</th>
                                        <th class="border-0">P.V</th>
                                        <th class="border-0">PRESENTACIÓN</th>
                                        <th class="border-0">MARCA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($guia->items as $item)
                                    <tr>
                                        <td class="text-left pl-4 font-weight-bold text-uppercase border-0">{{ $item->rubro->nombre }}</td>
                                        <td class="font-weight-bold font-mono border-0">{{ number_format($item->cantidad, 2) }} {{ $item->rubro->unidad_medida }}</td>
                                        <td class="font-mono border-0">${{ number_format($item->precio_unitario, 2) }}</td>
                                        <td class="text-uppercase text-muted border-0">{{ $item->rubro->presentacion ?? 'BULTO / SACO' }}</td>
                                        <td class="text-uppercase font-weight-bold border-0">GENÉRICA</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- FOOTER COPYRIGHT -->
                        <div class="bg-light text-center py-3 text-muted border-top" style="font-size: 10px;">
                            República Bolivariana de Venezuela &bull; Superintendencia Nacional de Gestión Agroalimentaria (SUNAGRO)
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
