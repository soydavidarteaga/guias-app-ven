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
import { UserCheck, Plus, Search, Pencil, Trash2, Phone, CreditCard } from '@lucide/vue';

defineOptions({
    layout: AppLayout,
});

interface Conductor {
    id: number;
    nombre_completo: string;
    cedula: string;
    telefono: string | null;
}

const props = defineProps<{
    conductores: {
        data: Conductor[];
        links: any[];
    };
    filters: {
        search?: string;
    };
}>();

const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get(
        '/conductores',
        { search: value },
        { preserveState: true, replace: true }
    );
});

// Modal State (Create / Edit)
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingId = ref<number | null>(null);

const form = useForm({
    nombre_completo: '',
    cedula: '',
    telefono: '',
});

const openCreateModal = () => {
    form.reset();
    form.clearErrors();
    isEditing.value = false;
    editingId.value = null;
    isModalOpen.value = true;
};

const openEditModal = (conductor: Conductor) => {
    form.clearErrors();
    isEditing.value = true;
    editingId.value = conductor.id;

    form.nombre_completo = conductor.nombre_completo || '';
    form.cedula = conductor.cedula || '';
    form.telefono = conductor.telefono || '';

    isModalOpen.value = true;
};

const submitForm = () => {
    if (isEditing.value && editingId.value) {
        form.put(`/conductores/${editingId.value}`, {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.post('/conductores', {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    }
};

// Delete State
const isDeleteModalOpen = ref(false);
const conductorToDelete = ref<Conductor | null>(null);

const confirmDelete = (conductor: Conductor) => {
    conductorToDelete.value = conductor;
    isDeleteModalOpen.value = true;
};

const deleteConductor = () => {
    if (conductorToDelete.value) {
        router.delete(`/conductores/${conductorToDelete.value.id}`, {
            onSuccess: () => {
                isDeleteModalOpen.value = false;
                conductorToDelete.value = null;
            },
        });
    }
};
</script>

<template>
    <Head title="Gestión de Conductores - SICA" />

    <div class="space-y-6 p-6 max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Gestión de Conductores Autorizados</h1>
                <p class="text-sm text-muted-foreground">
                    Administre la lista de choferes registrados para la emisión de guías de despacho.
                </p>
            </div>
            <Button @click="openCreateModal" class="gap-2">
                <Plus class="h-4 w-4" />
                Registrar Nuevo Conductor
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
                            placeholder="Buscar por Nombre, Cédula o Teléfono..."
                            class="pl-9"
                        />
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto rounded-md border">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-muted/50 text-xs font-semibold uppercase text-muted-foreground border-b">
                            <tr>
                                <th class="p-3">Nombre Completo</th>
                                <th class="p-3">Cédula de Identidad</th>
                                <th class="p-3">Teléfono de Contacto</th>
                                <th class="p-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="conductor in conductores.data" :key="conductor.id" class="hover:bg-muted/30 transition">
                                <td class="p-3 font-medium flex items-center gap-2">
                                    <UserCheck class="h-4 w-4 text-primary shrink-0" />
                                    {{ conductor.nombre_completo }}
                                </td>
                                <td class="p-3 font-mono text-xs">
                                    <div class="flex items-center gap-1">
                                        <CreditCard class="h-3.5 w-3.5 text-muted-foreground shrink-0" />
                                        <span>{{ conductor.cedula }}</span>
                                    </div>
                                </td>
                                <td class="p-3 text-muted-foreground text-xs">
                                    <div class="flex items-center gap-1" v-if="conductor.telefono">
                                        <Phone class="h-3.5 w-3.5 shrink-0" />
                                        <span>{{ conductor.telefono }}</span>
                                    </div>
                                    <span v-else>-</span>
                                </td>
                                <td class="p-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button
                                            size="sm"
                                            variant="ghost"
                                            class="h-8 w-8 p-0"
                                            @click="openEditModal(conductor)"
                                        >
                                            <Pencil class="h-4 w-4 text-muted-foreground hover:text-primary" />
                                        </Button>
                                        <Button
                                            size="sm"
                                            variant="ghost"
                                            class="h-8 w-8 p-0 text-destructive hover:bg-destructive/10"
                                            @click="confirmDelete(conductor)"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="conductores.data.length === 0">
                                <td colspan="4" class="p-8 text-center text-muted-foreground">
                                    No se encontraron conductores registrados.
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
                    <DialogTitle>{{ isEditing ? 'Editar Conductor' : 'Registrar Nuevo Conductor' }}</DialogTitle>
                    <DialogDescription>
                        Ingrese los datos personales y de contacto del chofer autorizador.
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-4 py-2">
                    <div class="space-y-1.5">
                        <Label for="nombre_completo">Nombre Completo *</Label>
                        <Input
                            id="nombre_completo"
                            v-model="form.nombre_completo"
                            placeholder="Ej. Pedro Rodríguez"
                            required
                        />
                        <span v-if="form.errors.nombre_completo" class="text-xs text-destructive">
                            {{ form.errors.nombre_completo }}
                        </span>
                    </div>

                    <div class="space-y-1.5">
                        <Label for="cedula">Cédula de Identidad *</Label>
                        <Input
                            id="cedula"
                            v-model="form.cedula"
                            placeholder="Ej. V-15000000"
                            required
                        />
                        <span v-if="form.errors.cedula" class="text-xs text-destructive">
                            {{ form.errors.cedula }}
                        </span>
                    </div>

                    <div class="space-y-1.5">
                        <Label for="telefono">Teléfono de Contacto</Label>
                        <Input
                            id="telefono"
                            v-model="form.telefono"
                            placeholder="Ej. 0412-9998877"
                        />
                    </div>

                    <DialogFooter class="pt-4">
                        <Button type="button" variant="outline" @click="isModalOpen = false">
                            Cancelar
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ isEditing ? 'Guardar Cambios' : 'Registrar Conductor' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Modal de Confirmación de Eliminación -->
        <Dialog :open="isDeleteModalOpen" @update:open="isDeleteModalOpen = $event">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Eliminar Conductor</DialogTitle>
                    <DialogDescription>
                        ¿Está seguro que desea eliminar al conductor
                        <strong class="text-foreground">{{ conductorToDelete?.nombre_completo }}</strong>? Esta acción no se puede deshacer.
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter class="gap-2 sm:gap-0">
                    <Button variant="outline" @click="isDeleteModalOpen = false">
                        Cancelar
                    </Button>
                    <Button variant="destructive" @click="deleteConductor">
                        Eliminar
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
