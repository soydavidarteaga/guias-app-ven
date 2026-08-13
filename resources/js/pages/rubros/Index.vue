<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Package, Plus, Search, Pencil, Trash2 } from '@lucide/vue';

defineOptions({
    layout: AppLayout,
});

interface Rubro {
    id: number;
    nombre: string;
    codigo_arancelario: string | null;
    unidad_medida: string;
    presentacion: string | null;
    precio_base: number;
}

const props = defineProps<{
    rubros: {
        data: Rubro[];
        links: any[];
    };
    filters: {
        search?: string;
    };
}>();

const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get(
        '/rubros',
        { search: value },
        { preserveState: true, replace: true }
    );
});

// Modal State (Create / Edit)
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingId = ref<number | null>(null);

const form = useForm({
    nombre: '',
    codigo_arancelario: '',
    unidad_medida: 'TN',
    presentacion: '',
    precio_base: 0,
});

const openCreateModal = () => {
    form.reset();
    form.clearErrors();
    form.unidad_medida = 'TN';
    form.precio_base = 0;
    isEditing.value = false;
    editingId.value = null;
    isModalOpen.value = true;
};

const openEditModal = (rubro: Rubro) => {
    form.clearErrors();
    isEditing.value = true;
    editingId.value = rubro.id;

    form.nombre = rubro.nombre || '';
    form.codigo_arancelario = rubro.codigo_arancelario || '';
    form.unidad_medida = rubro.unidad_medida || 'TN';
    form.presentacion = rubro.presentacion || '';
    form.precio_base = rubro.precio_base || 0;

    isModalOpen.value = true;
};

const submitForm = () => {
    if (isEditing.value && editingId.value) {
        form.put(`/rubros/${editingId.value}`, {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.post('/rubros', {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    }
};

// Delete State
const isDeleteModalOpen = ref(false);
const rubroToDelete = ref<Rubro | null>(null);

const confirmDelete = (rubro: Rubro) => {
    rubroToDelete.value = rubro;
    isDeleteModalOpen.value = true;
};

const deleteRubro = () => {
    if (rubroToDelete.value) {
        router.delete(`/rubros/${rubroToDelete.value.id}`, {
            onSuccess: () => {
                isDeleteModalOpen.value = false;
                rubroToDelete.value = null;
            },
        });
    }
};
</script>

<template>
    <Head title="Catálogo de Rubros - SICA" />

    <div class="space-y-6 p-6 max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Catálogo de Rubros Alimenticios</h1>
                <p class="text-sm text-muted-foreground">
                    Gestione los productos regulados y rubros para la emisión de guías.
                </p>
            </div>
            <Button @click="openCreateModal" class="gap-2">
                <Plus class="h-4 w-4" />
                Registrar Nuevo Rubro
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
                            placeholder="Buscar por Nombre, Código Arancelario..."
                            class="pl-9"
                        />
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto rounded-md border">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-muted/50 text-xs font-semibold uppercase text-muted-foreground border-b">
                            <tr>
                                <th class="p-3">Nombre del Rubro</th>
                                <th class="p-3">Código Arancelario</th>
                                <th class="p-3">Presentación</th>
                                <th class="p-3">Unidad de Medida</th>
                                <th class="p-3 text-right">Precio Base Ref. ($)</th>
                                <th class="p-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="rubro in rubros.data" :key="rubro.id" class="hover:bg-muted/30 transition">
                                <td class="p-3 font-medium flex items-center gap-2">
                                    <Package class="h-4 w-4 text-primary shrink-0" />
                                    {{ rubro.nombre }}
                                </td>
                                <td class="p-3 font-mono text-xs text-muted-foreground">
                                    {{ rubro.codigo_arancelario || '-' }}
                                </td>
                                <td class="p-3 text-muted-foreground">{{ rubro.presentacion || '-' }}</td>
                                <td class="p-3 font-semibold">{{ rubro.unidad_medida }}</td>
                                <td class="p-3 text-right font-mono font-bold text-emerald-600">
                                    ${{ Number(rubro.precio_base).toFixed(2) }}
                                </td>
                                <td class="p-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button
                                            size="sm"
                                            variant="ghost"
                                            class="h-8 w-8 p-0"
                                            @click="openEditModal(rubro)"
                                        >
                                            <Pencil class="h-4 w-4 text-muted-foreground hover:text-primary" />
                                        </Button>
                                        <Button
                                            size="sm"
                                            variant="ghost"
                                            class="h-8 w-8 p-0 text-destructive hover:bg-destructive/10"
                                            @click="confirmDelete(rubro)"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="rubros.data.length === 0">
                                <td colspan="6" class="p-8 text-center text-muted-foreground">
                                    No se encontraron rubros registrados.
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
                    <DialogTitle>{{ isEditing ? 'Editar Rubro' : 'Registrar Nuevo Rubro' }}</DialogTitle>
                    <DialogDescription>
                        Complete los datos técnicos del rubro alimenticio.
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-4 py-2">
                    <div class="space-y-1.5">
                        <Label for="nombre">Nombre del Rubro *</Label>
                        <Input
                            id="nombre"
                            v-model="form.nombre"
                            placeholder="Ej. Harina de Maíz Precocida"
                            required
                        />
                        <span v-if="form.errors.nombre" class="text-xs text-destructive">
                            {{ form.errors.nombre }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <Label for="codigo_arancelario">Código Arancelario</Label>
                            <Input
                                id="codigo_arancelario"
                                v-model="form.codigo_arancelario"
                                placeholder="Ej. 1102.20.00"
                            />
                        </div>

                        <div class="space-y-1.5">
                            <Label for="unidad_medida">Unidad de Medida *</Label>
                            <select
                                id="unidad_medida"
                                v-model="form.unidad_medida"
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm"
                            >
                                <option value="TN">TN (Tonelada Metric)</option>
                                <option value="KG">KG (Kilogramo)</option>
                                <option value="Bultos">Bultos</option>
                                <option value="Sacos">Sacos</option>
                                <option value="Cajas">Cajas</option>
                                <option value="Unidades">Unidades</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <Label for="presentacion">Presentación</Label>
                            <Input
                                id="presentacion"
                                v-model="form.presentacion"
                                placeholder="Ej. Saco 24 Kg"
                            />
                        </div>

                        <div class="space-y-1.5">
                            <Label for="precio_base">Precio Base Ref. ($) *</Label>
                            <Input
                                id="precio_base"
                                type="number"
                                step="0.01"
                                v-model.number="form.precio_base"
                                placeholder="0.00"
                                required
                            />
                            <span v-if="form.errors.precio_base" class="text-xs text-destructive">
                                {{ form.errors.precio_base }}
                            </span>
                        </div>
                    </div>

                    <DialogFooter class="pt-4">
                        <Button type="button" variant="outline" @click="isModalOpen = false">
                            Cancelar
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ isEditing ? 'Guardar Cambios' : 'Registrar Rubro' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Modal de Confirmación de Eliminación -->
        <Dialog :open="isDeleteModalOpen" @update:open="isDeleteModalOpen = $event">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Eliminar Rubro</DialogTitle>
                    <DialogDescription>
                        ¿Está seguro que desea eliminar el rubro
                        <strong class="text-foreground">{{ rubroToDelete?.nombre }}</strong>? Esta acción no se puede deshacer.
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter class="gap-2 sm:gap-0">
                    <Button variant="outline" @click="isDeleteModalOpen = false">
                        Cancelar
                    </Button>
                    <Button variant="destructive" @click="deleteRubro">
                        Eliminar
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
