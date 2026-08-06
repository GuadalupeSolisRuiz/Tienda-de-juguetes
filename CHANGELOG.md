# Changelog — Tienda de Juguetes (Toys Nova)

Todos los cambios notables en este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.1.0/) y este proyecto se adhiere a [Semantic Versioning](https://semver.org/lang/es/).

---

## [4.0.0] - 2026-08-06

### 🚀 Añadido (Added)
- **Gestión Administrativa de Tickets de Compra**:
  - Pestaña *"Gestión de Tickets"* en el panel de `gestion.php`.
  - Endpoint `include/obtener_pedidos_admin.php` para listar recibos globales o filtrados por cliente.
  - Endpoint `include/actualizar_estado_pedido.php` para cambiar el estado (*Completado*, *Pendiente*, *Cancelado*).
  - Endpoint `include/eliminar_pedido.php` para eliminar registros de pedidos.
- **Nuevas Secciones Informativas**:
  - Sección `#nosotros` en la página principal `index.php` y modal `#nosotrosModal` en `views/navbar.php`.
  - Sección `#contacto` con formulario en `index.php` y modal `#contactoModal` en `views/navbar.php`.
- **Sección de Ofertas Especiales**:
  - Sección `#ofertas` en `index.php` con productos seleccionados en rebaja, tachado de precio original, porcentaje de ahorro y función `window.addOfferToCart()`.
- **Persistencia de Carrito Asociada a la Cuenta**:
  - Identificación por usuario en `localStorage` (`toyStoreCart_user_{userId}`).
  - Conservación automática de productos al cerrar sesión y restauración al volver a iniciar.
- **Validaciones Estrictas de Inventario**:
  - Atributos frontend (`min="0.01"`, `min="0"`, `required`) en formularios de agregar y editar productos en `gestion.php`.
  - Validaciones de servidor en PHP para evitar precios cero/negativos, stock negativo o campos requeridos vacíos.
- **Configuración de Control de Versiones**:
  - Creación del archivo `.gitignore` optimizado para proyectos PHP, XAMPP, IDEs y logs de sistema.
---
### 🛠️ Corregido (Fixed)
- **Impresión de Ticket en 1 Sola Página**:
  - Rediseño de reglas `@media print` en `style.css` usando `display: none !important` para eliminar el flujo completo de páginas secundarias en blanco.
- **Estandarización de Datos de Contacto**:
  - Reemplazo de teléfonos de prueba por el número oficial genérico `(55) 5555-5555`.
---
### 🧹 Refactorizado (Refactored)
- **Reemplazo de Métricas Simuladas por Datos Reales**:
  - Eliminación de cifras hardcodeadas (`+2,500 productos`, `+5,000 entregados`) por consultas SQL dinámicas a la base de datos en `index.php`.
- **Limpieza del Pie de Página (Footer)**:
  - Eliminación de enlaces obsoletos/inactivos (*Prensa*, *Trabaja con nosotros*, *Blog*) y enlaces a redes sociales sin destino.
  - Vinculación directa de todos los enlaces restantes hacia las secciones y modales correspondientes.
---
## [3.0.0] - 2026-08-05
### 🚀 Añadido (Added)
- **Catálogo Principal de Productos**:
  - Despliegue de productos en carrusel y cuadrículas responsivas en `index.php` y `categoria.php`.
  - Sistema de filtros de productos por categoría y ordenamiento dinámico por precio y nombre.
- **Gestión Integral del Catálogo (Panel de Administración)**:
  - Formulario para agregar nuevos juguetes al catálogo en `gestion.php`.
  - Conversión automática de imágenes de productos cargadas a formato WebP optimizado (`convertirAWebP`).
  - Edición en modal de productos existentes (nombre, categoría, precio, stock e imágenes).
  - Eliminación segura de productos con actualización en base de datos (`id_disponible`).
  - Modal de vista rápida del producto con carrusel de tres ángulos (frente, izquierda, derecha).
---
## [2.0.0] - 2026-08-04
### 🚀 Añadido (Added)
- **Reconocimiento de Roles en Navbar**:
  - Identificación del rol del usuario registrado. Muestra el distintivo de rol junto al nombre (ej. `Administrador Alex`); en clientes se muestra la salutación `Bienvenido (Usuario)`.
  - Enlace exclusivo de **"Gestión"** en el navbar visible al detectar un Administrador, permitiendo acceder directamente a la gestión de base de datos (`gestion.php`).
  - Botón de **Cerrar sesión** ubicado junto a la información del usuario en el menú desplegable del navbar.
- **Edición de Perfil & Gestión de Contraseña**:
  - Modal de edición pre-cargado con la información registrada del usuario al hacer clic en su nombre.
  - Posibilidad de modificar la contraseña directamente desde la ventana de edición de perfil.
- **Desactivación de Cuenta (`id_rol = 4`)**:
  - Botón de **Desactivar cuenta** en el modal de perfil. Requiere escribir la palabra de confirmación `DESACTIVAR`.
  - Comprobación previa para asegurar que el usuario no tenga pedidos pendientes.
  - Cambio de estado a inactivo asignando `id_rol = 4`.
- **Reactivación y Bienvenida del Oso Toys Nova**:
  - Notificación *"Bienvenido de vuelta"* cuando un usuario inactivo regresa utilizando sus credenciales.
  - Despliegue en `index.php` del modal especial de bienvenida atendido por el **Oso de Toys Nova** indicando que se le extrañaba.
- **Eliminación Definitiva de Cuenta**:
  - Botón de **Eliminar cuenta** situado debajo del de desactivación.
  - Verificación de seguridad requiriendo la contraseña actual y comprobación de ausencia de pedidos pendientes para la eliminación definitiva.
- **Panel de Gestión de Usuarios y Base de Datos (`gestion.php`)**:
  - Modificación dinámica del rol de cualquier usuario en la base de datos (Cliente, Editor, Administrador).
  - Eliminación de usuarios registrados directamente desde la base de datos.
  - Opción para crear/registrar nuevos usuarios directamente desde el panel administrativo.
  - Filtro de búsqueda de usuarios por **Rol** dentro de la base de datos.
  - Filtro de búsqueda de usuarios por **Fecha de registro**.
  - Simulación de recuperación de contraseña para usuarios.
---
## [1.0.0] - 2026-08-01
### 🚀 Añadido (Added)
- **Autenticación y Registro de Usuarios**:
  - Formulario modal e independiente de **Registro de Usuario** (`registro.php`) con encriptación bcrypt para contraseñas.
  - Validaciones de formulario frontend (longitud mínima de 8 caracteres, combinación de letras y números, coincidencia de contraseñas, aceptación de términos).
  - Proceso backend de inicio de sesión (`include/login_process.php`) con verificación de credenciales y asignación de variables de sesión (`$_SESSION['usuario_id']`, `$_SESSION['usuario_rol']`).
  - Modal de **Editar Perfil** inicial para actualización de información básica.
