Objetivo
Desarrollar un módulo/sistema en Laravel modular, escalable y mantenible para la emisión, gestión y generación de PDF de Guías de Seguimiento y Control de Productos Alimenticios (tipo SUNAGRO / SICA).

1. Módulos y Arquitectura Requerida
Crea las migraciones, modelos, relaciones, controladores y servicios agrupados en la siguiente estructura modular:

Módulo de Usuarios y Autenticación:

Gestión de usuarios con Roles y Permisos (Admin, Operador de Despacho, Transportista, Auditor).

Vinculación de usuarios a Empresas/Sucursales específicas.

Módulo de Empresas y Establecimientos (SICA/SUNAGRO):

Entidad para Empresa Despachadora (Origen) y Empresa Receptora (Destino).

Campos: Razón Social, RIF/CI, Código SICA/SUNAGRO, Persona Autorizada, Teléfonos, Ubicación (Estado, Ciudad, Parroquia) y Dirección detallada.

Módulo de Catálogo de Productos y Rubros:

Entidad Rubro: Nombre del Rubro, Código Arancelario, Unidad de Medida (ej. TN), Presentación (Sacos, Cajas, etc.), Precio Base/Regulado.

Módulo de Transporte y Flota:

Entidad Conductor: Nombre Completo, Cédula de Identidad, Teléfono.

Entidad Vehiculo: Tipo (Camión, Gandola, etc.), Placa, Estatus (Operativo/Inactivo).

Módulo de Documentos Soporte:

Facturas, Notas de Entrega (NE) y Números de Precintos/Sellos de Seguridad asociados a un despacho.

Módulo de Guías de Movilización (Core):

Entidad GuiaMovilizacion:

Nro. de Guía (Secuencia autogenerada o manual única).

Fechas: Emisión (con hora) y Vencimiento.

Relaciones: Empresa Origen, Empresa Destino, Conductor, Vehículo.

Estado de la guía: Borrador, Emitida, En Tránsito, Anulada, Completada.

Copias de visualización: Copia 01 Beneficiario, Copia 02 Transporte.

Entidad pivote/detalle GuiaItem: Rubro, Cantidad (TN), Precio Unitario, Observación.

Módulo de Seguridad, Trazabilidad y QR:

Generación de código QR con payload de verificación de la guía (URL con hash unívoco).

Registro de trazabilidad para firma/sello de autoridades y alcabalas en tránsito.

2. Especificaciones Técnicas y Entregables
Modelos y Migraciones: Crea el esquema de base de datos relacional optimizado con claves foráneas e índices para búsquedas rápidas (por Nro. de Guía, RIF o Fechas).

Seeders y Factories: Genera datos de prueba realistas para empresas de distribución, rubros alimenticios, vehículos y conductores.

Service Layer: Aísla la lógica de negocio (creación de guía, cálculo de peso total, actualización de estados) en GuiaService.

Generación de PDF: Implementa la lógica para renderizar la vista de la guía exacta usando barryvdh/laravel-dompdf o similar, respetando el formato impreso (cabecera con datos estatales, bloques numéricos, QR y pie de firma institucional).

3. Formato de Salida
Genera el código paso a paso:

Migraciones de la BD con sus relaciones (belongsTo, hasMany).

Modelos con $fillable y relaciones definidas.

Form Request para validación de creación de Guías.

GuiaController y GuiaService.

Plantilla Blade para la generación del PDF impreso.