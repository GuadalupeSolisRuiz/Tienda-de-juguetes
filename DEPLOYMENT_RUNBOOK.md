# Guía de Despliegue / Runbook — Tienda de Juguetes (Toys Nova)

Manual operativo y guía paso a paso para el despliegue en entornos de desarrollo, pruebas y producción de la plataforma web **Toys Nova**.

---

## 📋 1. Información General de la Arquitectura

| Componente | Tecnología | Versión Mínima Requerida |
| :--- | :--- | :--- |
| **Lenguaje Backend** | PHP Native (Patrón Modular) | 8.0 o superior |
| **Base de Datos** | MySQL / MariaDB | MySQL 5.7+ / MariaDB 10.3+ |
| **Servidor Web** | Apache / Nginx / IIS | Apache 2.4+ (`mod_rewrite` habilitado) |
| **Frontend UI** | HTML5, Vanilla JavaScript (ES6+), CSS3 | Bootstrap 5.3.3, Bootstrap Icons 1.11 |
| **Optimización de Imágenes** | GD Library / Imagick | GD con soporte WebP |
| **Caché / PWA** | Service Worker, LocalStorage API | HTML5 Browser Compatibility |

---

## 🛠️ 2. Prerrequisitos e Instalación de Dependencias

### 2.1 Requisitos de Servidor PHP
Asegúrate de que las siguientes extensiones estén habilitadas en el archivo `php.ini`:
```ini
extension=mysqli
extension=gd
extension=fileinfo
extension=json
extension=session
extension=mbstring
```

### 2.2 Verificación de Extensión GD para conversión WebP
Ejecuta el siguiente comando en terminal o CLI para verificar que el soporte WebP está activo:
```bash
php -r "var_dump(gd_info());"
```
*Debe retornar `"WebP Support" => true`.*

---

## 🗄️ 3. Configuración de la Base de Datos

### 3.1 Creación de la Base de Datos
Crea la base de datos en el servidor MySQL con codificación `utf8mb4`:
```sql
CREATE DATABASE IF NOT EXISTS tienda_virtual CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3.2 Importación del Esquema Inicial
Importa el script SQL incluido en el proyecto (`BD/tienda_virtual.sql`):

- **Vía Línea de Comandos (CLI)**:
  ```bash
  mysql -u root -p tienda_virtual < BD/tienda_virtual.sql
  ```
- **Vía phpMyAdmin**:
  1. Accede a `http://localhost/phpmyadmin`.
  2. Selecciona la base de datos `tienda_virtual`.
  3. Ve a la pestaña **Importar**, selecciona `BD/tienda_virtual.sql` y haz clic en **Ejecutar**.

### 3.3 Estructura de Tablas Clave
- `usuarios`: Registra clientes, editores y administradores (`id_rol`: 1=Cliente, 2=Editor, 3=Administrador, 4=Inactivo).
- `productos`: Catálogo de juguetes, stock, precio, categoría e imágenes JSON (`vistas`).
- `pedidos`: Cabecera de compras, total, fecha, método de pago y estado (`id_estado`: 1=Completado, 2=Pendiente, 3=Cancelado).
- `detalle_pedidos`: Ítems asociados a cada pedido con cantidades y subtotales.

---

## ⚙️ 4. Configuración del Archivo de Conexión (`include/conect.php`)

Abre el archivo [include/conect.php](file:///c:/xampp/htdocs/Tienda-de-juguetes/include/conect.php) y configura las credenciales correspondientes a tu entorno de despliegue:

```php
<?php
$servidor   = "localhost";
$usuario    = "root";           // Usuario de BD en producción
$contrasena = "";               // Contraseña de BD en producción
$bd         = "tienda_virtual";

$conexion = new mysqli($servidor, $usuario, $contrasena, $bd);

if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");
?>
```

---

## 📁 5. Permisos de Archivos y Carpetas (Servidores Linux/Unix)

Asigna los permisos necesarios para la carga y conversión automática de imágenes en formato `.webp`:

```bash
# Asignar propietario web (ejemplo www-data o apache)
sudo chown -R www-data:www-data /var/www/html/Tienda-de-juguetes

# Permisos de carpetas estándar
find /var/www/html/Tienda-de-juguetes -type d -exec chmod 755 {} \;
find /var/www/html/Tienda-de-juguetes -type f -exec chmod 644 {} \;

# Permisos de escritura para almacenamiento de imágenes multimedia
chmod -R 777 /var/www/html/Tienda-de-juguetes/Juguetes
chmod -R 777 /var/www/html/Tienda-de-juguetes/Avatares
```

---

## 🧪 6. Checklist de Verificación Previa al Despliegue (Pre-flight Checklist)

- [ ] **Base de Datos**: Verificar que existan los roles iniciales (`1=Cliente, 2=Editor, 3=Administrador, 4=Inactivo`) y los métodos de pago (`1=Efectivo, 2=Tarjeta`).
- [ ] **Usuario Administrador Pruebas/Producción**: Asegurar la existencia de al menos un usuario administrador activo.
- [ ] **Compresión WebP**: Validar que la subida de un nuevo producto convierta la imagen a `.webp` en la carpeta `/Juguetes/`.
- [ ] **Impresión de Ticket**: Validar que la función de impresión abra la ventana limpia de 1 sola página exacta sin hojas secundarias en blanco.
- [ ] **Persistencia de Carrito**: Confirmar que los artículos del carrito se guarden con la clave `toyStoreCart_user_{id}` al iniciar sesión.

---

## 🚀 7. Procedimiento de Despliegue (Paso a Paso)

### Paso 1: Clonación / Copia de Código
Sube los archivos del proyecto al directorio público del servidor web (`public_html` / `htdocs`):
```bash
git clone https://github.com/GuadalupeSolisRuiz/Tienda-de-juguetes.git /var/www/html/Tienda-de-juguetes
```

### Paso 2: Creación e Importación de Base de Datos
Ejecuta la importación del script `BD/tienda_virtual.sql`.

### Paso 3: Ajuste de Conexión PHP
Edita `include/conect.php` con los datos del servidor de producción.

### Paso 4: Ajuste de Permisos Multimedia
Asegura que la carpeta `/Juguetes/` tenga permisos de lectura y escritura.

### Paso 5: Prueba de Funcionalidades
1. Accede a `http://tu-dominio/index.php`.
2. Realiza una prueba de inicio de sesión y compra simulada en efectivo o tarjeta.
3. Verifica la generación del ticket de compra y su impresión en 1 página.
4. Accede al panel `/gestion.php` con usuario Administrador para validar la gestión de productos y tickets.

---

## 🔄 8. Plan de Reversión (Rollback Plan)

En caso de fallos críticos durante el despliegue:

1. **Restaurar Base de Datos**:
   ```bash
   mysql -u root -p tienda_virtual < backup_anterior.sql
   ```
2. **Restaurar Código Fuente**:
   ```bash
   git reset --hard HEAD~1
   ```
3. **Limpieza de Caché del Navegador**:
   Notificar a los usuarios actualizar mediante `Ctrl + F5` para recargar los scripts de Service Worker (`sw.js`).

---

## 🛡️ 9. Buenas Prácticas de Seguridad en Producción

1. **Forzar HTTPS con SSL/TLS**: Configurar redirección de HTTP a HTTPS en `.htaccess` o configuración virtual host de Apache.
2. **Ocultar Errores PHP Directos**: En producción, ajustar en `php.ini`:
   ```ini
   display_errors = Off
   log_errors = On
   error_log = /var/log/php_errors.log
   ```
3. **Protección de Archivos Sensibles**: Verificar que `.gitignore` impida la publicación de credenciales o archivos de entorno.
