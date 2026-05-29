# 🧸 Tienda de Juguetes Online

![Estado del proyecto](https://img.shields.io/badge/estado-en%20desarrollo-yellow)
![Licencia](https://img.shields.io/badge/licencia-MIT-blue)
![Versión](https://img.shields.io/badge/versión-1.0.0-green)

> Plataforma de e-commerce para la venta de juguetes, con catálogo interactivo, carrito de compras y panel de administración.

---

## 📋 Tabla de contenidos

- [Descripción](#-descripción)
- [Características](#-características)
- [Tecnologías](#-tecnologías)
- [Requisitos previos](#-requisitos-previos)
- [Instalación](#-instalación)
- [Uso](#-uso)
- [Estructura del proyecto](#-estructura-del-proyecto)
- [Variables de entorno](#-variables-de-entorno)
- [Scripts disponibles](#-scripts-disponibles)
- [Contribuir](#-contribuir)
- [Licencia](#-licencia)
- [Contacto](#-contacto)

---

## 📖 Descripción

**Tienda de Juguetes** es una aplicación web de comercio electrónico especializada en juguetes para todas las edades. Permite a los clientes explorar un catálogo completo, filtrar productos por categoría y edad, agregar artículos al carrito y realizar compras de manera segura. Incluye además un panel administrativo para gestionar inventario, pedidos y usuarios.

---

## ✨ Características

- 🔍 **Catálogo con búsqueda y filtros** — por categoría, rango de precios y edad recomendada
- 🛒 **Carrito de compras** — con persistencia de sesión
- 👤 **Autenticación de usuarios** — registro, inicio de sesión y perfil
- 💳 **Pasarela de pagos** — integración con Stripe / PayPal
- 📦 **Seguimiento de pedidos** — historial y estado en tiempo real
- 🛠️ **Panel de administración** — gestión de productos, categorías y órdenes
- 📱 **Diseño responsivo** — compatible con dispositivos móviles y escritorio
- ⭐ **Sistema de reseñas** — calificaciones y comentarios por producto

---

## 🛠️ Tecnologías

| Capa | Tecnología |
|------|------------|
| Frontend | HTML 5, Javascript |
| Estilos | Bootstrap, CSS3 |
| Backend | XAMPP, PHP 8 |
| Base de datos | PhpMyAdmin |
| Almacenamiento | Local |

---

## ✅ Requisitos previos

Antes de comenzar, asegúrate de tener instalado:

- [Node.js](https://nodejs.org/) v18 o superior
- [npm](https://www.npmjs.com/) v9 o superior (o `yarn`)
- [PostgreSQL](https://www.postgresql.org/) v14 o superior
- Una cuenta en [Cloudinary](https://cloudinary.com/) (para imágenes)
- Una cuenta en [Stripe](https://stripe.com/) (para pagos)

---

## 🚀 Instalación

1. **Clona el repositorio:**

```bash
git clone https://github.com/tu-usuario/toyworld.git
cd toyworld
```

2. **Instala las dependencias del backend:**

```bash
cd server
npm install
```

3. **Instala las dependencias del frontend:**

```bash
cd ../client
npm install
```

4. **Configura las variables de entorno** (ver sección [Variables de entorno](#-variables-de-entorno))

5. **Ejecuta las migraciones de la base de datos:**

```bash
cd ../server
npx prisma migrate dev
```

6. **Carga los datos de prueba (opcional):**

```bash
npx prisma db seed
```

---

## ▶️ Uso

**Modo desarrollo** — levanta el backend y el frontend simultáneamente:

```bash
# En la raíz del proyecto
npm run dev
```

O por separado:

```bash
# Backend (puerto 3001)
cd server && npm run dev

# Frontend (puerto 5173)
cd client && npm run dev
```

Abre tu navegador en `http://localhost:5173`

**Credenciales de prueba:**

| Rol | Email | Contraseña |
|-----|-------|------------|
| Admin | admin@toyworld.com | Admin1234! |
| Cliente | cliente@ejemplo.com | Cliente1234! |

---

## 📁 Estructura del proyecto

```
toyworld/
├── client/                  # Aplicación React (frontend)
│   ├── public/
│   ├── src/
│   │   ├── assets/
│   │   ├── components/      # Componentes reutilizables
│   │   ├── pages/           # Vistas principales
│   │   ├── hooks/           # Custom hooks
│   │   ├── context/         # Estado global (Context API)
│   │   ├── services/        # Llamadas a la API
│   │   └── utils/
│   └── package.json
│
├── server/                  # API REST (backend)
│   ├── src/
│   │   ├── controllers/     # Lógica de controladores
│   │   ├── routes/          # Definición de rutas
│   │   ├── middleware/      # Middlewares (auth, validación)
│   │   ├── models/          # Modelos Prisma
│   │   └── utils/
│   ├── prisma/
│   │   ├── schema.prisma
│   │   └── seed.js
│   └── package.json
│
├── .env.example
├── .gitignore
├── README.md
└── package.json             # Scripts raíz (concurrently)
```

---

## 🔐 Variables de entorno

Copia el archivo de ejemplo y completa los valores:

```bash
cp .env.example .env
```

**`.env` (backend):**

```env
# Servidor
PORT=3001
NODE_ENV=development

# Base de datos
DATABASE_URL="postgresql://usuario:contraseña@localhost:5432/toyworld_db"

# Autenticación
JWT_SECRET=tu_secreto_super_seguro
JWT_EXPIRES_IN=7d

# Cloudinary
CLOUDINARY_CLOUD_NAME=tu_cloud_name
CLOUDINARY_API_KEY=tu_api_key
CLOUDINARY_API_SECRET=tu_api_secret

# Stripe
STRIPE_SECRET_KEY=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
```

**`.env` (frontend):**

```env
VITE_API_URL=http://localhost:3001/api
VITE_STRIPE_PUBLIC_KEY=pk_test_...
```

---

## 📜 Scripts disponibles

| Comando | Descripción |
|---------|-------------|
| `npm run dev` | Inicia frontend y backend en modo desarrollo |
| `npm run build` | Genera build de producción del frontend |
| `npm run test` | Ejecuta todos los tests |
| `npm run lint` | Revisa el código con ESLint |
| `npx prisma studio` | Abre el visor de base de datos |

---

## 🤝 Contribuir

¡Las contribuciones son bienvenidas! Por favor sigue estos pasos:

1. Haz un **fork** del proyecto
2. Crea una rama para tu feature: `git checkout -b feature/nueva-funcionalidad`
3. Realiza tus cambios y haz commit: `git commit -m 'feat: agrega nueva funcionalidad'`
4. Haz push a la rama: `git push origin feature/nueva-funcionalidad`
5. Abre un **Pull Request**

> Consulta [`CONTRIBUTING.md`](./CONTRIBUTING.md) para más detalles sobre el flujo de trabajo y convenciones de código.

---

## 📄 Licencia

Este proyecto está bajo la licencia **MIT**. Consulta el archivo [`LICENSE`](./LICENSE) para más información.

---

## 📬 Contacto

**Tu Nombre** — [@tu_usuario](https://twitter.com/tu_usuario) — tuemail@ejemplo.com

🔗 Repositorio: [https://github.com/tu-usuario/toyworld](https://github.com/tu-usuario/toyworld)

---

<p align="center">Hecho con ❤️ y mucha imaginación 🎠</p>
