<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { ArrowLeft, Plus, Trash2, Save, Building2, Truck, Package, Calendar, FileText } from '@lucide/vue';

defineOptions({
    layout: AppLayout,
});

interface Empresa {
    id: number;
    razon_social: string;
    rif: string;
    codigo_sica?: string;
}

interface Conductor {
    id: number;
    nombre_completo: string;
    cedula: string;
}

interface Vehiculo {
    id: number;
    tipo: string;
    placa: string;
}

interface Rubro {
    id: number;
    nombre: string;
    unidad_medida: string;
    precio_base: number;
}

const props = defineProps<{
    empresas: Empresa[];
    conductores: Conductor[];
    vehiculos: Vehiculo[];
    rubros: Rubro[];
}>();

// Local Reactive Lists to allow dynamic additions without page refresh
const localEmpresas = ref<Empresa[]>([...props.empresas]);
const localConductores = ref<Conductor[]>([...props.conductores]);
const localVehiculos = ref<Vehiculo[]>([...props.vehiculos]);
const localRubros = ref<Rubro[]>([...props.rubros]);

const formatDateForInput = (d: Date) => {
    const pad = (n: number) => n.toString().padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
};

const now = new Date();
const initialEmision = formatDateForInput(now);
const initialVencimientoDate = new Date(now.getTime() + 4 * 24 * 60 * 60 * 1000);
const initialVencimiento = formatDateForInput(initialVencimientoDate);

const form = useForm({
    empresa_origen_id: localEmpresas.value[0]?.id || '',
    empresa_destino_id: localEmpresas.value[1]?.id || localEmpresas.value[0]?.id || '',
    conductor_id: localConductores.value[0]?.id || '',
    vehiculo_id: localVehiculos.value[0]?.id || '',
    fecha_emision: initialEmision,
    fecha_vencimiento: initialVencimiento,
    documentos_soporte: 'NE 1172 FACT. N 2683 PRECINTO 38468059/1804022',
    observacion: 'SACOS 25KG',
    items: [
        {
            rubro_id: localRubros.value[0]?.id || '',
            cantidad: 10,
            precio_unitario: localRubros.value[0]?.precio_base || 0,
            observacion: '',
        },
    ],
});

const onFechaEmisionChange = () => {
    if (form.fecha_emision) {
        const emisionDate = new Date(form.fecha_emision);
        if (!isNaN(emisionDate.getTime())) {
            const vencDate = new Date(emisionDate.getTime() + 4 * 24 * 60 * 60 * 1000);
            form.fecha_vencimiento = formatDateForInput(vencDate);
        }
    }
};

const addItem = () => {
    form.items.push({
        rubro_id: localRubros.value[0]?.id || '',
        cantidad: 1,
        precio_unitario: localRubros.value[0]?.precio_base || 0,
        observacion: '',
    });
};

const removeItem = (index: number) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    }
};

const onRubroChange = (index: number, rubroId: number | string) => {
    const selected = localRubros.value.find(r => r.id === Number(rubroId));
    if (selected) {
        form.items[index].precio_unitario = selected.precio_base;
    }
};

const totalPeso = computed(() => {
    return form.items.reduce((acc, item) => acc + (Number(item.cantidad) || 0), 0);
});

const totalMonto = computed(() => {
    return form.items.reduce(
        (acc, item) => acc + (Number(item.cantidad) || 0) * (Number(item.precio_unitario) || 0),
        0
    );
});

const submit = () => {
    form.post('/guias');
};

// ---------------------------------------------------------------------
// QUICK CREATION MODALS
// ---------------------------------------------------------------------

// 1. Quick Empresa Modal
const isEmpresaModalOpen = ref(false);
const empresaTargetField = ref<'origen' | 'destino'>('origen');
const empresaForm = ref({
    razon_social: '',
    rif: '',
    codigo_sica: '',
    persona_autorizada: '',
    telefonos: '',
    estado: '',
    ciudad: '',
    direccion: '',
});
const empresaError = ref('');
const isSubmittingEmpresa = ref(false);

const openQuickEmpresaModal = (target: 'origen' | 'destino') => {
    empresaTargetField.value = target;
    empresaForm.value = {
        razon_social: '',
        rif: '',
        codigo_sica: '',
        persona_autorizada: '',
        telefonos: '',
        estado: '',
        ciudad: '',
        direccion: '',
    };
    empresaError.value = '';
    isEmpresaModalOpen.value = true;
};

const submitQuickEmpresa = async () => {
    try {
        isSubmittingEmpresa.value = true;
        empresaError.value = '';
        const response = await axios.post('/empresas', empresaForm.value);
        const newEmpresa = response.data.data;
        
        localEmpresas.value.unshift(newEmpresa);
        if (empresaTargetField.value === 'origen') {
            form.empresa_origen_id = newEmpresa.id;
        } else {
            form.empresa_destino_id = newEmpresa.id;
        }
        isEmpresaModalOpen.value = false;
    } catch (err: any) {
        empresaError.value = err.response?.data?.message || 'Error al registrar la empresa.';
    } finally {
        isSubmittingEmpresa.value = false;
    }
};

// 2. Quick Conductor Modal
const isConductorModalOpen = ref(false);
const conductorForm = ref({
    nombre_completo: '',
    cedula: '',
    telefono: '',
});
const conductorError = ref('');
const isSubmittingConductor = ref(false);

const openQuickConductorModal = () => {
    conductorForm.value = { nombre_completo: '', cedula: '', telefono: '' };
    conductorError.value = '';
    isConductorModalOpen.value = true;
};

const submitQuickConductor = async () => {
    try {
        isSubmittingConductor.value = true;
        conductorError.value = '';
        const response = await axios.post('/conductores', conductorForm.value);
        const newConductor = response.data.data;

        localConductores.value.unshift(newConductor);
        form.conductor_id = newConductor.id;
        isConductorModalOpen.value = false;
    } catch (err: any) {
        conductorError.value = err.response?.data?.message || 'Error al registrar el conductor.';
    } finally {
        isSubmittingConductor.value = false;
    }
};

// 3. Quick Vehiculo Modal
const isVehiculoModalOpen = ref(false);
const vehiculoForm = ref({
    tipo: '',
    placa: '',
    estatus: 'Operativo',
});
const vehiculoError = ref('');
const isSubmittingVehiculo = ref(false);

const openQuickVehiculoModal = () => {
    vehiculoForm.value = { tipo: '', placa: '', estatus: 'Operativo' };
    vehiculoError.value = '';
    isVehiculoModalOpen.value = true;
};

const submitQuickVehiculo = async () => {
    try {
        isSubmittingVehiculo.value = true;
        vehiculoError.value = '';
        const response = await axios.post('/vehiculos', vehiculoForm.value);
        const newVehiculo = response.data.data;

        localVehiculos.value.unshift(newVehiculo);
        form.vehiculo_id = newVehiculo.id;
        isVehiculoModalOpen.value = false;
    } catch (err: any) {
        vehiculoError.value = err.response?.data?.message || 'Error al registrar el vehículo.';
    } finally {
        isSubmittingVehiculo.value = false;
    }
};

// 4. Quick Rubro Modal
const isRubroModalOpen = ref(false);
const activeItemIndexForRubro = ref(0);
const rubroForm = ref({
    nombre: '',
    codigo_arancelario: '',
    unidad_medida: 'TN',
    presentacion: '',
    precio_base: 0,
});
const rubroError = ref('');
const isSubmittingRubro = ref(false);

const openQuickRubroModal = (index: number) => {
    activeItemIndexForRubro.value = index;
    rubroForm.value = {
        nombre: '',
        codigo_arancelario: '',
        unidad_medida: 'TN',
        presentacion: '',
        precio_base: 0,
    };
    rubroError.value = '';
    isRubroModalOpen.value = true;
};

const submitQuickRubro = async () => {
    try {
        isSubmittingRubro.value = true;
        rubroError.value = '';
        const response = await axios.post('/rubros', rubroForm.value);
        const newRubro = response.data.data;

        localRubros.value.unshift(newRubro);
        if (form.items[activeItemIndexForRubro.value]) {
            form.items[activeItemIndexForRubro.value].rubro_id = newRubro.id;
            form.items[activeItemIndexForRubro.value].precio_unitario = newRubro.precio_base;
        }
        isRubroModalOpen.value = false;
    } catch (err: any) {
        rubroError.value = err.response?.data?.message || 'Error al registrar el rubro.';
    } finally {
        isSubmittingRubro.value = false;
    }
};
</script>

<template>
    <Head title="Emitir Guía de Movilización - SICA" />

    <div class="space-y-6 p-6 max-w-5xl mx-auto">
        <!-- Header -->
        <div class="flex items-center gap-4">
            <Button as-child variant="outline" size="sm">
                <Link href="/guias">
                    <ArrowLeft class="h-4 w-4 mr-1" />
                    Volver
                </Link>
            </Button>
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Emitir Guía de Movilización (SICA)</h1>
                <p class="text-sm text-muted-foreground">
                    Complete la información requerida para el despacho de rubros alimenticios.
                </p>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-6">

            <!-- Fechas y Documentos de Soporte -->
            <Card class="border-sidebar-border/70">
                <CardHeader>
                    <CardTitle class="text-lg flex items-center gap-2">
                        <Calendar class="h-5 w-5 text-primary" />
                        1. Fechas y Documentación del Despacho
                    </CardTitle>
                    <CardDescription>Establezca las fechas de vigencia y soporte legal del traslado.</CardDescription>
                </CardHeader>
                <CardContent class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <Label for="fecha_emision">Fecha de Aprobación / Emisión</Label>
                        <Input
                            id="fecha_emision"
                            type="datetime-local"
                            v-model="form.fecha_emision"
                            @change="onFechaEmisionChange"
                            required
                        />
                        <p class="text-xs text-muted-foreground">Al cambiar la fecha de aprobación, la fecha de vencimiento se calcula a +4 días automáticamente.</p>
                    </div>

                    <div class="space-y-2">
                        <Label for="fecha_vencimiento">Fecha de Vencimiento (+4 días)</Label>
                        <Input
                            id="fecha_vencimiento"
                            type="datetime-local"
                            v-model="form.fecha_vencimiento"
                            required
                        />
                        <p class="text-xs text-muted-foreground">Fecha límite de vigencia de la guía (modificable libremente).</p>
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <Label for="documentos_soporte" class="flex items-center gap-2">
                            <FileText class="h-4 w-4 text-primary" />
                            Facturas u Órdenes que Soportan el Despacho
                        </Label>
                        <Input
                            id="documentos_soporte"
                            type="text"
                            v-model="form.documentos_soporte"
                            placeholder="Ej. NE 1172 FACT. N 2683 PRECINTO 38468059/1804022"
                        />
                        <p class="text-xs text-muted-foreground">Indique los números de factura, notas de entrega, órdenes o precintos asociados.</p>
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <Label for="observacion" class="flex items-center gap-2">
                            <FileText class="h-4 w-4 text-primary" />
                            Observación de los Rubros (PDF)
                        </Label>
                        <Input
                            id="observacion"
                            type="text"
                            v-model="form.observacion"
                            placeholder="Ej. SACOS 25KG"
                        />
                        <p class="text-xs text-muted-foreground">Observación impresa en la tabla de rubros del PDF de la guía.</p>
                    </div>
                </CardContent>
            </Card>

            <!-- 2. Datos de Origen y Destino -->
            <Card class="border-sidebar-border/70">
                <CardHeader>
                    <CardTitle class="text-lg flex items-center gap-2">
                        <Building2 class="h-5 w-5 text-primary" />
                        2. Origen y Destino del Despacho
                    </CardTitle>
                    <CardDescription>Seleccione las empresas participantes registradas en el sistema SICA.</CardDescription>
                </CardHeader>
                <CardContent class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <Label for="empresa_origen">Empresa Origen (Despachadora)</Label>
                            <Button
                                type="button"
                                variant="link"
                                size="sm"
                                class="h-auto p-0 text-xs text-primary gap-1"
                                @click="openQuickEmpresaModal('origen')"
                            >
                                <Plus class="h-3.5 w-3.5" />
                                Nueva Empresa
                            </Button>
                        </div>
                        <select
                            id="empresa_origen"
                            v-model="form.empresa_origen_id"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm"
                        >
                            <option v-for="e in localEmpresas" :key="e.id" :value="e.id">
                                {{ e.razon_social }} (RIF: {{ e.rif }})
                            </option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <Label for="empresa_destino">Empresa Destino (Receptora)</Label>
                            <Button
                                type="button"
                                variant="link"
                                size="sm"
                                class="h-auto p-0 text-xs text-primary gap-1"
                                @click="openQuickEmpresaModal('destino')"
                            >
                                <Plus class="h-3.5 w-3.5" />
                                Nueva Empresa
                            </Button>
                        </div>
                        <select
                            id="empresa_destino"
                            v-model="form.empresa_destino_id"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm"
                        >
                            <option v-for="e in localEmpresas" :key="e.id" :value="e.id">
                                {{ e.razon_social }} (RIF: {{ e.rif }})
                            </option>
                        </select>
                    </div>
                </CardContent>
            </Card>

            <!-- 3. Datos de Transporte -->
            <Card class="border-sidebar-border/70">
                <CardHeader>
                    <CardTitle class="text-lg flex items-center gap-2">
                        <Truck class="h-5 w-5 text-primary" />
                        3. Flota y Conductor Autorizado
                    </CardTitle>
                    <CardDescription>Asigne el vehículo de transporte y el conductor responsable.</CardDescription>
                </CardHeader>
                <CardContent class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <Label for="conductor">Conductor Registrado</Label>
                            <Button
                                type="button"
                                variant="link"
                                size="sm"
                                class="h-auto p-0 text-xs text-primary gap-1"
                                @click="openQuickConductorModal"
                            >
                                <Plus class="h-3.5 w-3.5" />
                                Nuevo Conductor
                            </Button>
                        </div>
                        <select
                            id="conductor"
                            v-model="form.conductor_id"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm"
                        >
                            <option v-for="c in localConductores" :key="c.id" :value="c.id">
                                {{ c.nombre_completo }} - CI: {{ c.cedula }}
                            </option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <Label for="vehiculo">Vehículo / Placa</Label>
                            <Button
                                type="button"
                                variant="link"
                                size="sm"
                                class="h-auto p-0 text-xs text-primary gap-1"
                                @click="openQuickVehiculoModal"
                            >
                                <Plus class="h-3.5 w-3.5" />
                                Nuevo Vehículo
                            </Button>
                        </div>
                        <select
                            id="vehiculo"
                            v-model="form.vehiculo_id"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm"
                        >
                            <option v-for="v in localVehiculos" :key="v.id" :value="v.id">
                                {{ v.tipo }} (Placa: {{ v.placa }})
                            </option>
                        </select>
                    </div>
                </CardContent>
            </Card>

            <!-- 4. Detalle de Rubros -->
            <Card class="border-sidebar-border/70">
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle class="text-lg flex items-center gap-2">
                            <Package class="h-5 w-5 text-primary" />
                            4. Rubros y Carga Alimenticia
                        </CardTitle>
                        <CardDescription>Agregue los productos que componen la carga.</CardDescription>
                    </div>
                    <Button type="button" variant="outline" size="sm" @click="addItem" class="gap-1">
                        <Plus class="h-4 w-4" />
                        Agregar Rubro
                    </Button>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="overflow-x-auto rounded-md border">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-muted/50 text-xs font-semibold uppercase text-muted-foreground border-b">
                                <tr>
                                    <th class="p-3 w-2/5">Rubro / Producto</th>
                                    <th class="p-3 w-1/5">Cantidad (TN)</th>
                                    <th class="p-3 w-1/5">Precio Unitario ($)</th>
                                    <th class="p-3 w-1/5">Observación</th>
                                    <th class="p-3 text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <tr v-for="(item, index) in form.items" :key="index">
                                    <td class="p-2">
                                        <div class="flex items-center gap-2">
                                            <select
                                                v-model="item.rubro_id"
                                                @change="onRubroChange(index, item.rubro_id)"
                                                class="w-full rounded-md border border-input bg-background px-2 py-1 text-sm shadow-sm"
                                            >
                                                <option v-for="r in localRubros" :key="r.id" :value="r.id">
                                                    {{ r.nombre }} ({{ r.unidad_medida }})
                                                </option>
                                            </select>
                                            <Button
                                                type="button"
                                                variant="ghost"
                                                size="sm"
                                                class="h-8 w-8 p-0 text-primary shrink-0"
                                                title="Crear Nuevo Rubro"
                                                @click="openQuickRubroModal(index)"
                                            >
                                                <Plus class="h-4 w-4" />
                                            </Button>
                                        </div>
                                    </td>
                                    <td class="p-2">
                                        <Input
                                            type="number"
                                            step="0.01"
                                            v-model.number="item.cantidad"
                                            placeholder="0.00"
                                            class="h-8"
                                        />
                                    </td>
                                    <td class="p-2">
                                        <Input
                                            type="number"
                                            step="0.01"
                                            v-model.number="item.precio_unitario"
                                            placeholder="0.00"
                                            class="h-8"
                                        />
                                    </td>
                                    <td class="p-2">
                                        <Input
                                            type="text"
                                            v-model="item.observacion"
                                            placeholder="Ej. Lote 001"
                                            class="h-8"
                                        />
                                    </td>
                                    <td class="p-2 text-center">
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="sm"
                                            class="h-8 w-8 p-0 text-destructive"
                                            @click="removeItem(index)"
                                            :disabled="form.items.length === 1"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Resumen Totales -->
                    <div class="flex justify-end gap-6 bg-muted/30 p-4 rounded-md font-medium text-sm">
                        <div>Peso Total: <span class="font-bold text-primary">{{ totalPeso.toFixed(2) }} TN</span></div>
                        <div>Monto Total Estimado: <span class="font-bold text-emerald-600">${{ totalMonto.toFixed(2) }}</span></div>
                    </div>
                </CardContent>
            </Card>

            <!-- Submit Button -->
            <div class="flex justify-end gap-4">
                <Button type="button" variant="outline" as-child>
                    <Link href="/guias">Cancelar</Link>
                </Button>
                <Button type="submit" :disabled="form.processing" class="gap-2">
                    <Save class="h-4 w-4" />
                    Emitir Guía de Movilización
                </Button>
            </div>
        </form>

        <!-- =================================================================== -->
        <!-- INLINE QUICK CREATION DIALOGS -->
        <!-- =================================================================== -->

        <!-- 1. Quick Empresa Dialog -->
        <Dialog :open="isEmpresaModalOpen" @update:open="isEmpresaModalOpen = $event">
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>Registro Rápido de Empresa ({{ empresaTargetField === 'origen' ? 'Origen' : 'Destino' }})</DialogTitle>
                    <DialogDescription>Cree la empresa para seleccionarla inmediatamente en la guía.</DialogDescription>
                </DialogHeader>
                <form @submit.prevent="submitQuickEmpresa" class="space-y-3 py-2">
                    <div v-if="empresaError" class="p-2 text-xs bg-destructive/10 text-destructive rounded">
                        {{ empresaError }}
                    </div>
                    <div class="space-y-1">
                        <Label for="q_razon_social">Razón Social *</Label>
                        <Input id="q_razon_social" v-model="empresaForm.razon_social" required placeholder="Ej. Distribuidora Central C.A." />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <Label for="q_rif">RIF / C.I. *</Label>
                            <Input id="q_rif" v-model="empresaForm.rif" required placeholder="Ej. J-99999999-0" />
                        </div>
                        <div class="space-y-1">
                            <Label for="q_codigo_sica">Código SICA</Label>
                            <Input id="q_codigo_sica" v-model="empresaForm.codigo_sica" placeholder="Ej. SICA-1234" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <Label for="q_estado">Estado</Label>
                            <Input id="q_estado" v-model="empresaForm.estado" placeholder="Ej. Miranda" />
                        </div>
                        <div class="space-y-1">
                            <Label for="q_ciudad">Ciudad</Label>
                            <Input id="q_ciudad" v-model="empresaForm.ciudad" placeholder="Ej. Caracas" />
                        </div>
                    </div>
                    <div class="space-y-1">
                        <Label for="q_direccion">Dirección</Label>
                        <Input id="q_direccion" v-model="empresaForm.direccion" placeholder="Ej. Av. Francisco de Miranda" />
                    </div>
                    <DialogFooter class="pt-2">
                        <Button type="button" variant="outline" @click="isEmpresaModalOpen = false">Cancelar</Button>
                        <Button type="submit" :disabled="isSubmittingEmpresa">Guardar y Seleccionar</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- 2. Quick Conductor Dialog -->
        <Dialog :open="isConductorModalOpen" @update:open="isConductorModalOpen = $event">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Registro Rápido de Conductor</DialogTitle>
                    <DialogDescription>Cree el chofer para seleccionarlo inmediatamente.</DialogDescription>
                </DialogHeader>
                <form @submit.prevent="submitQuickConductor" class="space-y-3 py-2">
                    <div v-if="conductorError" class="p-2 text-xs bg-destructive/10 text-destructive rounded">
                        {{ conductorError }}
                    </div>
                    <div class="space-y-1">
                        <Label for="q_nombre_completo">Nombre Completo *</Label>
                        <Input id="q_nombre_completo" v-model="conductorForm.nombre_completo" required placeholder="Ej. Carlos Mendoza" />
                    </div>
                    <div class="space-y-1">
                        <Label for="q_cedula">Cédula de Identidad *</Label>
                        <Input id="q_cedula" v-model="conductorForm.cedula" required placeholder="Ej. V-20123456" />
                    </div>
                    <div class="space-y-1">
                        <Label for="q_telefono">Teléfono</Label>
                        <Input id="q_telefono" v-model="conductorForm.telefono" placeholder="Ej. 0414-9990011" />
                    </div>
                    <DialogFooter class="pt-2">
                        <Button type="button" variant="outline" @click="isConductorModalOpen = false">Cancelar</Button>
                        <Button type="submit" :disabled="isSubmittingConductor">Guardar y Seleccionar</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- 3. Quick Vehiculo Dialog -->
        <Dialog :open="isVehiculoModalOpen" @update:open="isVehiculoModalOpen = $event">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Registro Rápido de Vehículo</DialogTitle>
                    <DialogDescription>Cree la unidad de transporte para seleccionarla inmediatamente.</DialogDescription>
                </DialogHeader>
                <form @submit.prevent="submitQuickVehiculo" class="space-y-3 py-2">
                    <div v-if="vehiculoError" class="p-2 text-xs bg-destructive/10 text-destructive rounded">
                        {{ vehiculoError }}
                    </div>
                    <div class="space-y-1">
                        <Label for="q_tipo_vehiculo">Tipo de Vehículo *</Label>
                        <Input id="q_tipo_vehiculo" v-model="vehiculoForm.tipo" required placeholder="Ej. Camión Cava 750" />
                    </div>
                    <div class="space-y-1">
                        <Label for="q_placa">Placa / Matrícula *</Label>
                        <Input id="q_placa" v-model="vehiculoForm.placa" required placeholder="Ej. A99BB00" />
                    </div>
                    <DialogFooter class="pt-2">
                        <Button type="button" variant="outline" @click="isVehiculoModalOpen = false">Cancelar</Button>
                        <Button type="submit" :disabled="isSubmittingVehiculo">Guardar y Seleccionar</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- 4. Quick Rubro Dialog -->
        <Dialog :open="isRubroModalOpen" @update:open="isRubroModalOpen = $event">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Registro Rápido de Rubro</DialogTitle>
                    <DialogDescription>Cree el producto regulado para asignarlo a esta línea.</DialogDescription>
                </DialogHeader>
                <form @submit.prevent="submitQuickRubro" class="space-y-3 py-2">
                    <div v-if="rubroError" class="p-2 text-xs bg-destructive/10 text-destructive rounded">
                        {{ rubroError }}
                    </div>
                    <div class="space-y-1">
                        <Label for="q_nombre_rubro">Nombre del Rubro *</Label>
                        <Input id="q_nombre_rubro" v-model="rubroForm.nombre" required placeholder="Ej. Aceite Vegetal 1L" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <Label for="q_unidad">Unidad de Medida</Label>
                            <select id="q_unidad" v-model="rubroForm.unidad_medida" class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm">
                                <option value="TN">TN</option>
                                <option value="KG">KG</option>
                                <option value="Cajas">Cajas</option>
                                <option value="Bultos">Bultos</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <Label for="q_precio_base">Precio Ref. ($)</Label>
                            <Input id="q_precio_base" type="number" step="0.01" v-model.number="rubroForm.precio_base" required placeholder="0.00" />
                        </div>
                    </div>
                    <div class="space-y-1">
                        <Label for="q_presentacion">Presentación</Label>
                        <Input id="q_presentacion" v-model="rubroForm.presentacion" placeholder="Ej. Caja de 12 Unidades" />
                    </div>
                    <DialogFooter class="pt-2">
                        <Button type="button" variant="outline" @click="isRubroModalOpen = false">Cancelar</Button>
                        <Button type="submit" :disabled="isSubmittingRubro">Guardar y Seleccionar</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </div>
</template>
