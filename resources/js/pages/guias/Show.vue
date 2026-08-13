<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ArrowLeft, Download, ExternalLink, Building2, Truck, MapPin, QrCode, Copy, Check } from '@lucide/vue';
import { ref } from 'vue';

defineOptions({
    layout: AppLayout,
});

interface Guia {
    id: number;
    nro_guia: string;
    fecha_emision: string;
    fecha_vencimiento: string;
    estado: string;
    qr_hash: string;
    trazabilidad: Array<{ estado: string; fecha: string; comentario?: string; usuario_id?: string }>;
    empresa_origen: { razon_social: string; rif: string; codigo_sica: string; direccion: string; estado: string };
    empresa_destino: { razon_social: string; rif: string; codigo_sica: string; direccion: string; estado: string };
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
}>();

const copied = ref(false);

const publicUrl = computed(() => {
    return `${window.location.origin}/guias/${props.guia.qr_hash}`;
});

const copyPublicUrl = () => {
    navigator.clipboard.writeText(publicUrl.value);
    copied.value = true;
    setTimeout(() => {
        copied.value = false;
    }, 2000);
};

const getBadgeVariant = (status: string) => {
    switch (status) {
        case 'Emitida': return 'default';
        case 'En Tránsito': return 'outline';
        case 'Completada': return 'secondary';
        case 'Anulada': return 'destructive';
        default: return 'outline';
    }
};
</script>

<template>
    <Head :title="`Guía ${guia.nro_guia} - SICA`" />

    <div class="space-y-6 p-6 max-w-5xl mx-auto">
        <!-- Top Bar -->
        <div class="flex flex-col gap-4 md:flex-row md:items-center justify-between">
            <div class="flex items-center gap-3">
                <Button as-child variant="outline" size="sm">
                    <Link href="/guias">
                        <ArrowLeft class="h-4 w-4 mr-1" />
                        Volver al Listado
                    </Link>
                </Button>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold font-mono tracking-tight">{{ guia.nro_guia }}</h1>
                        <Badge :variant="getBadgeVariant(guia.estado)" class="text-sm">
                            {{ guia.estado }}
                        </Badge>
                    </div>
                    <p class="text-xs text-muted-foreground mt-0.5">
                        Emisión: {{ new Date(guia.fecha_emision).toLocaleString() }} | Vencimiento: {{ new Date(guia.fecha_vencimiento).toLocaleString() }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <!-- Botón de Enlace Público de Verificación -->
                <Button as-child variant="outline" class="gap-2">
                    <a :href="`/guias/${guia.qr_hash}`" target="_blank" title="Abrir portal de verificación pública">
                        <ExternalLink class="h-4 w-4 text-emerald-600" />
                        Ver Guía Pública
                    </a>
                </Button>

                <Button as-child class="gap-2">
                    <a :href="`/guias/${guia.id}/pdf`" target="_blank">
                        <Download class="h-4 w-4" />
                        Descargar PDF
                    </a>
                </Button>
            </div>
        </div>

        <!-- Origen y Destino Grid -->
        <div class="grid md:grid-cols-2 gap-6">
            <Card class="border-sidebar-border/70">
                <CardHeader class="pb-3">
                    <CardTitle class="text-base flex items-center gap-2">
                        <Building2 class="h-4 w-4 text-primary" />
                        Empresa Origen (Despachadora)
                    </CardTitle>
                </CardHeader>
                <CardContent class="space-y-1.5 text-sm">
                    <div class="font-semibold text-lg">{{ guia.empresa_origen.razon_social }}</div>
                    <div><span class="text-muted-foreground">RIF:</span> {{ guia.empresa_origen.rif }}</div>
                    <div><span class="text-muted-foreground">Código SICA:</span> {{ guia.empresa_origen.codigo_sica }}</div>
                    <div class="flex items-start gap-1 text-muted-foreground pt-1">
                        <MapPin class="h-4 w-4 shrink-0 mt-0.5" />
                        <span>{{ guia.empresa_origen.direccion }} ({{ guia.empresa_origen.estado }})</span>
                    </div>
                </CardContent>
            </Card>

            <Card class="border-sidebar-border/70">
                <CardHeader class="pb-3">
                    <CardTitle class="text-base flex items-center gap-2">
                        <Building2 class="h-4 w-4 text-primary" />
                        Empresa Destino (Receptora)
                    </CardTitle>
                </CardHeader>
                <CardContent class="space-y-1.5 text-sm">
                    <div class="font-semibold text-lg">{{ guia.empresa_destino.razon_social }}</div>
                    <div><span class="text-muted-foreground">RIF:</span> {{ guia.empresa_destino.rif }}</div>
                    <div><span class="text-muted-foreground">Código SICA:</span> {{ guia.empresa_destino.codigo_sica }}</div>
                    <div class="flex items-start gap-1 text-muted-foreground pt-1">
                        <MapPin class="h-4 w-4 shrink-0 mt-0.5" />
                        <span>{{ guia.empresa_destino.direccion }} ({{ guia.empresa_destino.estado }})</span>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Transporte & QR Section -->
        <div class="grid md:grid-cols-3 gap-6">
            <Card class="md:col-span-2 border-sidebar-border/70">
                <CardHeader class="pb-3">
                    <CardTitle class="text-base flex items-center gap-2">
                        <Truck class="h-4 w-4 text-primary" />
                        Datos del Transporte
                    </CardTitle>
                </CardHeader>
                <CardContent class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-xs text-muted-foreground uppercase font-semibold">Conductor</div>
                        <div class="font-medium text-base">{{ guia.conductor.nombre_completo }}</div>
                        <div class="text-xs text-muted-foreground">Cédula: {{ guia.conductor.cedula }}</div>
                        <div class="text-xs text-muted-foreground">Teléfono: {{ guia.conductor.telefono }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-muted-foreground uppercase font-semibold">Vehículo</div>
                        <div class="font-medium text-base">{{ guia.vehiculo.tipo }}</div>
                        <div class="font-mono text-xs text-primary font-bold">Placa: {{ guia.vehiculo.placa }}</div>
                    </div>
                </CardContent>
            </Card>

            <!-- Card de Código QR y Enlace Público -->
            <Card class="border-sidebar-border/70 flex flex-col justify-center items-center p-4 text-center">
                <QrCode class="h-6 w-6 text-primary mb-1" />
                <div class="text-xs font-semibold uppercase text-muted-foreground mb-2">Verificación QR SICA</div>
                
                <a :href="`/guias/${guia.qr_hash}`" target="_blank" title="Haga clic para abrir la verificación pública">
                    <img
                        :src="`https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=${encodeURIComponent(publicUrl)}`"
                        alt="QR Verification"
                        class="rounded border p-1 bg-white hover:scale-105 transition-transform cursor-pointer shadow-sm"
                    />
                </a>

                <div class="mt-3 flex items-center justify-center gap-1.5 w-full">
                    <a
                        :href="`/guias/${guia.qr_hash}`"
                        target="_blank"
                        class="text-[11px] font-mono text-emerald-600 dark:text-emerald-400 hover:underline truncate max-w-[180px]"
                        :title="publicUrl"
                    >
                        /guias/{{ guia.qr_hash }}
                    </a>
                    <button
                        @click="copyPublicUrl"
                        class="p-1 text-muted-foreground hover:text-foreground rounded"
                        title="Copiar Enlace Público"
                    >
                        <Check v-if="copied" class="h-3.5 w-3.5 text-emerald-600" />
                        <Copy v-else class="h-3.5 w-3.5" />
                    </button>
                </div>
            </Card>
        </div>

        <!-- Tabla de Productos -->
        <Card class="border-sidebar-border/70">
            <CardHeader>
                <CardTitle class="text-base">Productos / Rubros Despachados</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="overflow-x-auto rounded-md border">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-muted/50 text-xs font-semibold uppercase text-muted-foreground border-b">
                            <tr>
                                <th class="p-3">Rubro</th>
                                <th class="p-3">Presentación</th>
                                <th class="p-3 text-right">Cantidad (TN)</th>
                                <th class="p-3 text-right">Precio Ref. ($)</th>
                                <th class="p-3">Observación</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="item in guia.items" :key="item.id">
                                <td class="p-3 font-medium">{{ item.rubro.nombre }}</td>
                                <td class="p-3 text-muted-foreground">{{ item.rubro.presentacion }}</td>
                                <td class="p-3 text-right font-mono font-bold">{{ item.cantidad }}</td>
                                <td class="p-3 text-right font-mono">${{ item.precio_unitario }}</td>
                                <td class="p-3 text-muted-foreground text-xs">{{ item.observacion || '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end mt-4 text-sm font-semibold">
                    <div class="bg-muted/40 px-4 py-2 rounded-md">
                        Peso Total de la Guía: <span class="text-primary font-bold font-mono text-base ml-2">{{ pesoTotal.toFixed(2) }} TN</span>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
