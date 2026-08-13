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
import { Building2, Plus, Search, Pencil, Trash2, MapPin, Phone, UserCheck } from '@lucide/vue';

defineOptions({
    layout: AppLayout,
});

interface Empresa {
    id: number;
    razon_social: string;
    rif: string;
    codigo_sica: string | null;
    persona_autorizada: string | null;
    telefonos: string | null;
    estado: string | null;
    ciudad: string | null;
    parroquia: string | null;
    direccion: string | null;
}

const props = defineProps<{
    empresas: {
        data: Empresa[];
        links: any[];
    };
    filters: {
        search?: string;
    };
}>();

const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get(
        '/empresas',
        { search: value },
        { preserveState: true, replace: true }
    );
});

// Modal State (Create / Edit)
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingId = ref<number | null>(null);

const form = useForm({
    razon_social: '',
    rif: '',
    codigo_sica: '',
    persona_autorizada: '',
    telefonos: '',
    estado: '',
    ciudad: '',
    parroquia: '',
    direccion: '',
});

const openCreateModal = () => {
    form.reset();
    form.clearErrors();
    isEditing.value = false;
    editingId.value = null;
    isModalOpen.value = true;
};

const openEditModal = (empresa: Empresa) => {
    form.clearErrors();
    isEditing.value = true;
    editingId.value = empresa.id;

    form.razon_social = empresa.razon_social || '';
    form.rif = empresa.rif || '';
    form.codigo_sica = empresa.codigo_sica || '';
    form.persona_autorizada = empresa.persona_autorizada || '';
    form.telefonos = empresa.telefonos || '';
    form.estado = empresa.estado || '';
    form.ciudad = empresa.ciudad || '';
    form.parroquia = empresa.parroquia || '';
    form.direccion = empresa.direccion || '';

    isModalOpen.value = true;
};

const submitForm = () => {
    if (isEditing.value && editingId.value) {
        form.put(`/empresas/${editingId.value}`, {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.post('/empresas', {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            },
        });
    }
};

// Delete State
const isDeleteModalOpen = ref(false);
const empresaToDelete = ref<Empresa | null>(null);

const confirmDelete = (empresa: Empresa) => {
    empresaToDelete.value = empresa;
    isDeleteModalOpen.value = true;
};

const deleteEmpresa = () => {
    if (empresaToDelete.value) {
        router.delete(`/empresas/${empresaToDelete.value.id}`, {
            onSuccess: () => {
                isDeleteModalOpen.value = false;
                empresaToDelete.value = null;
            },
        });
    }
};
</script>

<template>
    <Head title="Gestión de Empresas SICA - SUNAGRO" />

    <div class="space-y-6 p-6 max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Gestión de Empresas (SICA / SUNAGRO)</h1>
                <p class="text-sm text-muted-foreground">
                    Administre los establecimientos despachadores y receptores registrados.
                </p>
            </div>
            <Button @click="openCreateModal" class="gap-2">
                <Plus class="h-4 w-4" />
                Registrar Nueva Empresa
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
                            placeholder="Buscar por Razón Social, RIF, Código SICA..."
                            class="pl-9"
                        />
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto rounded-md border">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-muted/50 text-xs font-semibold uppercase text-muted-foreground border-b">
                            <tr>
                                <th class="p-3">Razón Social</th>
                                <th class="p-3">RIF / Código SICA</th>
                                <th class="p-3">Contacto</th>
                                <th class="p-3">Ubicación</th>
                                <th class="p-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-for="empresa in empresas.data" :key="empresa.id" class="hover:bg-muted/30 transition">
                                <td class="p-3">
                                    <div class="font-semibold text-base flex items-center gap-2">
                                        <Building2 class="h-4 w-4 text-primary shrink-0" />
                                        {{ empresa.razon_social }}
                                    </div>
                                    <div class="text-xs text-muted-foreground pl-6 truncate max-w-xs" v-if="empresa.direccion">
                                        {{ empresa.direccion }}
                                    </div>
                                </td>
                                <td class="p-3 font-mono">
                                    <div class="font-medium text-xs">RIF: {{ empresa.rif }}</div>
                                    <div class="text-xs text-primary font-bold" v-if="empresa.codigo_sica">
                                        SICA: {{ empresa.codigo_sica }}
                                    </div>
                                    <div class="text-xs text-muted-foreground italic" v-else>Sin Código SICA</div>
                                </td>
                                <td class="p-3 text-xs space-y-0.5">
                                    <div class="flex items-center gap-1 font-medium text-muted-foreground" v-if="empresa.persona_autorizada">
                                        <UserCheck class="h-3.5 w-3.5 text-primary shrink-0" />
                                        <span>{{ empresa.persona_autorizada }}</span>
                                    </div>
                                    <div class="flex items-center gap-1 text-muted-foreground" v-if="empresa.telefonos">
                                        <Phone class="h-3.5 w-3.5 shrink-0" />
                                        <span>{{ empresa.telefonos }}</span>
                                    </div>
                                </td>
                                <td class="p-3 text-xs">
                                    <div class="flex items-start gap-1 text-muted-foreground">
                                        <MapPin class="h-3.5 w-3.5 shrink-0 mt-0.5" />
                                        <span>{{ empresa.ciudad || '-' }}, {{ empresa.estado || '-' }}</span>
                                    </div>
                                </td>
                                <td class="p-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button
                                            size="sm"
                                            variant="ghost"
                                            class="h-8 w-8 p-0"
                                            @click="openEditModal(empresa)"
                                        >
                                            <Pencil class="h-4 w-4 text-muted-foreground hover:text-primary" />
                                        </Button>
                                        <Button
                                            size="sm"
                                            variant="ghost"
                                            class="h-8 w-8 p-0 text-destructive hover:bg-destructive/10"
                                            @click="confirmDelete(empresa)"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="empresas.data.length === 0">
                                <td colspan="5" class="p-8 text-center text-muted-foreground">
                                    No se encontraron empresas registradas.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>

        <!-- Modal de Crear / Editar -->
        <Dialog :open="isModalOpen" @update:open="isModalOpen = $event">
            <DialogContent class="sm:max-w-xl">
                <DialogHeader>
                    <DialogTitle>{{ isEditing ? 'Editar Empresa' : 'Registrar Nueva Empresa' }}</DialogTitle>
                    <DialogDescription>
                        Ingrese la información general y fiscal de la empresa según los datos SICA.
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-4 py-2">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5 col-span-2">
                            <Label for="razon_social">Razón Social *</Label>
                            <Input
                                id="razon_social"
                                v-model="form.razon_social"
                                placeholder="Ej. Alimentos Polar C.A."
                                required
                            />
                            <span v-if="form.errors.razon_social" class="text-xs text-destructive">
                                {{ form.errors.razon_social }}
                            </span>
                        </div>

                        <div class="space-y-1.5">
                            <Label for="rif">RIF / C.I. *</Label>
                            <Input
                                id="rif"
                                v-model="form.rif"
                                placeholder="Ej. J-00001111-2"
                                required
                            />
                            <span v-if="form.errors.rif" class="text-xs text-destructive">
                                {{ form.errors.rif }}
                            </span>
                        </div>

                        <div class="space-y-1.5">
                            <Label for="codigo_sica">Código SICA / SUNAGRO</Label>
                            <Input
                                id="codigo_sica"
                                v-model="form.codigo_sica"
                                placeholder="Ej. SICA-00123"
                            />
                            <span v-if="form.errors.codigo_sica" class="text-xs text-destructive">
                                {{ form.errors.codigo_sica }}
                            </span>
                        </div>

                        <div class="space-y-1.5">
                            <Label for="persona_autorizada">Persona Autorizada</Label>
                            <Input
                                id="persona_autorizada"
                                v-model="form.persona_autorizada"
                                placeholder="Ej. Juan Pérez"
                            />
                        </div>

                        <div class="space-y-1.5">
                            <Label for="telefonos">Teléfono(s)</Label>
                            <Input
                                id="telefonos"
                                v-model="form.telefonos"
                                placeholder="Ej. 0414-1234567"
                            />
                        </div>

                        <div class="space-y-1.5">
                            <Label for="estado">Estado</Label>
                            <Input
                                id="estado"
                                v-model="form.estado"
                                placeholder="Ej. Carabobo"
                            />
                        </div>

                        <div class="space-y-1.5">
                            <Label for="ciudad">Ciudad</Label>
                            <Input
                                id="ciudad"
                                v-model="form.ciudad"
                                placeholder="Ej. Valencia"
                            />
                        </div>

                        <div class="space-y-1.5 col-span-2">
                            <Label for="direccion">Dirección Detallada</Label>
                            <Input
                                id="direccion"
                                v-model="form.direccion"
                                placeholder="Ej. Zona Industrial I, Av. Principal"
                            />
                        </div>
                    </div>

                    <DialogFooter class="pt-4">
                        <Button type="button" variant="outline" @click="isModalOpen = false">
                            Cancelar
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ isEditing ? 'Guardar Cambios' : 'Registrar Empresa' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Modal de Confirmación de Eliminación -->
        <Dialog :open="isDeleteModalOpen" @update:open="isDeleteModalOpen = $event">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Eliminar Empresa</DialogTitle>
                    <DialogDescription>
                        ¿Está seguro que desea eliminar la empresa
                        <strong class="text-foreground">{{ empresaToDelete?.razon_social }}</strong>? Esta acción no se puede deshacer.
                    </DialogDescription>
                </DialogHeader>

                <DialogFooter class="gap-2 sm:gap-0">
                    <Button variant="outline" @click="isDeleteModalOpen = false">
                        Cancelar
                    </Button>
                    <Button variant="destructive" @click="deleteEmpresa">
                        Eliminar
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
