<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineOptions({
    layout: null,
});

interface Guia {
    id: number;
    nro_guia: string;
    fecha_emision: string;
    fecha_vencimiento: string;
    estado: string;
    qr_hash: string;
    empresa_origen: {
        razon_social: string;
        rif: string;
        codigo_sica: string;
        direccion: string;
        estado: string;
        ciudad: string;
        parroquia?: string;
    };
    empresa_destino: {
        razon_social: string;
        rif: string;
        codigo_sica: string;
        direccion: string;
        estado: string;
        ciudad: string;
        parroquia?: string;
    };
    conductor: { nombre_completo: string; cedula: string; telefono: string };
    vehiculo: { tipo: string; placa: string };
    items: Array<{
        id: number;
        cantidad: number;
        precio_unitario: number;
        observacion?: string;
        rubro: { nombre: string; presentacion: string; unidad_medida: string };
    }>;
}

const props = defineProps<{
    guia: Guia;
    pesoTotal: number;
    verificadoEn: string;
}>();

const formatDate = (dateString: string) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}-${month}-${year}`;
};

const formatDateTime = (dateString: string) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    let hours = date.getHours();
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const seconds = String(date.getSeconds()).padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    const strHours = String(hours).padStart(2, '0');
    return `${day}-${month}-${year} ${strHours}:${minutes}:${seconds} ${ampm}`;
};
</script>

<template>
    <Head :title="`Consulta de Datos del Despacho #${guia.nro_guia}`" />

    <div class="min-h-screen bg-white text-black font-sans p-2 md:p-6 flex justify-center text-xs">
        <div class="w-full max-w-5xl border border-gray-300 shadow-md bg-white">
            <!-- 1. HEADER IMAGE OFICIAL -->
            <div class="border-b border-gray-300">
                <img
                    src="/header-guia-publica.png"
                    alt="Header Guía Pública SUNAGRO"
                    class="w-full h-auto block"
                />
            </div>

            <!-- 2. MAIN TITLE BANNER -->
            <div class="bg-[#2A4580] text-white font-black text-center py-2 text-sm uppercase tracking-wide">
                CONSULTA DE DATOS DEL DESPACHO #{{ guia.nro_guia }}
            </div>

            <!-- 3. TIPO TRANSACCIÓN BANNER -->
            <div class="bg-[#F57C00] text-white font-bold text-center py-1.5 text-xs uppercase">
                TIPO TRANSACCIÓN: DESPACHO
            </div>

            <!-- 4. SUMMARY DATA BAR GRID -->
            <div class="grid grid-cols-2 md:grid-cols-4 bg-gray-100 border-b border-gray-300 text-center py-2 px-2 gap-2 text-[11px] font-semibold">
                <div>
                    N GUÍA: <span class="font-bold text-black">{{ guia.nro_guia }}</span>
                </div>
                <div>
                    RECEPCIONADA: <span class="font-bold text-black">{{ guia.estado === 'Completada' ? 'SI' : 'NO' }}</span>
                </div>
                <div>
                    VENCE: <span class="font-bold text-black">{{ formatDate(guia.fecha_vencimiento) }}</span>
                </div>
                <div class="truncate">
                    COG. SEGURIDAD: <span class="bg-[#90CAF9] px-1.5 py-0.5 rounded font-mono text-[10px] text-blue-900 font-bold">{{ guia.qr_hash.substring(0, 16) }}...</span>
                </div>
            </div>

            <!-- 5. APPROVAL BANNER -->
            <div class="bg-[#00C853] text-white font-bold text-center py-1.5 text-xs uppercase tracking-wider">
                APROBADO POR SISTEMA EL {{ formatDateTime(guia.fecha_emision) }}
            </div>

            <!-- 6. EMPRESA ORIGEN & DESTINO GRID -->
            <div class="relative">
                <!-- Green Arrow in center for desktop -->
                <div class="hidden md:flex absolute inset-0 items-center justify-center pointer-events-none z-10">
                    <div class="bg-emerald-500 text-white rounded-full p-2 shadow-md">
                        <span class="text-xl leading-none">➜</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-0 border-b border-gray-300">
                    <!-- EMPRESA ORIGEN -->
                    <div class="border-b md:border-b-0 md:border-r border-gray-300">
                        <div class="bg-[#2A4580] text-white font-bold text-center py-1.5 uppercase text-xs">
                            EMPRESA ORIGEN
                        </div>
                        <table class="w-full text-[11px] border-collapse">
                            <tbody>
                                <tr class="border-b border-gray-200">
                                    <td class="w-28 font-bold bg-gray-50 p-1.5 text-right uppercase border-r border-gray-200">CÓDIGO:</td>
                                    <td class="p-1.5 font-mono text-center">{{ guia.empresa_origen.codigo_sica || '715118' }}</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="font-bold bg-gray-50 p-1.5 text-right uppercase border-r border-gray-200">RAZÓN:</td>
                                    <td class="p-1.5 text-center font-semibold">{{ guia.empresa_origen.razon_social }}</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="font-bold bg-gray-50 p-1.5 text-right uppercase border-r border-gray-200">RIF:</td>
                                    <td class="p-1.5 font-mono text-center">{{ guia.empresa_origen.rif }}</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="font-bold bg-gray-50 p-1.5 text-right uppercase border-r border-gray-200">TIPO:</td>
                                    <td class="p-1.5 text-center text-gray-700">CENTRO RECEPCION PRODUCTO IMPORTADO Y NACIONAL</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="font-bold bg-gray-50 p-1.5 text-right uppercase border-r border-gray-200">DIRECCIÓN:</td>
                                    <td class="p-1.5 text-center uppercase text-[10px] leading-tight">{{ guia.empresa_origen.direccion }}</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="font-bold bg-gray-50 p-1.5 text-right uppercase border-r border-gray-200">ESTADO:</td>
                                    <td class="p-1.5 text-center uppercase font-semibold">{{ guia.empresa_origen.estado || 'CARABOBO' }}</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="font-bold bg-gray-50 p-1.5 text-right uppercase border-r border-gray-200">MUNICIPIO:</td>
                                    <td class="p-1.5 text-center uppercase">{{ guia.empresa_origen.ciudad || 'Valencia' }}</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="font-bold bg-gray-50 p-1.5 text-right uppercase border-r border-gray-200">PARROQUIA:</td>
                                    <td class="p-1.5 text-center uppercase">{{ guia.empresa_origen.parroquia || 'Urbana Rafael Urdaneta' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-bold bg-gray-50 p-1.5 text-right uppercase border-r border-gray-200">CIUDAD:</td>
                                    <td class="p-1.5 text-center uppercase font-semibold">{{ guia.empresa_origen.ciudad || 'VALENCIA' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- EMPRESA DESTINO -->
                    <div>
                        <div class="bg-[#2A4580] text-white font-bold text-center py-1.5 uppercase text-xs">
                            EMPRESA DESTINO
                        </div>
                        <table class="w-full text-[11px] border-collapse">
                            <tbody>
                                <tr class="border-b border-gray-200">
                                    <td class="w-28 font-bold bg-gray-50 p-1.5 text-right uppercase border-r border-gray-200">CÓDIGO:</td>
                                    <td class="p-1.5 font-mono text-center">{{ guia.empresa_destino.codigo_sica || '992357' }}</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="font-bold bg-gray-50 p-1.5 text-right uppercase border-r border-gray-200">RAZÓN:</td>
                                    <td class="p-1.5 text-center font-semibold">{{ guia.empresa_destino.razon_social }}</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="font-bold bg-gray-50 p-1.5 text-right uppercase border-r border-gray-200">RIF:</td>
                                    <td class="p-1.5 font-mono text-center">{{ guia.empresa_destino.rif }}</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="font-bold bg-gray-50 p-1.5 text-right uppercase border-r border-gray-200">TIPO:</td>
                                    <td class="p-1.5 text-center text-gray-700">AGROINDUSTRIA / CENTRO DISTRIBUCIÓN</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="font-bold bg-gray-50 p-1.5 text-right uppercase border-r border-gray-200">DIRECCIÓN:</td>
                                    <td class="p-1.5 text-center uppercase text-[10px] leading-tight">{{ guia.empresa_destino.direccion }}</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="font-bold bg-gray-50 p-1.5 text-right uppercase border-r border-gray-200">ESTADO:</td>
                                    <td class="p-1.5 text-center uppercase font-semibold">{{ guia.empresa_destino.estado || 'CARABOBO' }}</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="font-bold bg-gray-50 p-1.5 text-right uppercase border-r border-gray-200">MUNICIPIO:</td>
                                    <td class="p-1.5 text-center uppercase">{{ guia.empresa_destino.ciudad || 'Valencia' }}</td>
                                </tr>
                                <tr class="border-b border-gray-200">
                                    <td class="font-bold bg-gray-50 p-1.5 text-right uppercase border-r border-gray-200">PARROQUIA:</td>
                                    <td class="p-1.5 text-center uppercase">{{ guia.empresa_destino.parroquia || 'Urbana Rafael Urdaneta' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-bold bg-gray-50 p-1.5 text-right uppercase border-r border-gray-200">CIUDAD:</td>
                                    <td class="p-1.5 text-center uppercase font-semibold">{{ guia.empresa_destino.ciudad || 'VALENCIA' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 7. DATOS DEL TRANSPORTE -->
            <div class="border-b border-gray-300">
                <div class="bg-[#2A4580] text-white font-bold text-center py-1.5 uppercase text-xs">
                    DATOS DEL TRANSPORTE
                </div>
                <table class="w-full text-[11px] border-collapse">
                    <tbody>
                        <tr class="border-b border-gray-200">
                            <td class="w-32 font-bold bg-gray-50 p-1.5 text-right uppercase border-r border-gray-200">CONDUCTOR:</td>
                            <td class="p-1.5 font-semibold text-center uppercase">
                                [{{ guia.conductor.cedula }}] - {{ guia.conductor.nombre_completo }}
                            </td>
                        </tr>
                        <tr>
                            <td class="font-bold bg-gray-50 p-1.5 text-right uppercase border-r border-gray-200">VEHÍCULO:</td>
                            <td class="p-1.5 font-semibold text-center uppercase">
                                {{ guia.vehiculo.tipo }} - {{ guia.vehiculo.placa }} -
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- 8. OBSERVACIÓN GENERAL / WARNING BANNER -->
            <div class="border-b border-gray-300">
                <div class="bg-[#F57C00] text-white font-bold text-center py-1 uppercase text-xs">
                    OBSERVACIÓN GENERAL DE LA GUÍA
                </div>
                <div class="bg-amber-50 border-t border-b border-amber-200 p-3 text-center text-amber-800 font-bold text-[11px] uppercase tracking-tight flex items-center justify-center gap-2">
                    <span>⚠️</span>
                    <span>-ESTE CUADRO REFLEJA SOLO DATOS DE CONSULTA, LA IMPRESIÓN DEL MISMO NO ES VÁLIDO PARA MOVILIZAR LOS RUBROS-</span>
                    <span>⚠️</span>
                </div>
            </div>

            <!-- 9. RUBROS TABLE -->
            <div>
                <div class="bg-[#2A4580] text-white font-bold text-center py-1.5 uppercase text-xs">
                    RUBROS
                </div>
                <table class="w-full text-[11px] text-center border-collapse">
                    <thead class="bg-gray-100 font-bold border-b border-gray-300 text-gray-700">
                        <tr>
                            <th class="p-2 border-r border-gray-200 text-left pl-4">RUBRO</th>
                            <th class="p-2 border-r border-gray-200">CANTIDAD</th>
                            <th class="p-2 border-r border-gray-200">P.V</th>
                            <th class="p-2 border-r border-gray-200">PRESENTACIÓN</th>
                            <th class="p-2">MARCA</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="item in guia.items" :key="item.id" class="hover:bg-gray-50">
                            <td class="p-2 text-left pl-4 font-semibold uppercase">{{ item.rubro.nombre }}</td>
                            <td class="p-2 font-mono font-bold">{{ Number(item.cantidad).toFixed(2) }} {{ item.rubro.unidad_medida }}</td>
                            <td class="p-2 font-mono">${{ Number(item.precio_unitario).toFixed(2) }}</td>
                            <td class="p-2 uppercase text-gray-600">{{ item.rubro.presentacion || 'BULTO / SACO' }}</td>
                            <td class="p-2 uppercase font-semibold text-gray-700">GENÉRICA</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- FOOTER COPYRIGHT -->
            <div class="bg-gray-100 text-center py-3 text-[10px] text-gray-500 border-t border-gray-300">
                República Bolivariana de Venezuela &bull; Superintendencia Nacional de Gestión Agroalimentaria (SUNAGRO)
            </div>
        </div>
    </div>
</template>
