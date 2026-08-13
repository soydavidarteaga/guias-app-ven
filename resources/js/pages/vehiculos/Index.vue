<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Truck, Plus, Search, Pencil, Trash2 } from '@lucide/vue';

defineOptions({
    layout: AppLayout,
});

interface Vehiculo {
    id: number;
    tipo: string;
    placa: string;
    estatus: string;
}

const props = defineProps<{
    vehiculos: {
        data: Vehiculo[];
        links: any[];
    };
    filters: {
        search?: string;
    };
}>();

const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get(
        '/vehiculos',
        { search: value },
        { preserveState: true, replace: true }
    );
});

// Modal State (Create / Edit)
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingId = ref<number | null>(null);

const form = useForm({
    tipo: '',
    placa: '',
    estatus: 'Operativo',
});

const openCreateModal = () => {
    form.reset();
    form.clearErrors();
    form.estatus = 'Operativo';
    isEditing.value = false;
    editingId.value = null;
    isModalOpen.value = true;
};

const openEditModal = (vehiculo: Vehiculo) => {
    form.clearErrors();
    isEditing.value = true;
    editingId.value = vehiculo.id;

    form.tipo = vehiculo.tipo || '';
    form.placa = vehiculo.placa || '';
    form.estatus = vehiculo.estatus || 'Operativo';

    isModalOpen.value = true;
};

const submitForm = () => {
    if (isEditing.value && editingId.value) {
        form.put(`/vehiculos/${editingId.value}`, {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.post('/vehiculos', {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    }
};

// Delete State
const isDeleteModalOpen = ref(false);
const vehiculoToDelete = ref<Vehiculo | null>(null);

const confirmDelete = (vehiculo: Vehiculo) => {
    vehiculoToDelete.value = vehiculo;
    isDeleteModalOpen.value = true;
};

const deleteVehiculo = () => {
    if (vehiculoToDelete.value) {
        router.delete(`/vehiculos/${vehiculoToDelete.value.id}`, {
            onSuccess: () => {
                isDeleteModalOpen.value = false;
                vehiculoToDelete.value = null;
            },
        });
    }
};

const getStatusBadgeVariant = (estatus: string) => {
    switch (estatus) {
        case 'Operativo': return 'default';
        case 'Mantenimiento': return 'outline';
        case 'Inactivo': return 'destructive';
        default: return 'outline';
    }
};
</script>

<template>
    <Head title="Gestión de Flota / Vehículos - SICA" />

    <div class="space-y-6 p-6 max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Gestión de Flota y Vehículos</h1>
                <p class="text-sm text-muted-foreground">
                    Administre camiones, gandolas y unidades de transporte autorizadas.
                </p>
            </div>
            <Button @click="openCreateModal" class="gap-2">
                <Plus class="h-4 w-4" />
                Registrar Nuevo Vehículo
            </Button>
        </div>

        <!-- Filters & Table Card -->
        <Card class="border-sidebar-border/70">
            <CardContent class="p-6">
                <!-- Search -->
                <div class="mb-4 flex items-center justify-between">
                    <div class="relative flex-1 max-w-sm">
                        <Search class="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                        <Input
                            v-model="search"
                            placeholder="Buscar por Tipo de Vehículo, Placa o Estatus..."
                            class="pl-9"
                        />
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto rounded-md border">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-muted/50 text-xs font-semibold uppercase text-muted-foreground border-b">
                            <tr>
                                <th class="p-3">Tipo de Vehículo</th>
                                <th class="p-3">Placa / Matrícula</th>
                                <th class="p-3">Estatus</th>
                                <th class="p-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="vehiculo in vehiculos.data" :key="vehiculo.id" class="hover:bg-muted/30 transition">
                                <td class="p-3 font-medium flex items-center gap-2">
                                    <Truck class="h-4 w-4 text-primary shrink-0" />
                                    {{ vehiculo.tipo }}
                                </td>
                                <td class="p-3 font-mono font-bold text-xs uppercase text-primary">
                                    {{ vehiculo.placa }}
                                </td>
                                <td class="p-3">
                                    <Badge :variant="getStatusBadgeVariant(vehiculo.estatus)">
                                        {{ vehiculo.estatus }}
                                    </Badge>
                                </td>
                                <td class="p-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button
                                            size="sm"
                                            variant="ghost"
                                            class="h-8 w-8 p-0"
                                            @click="openEditModal(vehiculo)"
                                        >
                                            <Pencil class="h-4 w-4 text-muted-foreground hover:text-primary" />
                                        </Button>
                                        <Button
                                            size="sm"
                                            variant="ghost"
                                            class="h-8 w-8 p-0 text-destructive hover:bg-destructive/10"
                                            @click="confirmDelete(vehiculo)"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="vehiculos.data.length === 0">
                                <td colspan="4" class="p-8 text-center text-muted-foreground">
                                    No se encontraron vehículos registrados.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>

        <!-- Modal de Crear / Editar -->
        <Dialog :open="isModalOpen" @update:open="isModalOpen = $event">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>{{ isEditing ? 'Editar Vehículo' : 'Registrar Nuevo Vehículo' }}</DialogTitle>
                    <DialogDescription>
                        Ingrese las especificaciones y estatus de la unidad de transporte.
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-4 py-2">
                    <div class="space-y-1.5">
                        <Label for="tipo">Tipo de Vehículo *</Label>
                        <Input
                            id="tipo"
                            v-model="form.tipo"
                            placeholder="Ej. Gandola, Camión Cava, Furgón 350"
                            required
                        />
                        <span v-if="form.errors.tipo" class="text-xs text-destructive">
                            {{ form.errors.tipo }}
                        </span>
                    </div>

                    <div class="space-y-1.5">
                        <Label for="placa">Placa / Matrícula *</Label>
                        <Input
                            id="placa"
                            v-model="form.placa"
                            placeholder="Ej. A12B34C"
                            required
                        />
                        <span v-if="form.errors.placa" class="text-xs text-destructive">
                            {{ form.errors.placa }}
                        </span>
                    </div>

                    <div class="space-y-1.5">
                        <Label for="estatus">Estatus Operativo *</Label>
                        <select
                            id="estatus"
                            v-model="form.estatus"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm"
                        >
                            <option value="Operativo">Operativo</option>
                            <option value="Mantenimiento">En Mantenimiento</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                        <span v-if="form.errors.estatus" class="text-xs text-destructive">
                            {{ form.errors.estatus }}
                        </span>
                    </div>

                    <DialogFooter class="pt-4">
                        <Button type="button" variant="outline" @click="isModalOpen = false">
                            Cancelar
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ isEditing ? 'Guardar Cambios' : 'Registrar Vehículo' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Modal de Confirmación de Eliminación -->
        <Dialog :open="isDeleteModalOpen" @update:open="isDeleteModalOpen = $event">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Eliminar Vehículo</DialogTitle>
                    <DialogDescription>
                        ¿Está seguro que desea eliminar la unidad
                        <strong class="text-foreground">{{ vehiculoToDelete?.tipo }} (Placa: {{ vehiculoToDelete?.placa }})</strong>? Esta acción no se puede deshacer.
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter class="gap-2 sm:gap-0">
                    <Button variant="outline" @click="isDeleteModalOpen = false">
                        Cancelar
                    </Button>
                    <Button variant="destructive" @click="deleteVehiculo">
                        Eliminar
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
