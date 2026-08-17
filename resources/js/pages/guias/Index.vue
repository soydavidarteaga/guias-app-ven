<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { FileText, Plus, Search, Truck, CheckCircle2, Clock, Download, Eye } from '@lucide/vue';

defineOptions({
    layout: AppLayout,
});

interface Guia {
    id: number;
    nro_guia: string;
    fecha_emision: string;
    estado: string;
    empresa_origen: { razon_social: string; rif: string };
    empresa_destino: { razon_social: string; rif: string };
    conductor: { nombre_completo: string };
    vehiculo: { placa: string };
    items: Array<{ cantidad: number }>;
}

const props = defineProps<{
    guias: {
        data: Guia[];
        links: any[];
    };
    stats: {
        totalGuias: number;
        totalToneladas: number;
        guiasEnTransito: number;
        guiasEmitidas: number;
    };
    filters: {
        search?: string;
        estado?: string;
    };
}>();

const search = ref(props.filters.search || '');
const estado = ref(props.filters.estado || '');

watch([search, estado], () => {
    router.get(
        '/guias',
        { search: search.value, estado: estado.value },
        { preserveState: true, replace: true }
    );
});

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
    <Head title="Guías de Movilización - SICA" />

    <div class="space-y-6 p-6">
        <!-- Header -->
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Guías de Movilización SICA / SUNAGRO</h1>
                <p class="text-sm text-muted-foreground">
                    Gestión, emisión y control de guías de despacho de productos alimenticios.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <Button as-child variant="outline" class="gap-2 border-amber-500/50 text-amber-600 hover:bg-amber-500/10 hover:text-amber-700">
                    <a href="/guias/exportar-todas-zip">
                        <FileText class="h-4 w-4" />
                        Descargar Todas (ZIP)
                    </a>
                </Button>
                <Button as-child class="gap-2">
                    <Link href="/guias/crear">
                        <Plus class="h-4 w-4" />
                        Emitir Nueva Guía
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid gap-4 md:grid-cols-4">
            <Card class="border-sidebar-border/70">
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium">Total Emitidas</CardTitle>
                    <FileText class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ stats.totalGuias }}</div>
                    <p class="text-xs text-muted-foreground">Guías registradas en sistema</p>
                </CardContent>
            </Card>

            <Card class="border-sidebar-border/70">
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium">Total Toneladas</CardTitle>
                    <Truck class="h-4 w-4 text-primary" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ stats.totalToneladas.toFixed(2) }} TN</div>
                    <p class="text-xs text-muted-foreground">Volumen total movilizado</p>
                </CardContent>
            </Card>

            <Card class="border-sidebar-border/70">
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium">En Tránsito</CardTitle>
                    <Clock class="h-4 w-4 text-amber-500" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ stats.guiasEnTransito }}</div>
                    <p class="text-xs text-muted-foreground">Despachos en carretera</p>
                </CardContent>
            </Card>

            <Card class="border-sidebar-border/70">
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium">Emitidas Recientes</CardTitle>
                    <CheckCircle2 class="h-4 w-4 text-emerald-500" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ stats.guiasEmitidas }}</div>
                    <p class="text-xs text-muted-foreground">Listas para despacho</p>
                </CardContent>
            </Card>
        </div>

        <!-- Filters & Table -->
        <Card class="border-sidebar-border/70">
            <CardContent class="p-6">
                <div class="mb-4 flex flex-col gap-4 md:flex-row md:items-center justify-between">
                    <div class="relative flex-1 max-w-sm">
                        <Search class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                        <Input
                            v-model="search"
                            placeholder="Buscar por Nro. de Guía o Empresa..."
                            class="pl-9"
                        />
                    </div>
                    <div class="flex items-center gap-2">
                        <select
                            v-model="estado"
                            class="rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm"
                        >
                            <option value="">Todos los Estados</option>
                            <option value="Borrador">Borrador</option>
                            <option value="Emitida">Emitida</option>
                            <option value="En Tránsito">En Tránsito</option>
                            <option value="Completada">Completada</option>
                            <option value="Anulada">Anulada</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto rounded-md border">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-muted/50 text-xs font-semibold uppercase text-muted-foreground border-b">
                            <tr>
                                <th class="p-3">Nro. Guía</th>
                                <th class="p-3">Empresa Origen</th>
                                <th class="p-3">Empresa Destino</th>
                                <th class="p-3">Conductor / Placa</th>
                                <th class="p-3">Estado</th>
                                <th class="p-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="guia in guias.data" :key="guia.id" class="hover:bg-muted/30 transition">
                                <td class="p-3 font-medium font-mono text-primary">{{ guia.nro_guia }}</td>
                                <td class="p-3">
                                    <div class="font-medium">{{ guia.empresa_origen.razon_social }}</div>
                                    <div class="text-xs text-muted-foreground">{{ guia.empresa_origen.rif }}</div>
                                </td>
                                <td class="p-3">
                                    <div class="font-medium">{{ guia.empresa_destino.razon_social }}</div>
                                    <div class="text-xs text-muted-foreground">{{ guia.empresa_destino.rif }}</div>
                                </td>
                                <td class="p-3">
                                    <div>{{ guia.conductor.nombre_completo }}</div>
                                    <div class="text-xs font-mono text-muted-foreground">{{ guia.vehiculo.placa }}</div>
                                </td>
                                <td class="p-3">
                                    <Badge :variant="getBadgeVariant(guia.estado)">
                                        {{ guia.estado }}
                                    </Badge>
                                </td>
                                <td class="p-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Button as-child size="sm" variant="ghost" class="h-8 w-8 p-0">
                                            <Link :href="`/guias/${guia.nro_guia}`">
                                                <Eye class="h-4 w-4" />
                                            </Link>
                                        </Button>
                                        <Button as-child size="sm" variant="outline" class="h-8 p-2 text-xs gap-1">
                                            <a :href="`/guias/${guia.nro_guia}/pdf`" target="_blank">
                                                <Download class="h-3.5 w-3.5" />
                                                PDF
                                            </a>
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="guias.data.length === 0">
                                <td colspan="6" class="p-8 text-center text-muted-foreground">
                                    No se encontraron guías de movilización.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="guias.links.length > 3" class="mt-4 flex flex-wrap justify-center gap-1">
                    <template v-for="(link, i) in guias.links" :key="i">
                        <div v-if="link.url === null" class="mr-1 mb-1 px-4 py-2 text-sm text-muted-foreground border rounded" v-html="link.label"></div>
                        <Link v-else :href="link.url" class="mr-1 mb-1 px-4 py-2 text-sm border rounded hover:bg-muted" :class="{ 'bg-primary text-primary-foreground hover:bg-primary': link.active }">
                            <span v-html="link.label"></span>
                        </Link>
                    </template>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
