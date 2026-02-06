# Zin Fábrica - Panel de Administración y Sitio Web

Plataforma web para gestión y visualización de productos de bebidas sin alcohol, construida con Laravel 11, Inertia.js y Vue 3.

## 🍹 Acerca del Proyecto

Zin Fábrica es una aplicación web full-stack que combina un sitio público elegante con un potente panel de administración para gestionar productos artesanales de bebidas sin alcohol. El proyecto incluye gestión de inventario, categorías, puntos de venta y una experiencia de usuario moderna.

## ✨ Características Principales

### Panel de Administración (Autenticado)
- **Gestión de Productos**: CRUD completo con soporte para múltiples fotos y precios
- **Categorías**: Organización y clasificación de productos
- **Puntos de Venta**: Administración de locaciones con ubicación, contacto y redes sociales
- **Relaciones**: Vinculación de categorías con puntos de venta
- **Autenticación**: Sistema de login y gestión de perfiles

### Sitio Público
- **Home**: Página principal con productos destacados y presentación de marca
- **Shop**: Catálogo completo con paginación (10 productos por página)
- **Dónde Encontrarnos**: Directorio de puntos de venta con mapa de categorías disponibles
- **Contacto**: Formulario de consultas
- **Diseño Responsivo**: Experiencia optimizada para móvil, tablet y desktop

## 🛠️ Stack Tecnológico

- **Backend**: Laravel 11
- **Frontend**: Vue 3 + Inertia.js
- **Estilos**: Bootstrap 5.3 + Bootstrap Icons
- **Base de Datos**: MySQL/MariaDB
- **Build**: Vite

## 📋 Requisitos

- PHP >= 8.2
- Composer
- Node.js >= 18
- NPM o Yarn
- MySQL >= 8.0 o MariaDB

## 🚀 Instalación

1. **Clonar el repositorio**
```bash
git clone <repository-url>
cd fernando
```

2. **Instalar dependencias de PHP**
```bash
composer install
```

3. **Instalar dependencias de Node**
```bash
npm install
```

4. **Configurar variables de entorno**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configurar base de datos**

Editar `.env` con las credenciales de tu base de datos:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=zin_fabrica
DB_USERNAME=root
DB_PASSWORD=
```

6. **Ejecutar migraciones**
```bash
php artisan migrate
```

7. **Crear storage link**
```bash
php artisan storage:link
```

8. **Compilar assets**

Desarrollo:
```bash
npm run dev
```

Producción:
```bash
npm run build
```

9. **Iniciar servidor**
```bash
php artisan serve
```

La aplicación estará disponible en `http://localhost:8000`

## 📁 Estructura del Proyecto

```
app/
├── Http/Controllers/
│   ├── CategoryController.php
│   ├── ProductController.php
│   └── SalePointController.php
├── Models/
│   ├── Category.php
│   ├── Product.php
│   ├── ProductPhoto.php
│   └── SalePoint.php
database/
├── migrations/
│   ├── create_categories_table.php
│   ├── create_products_table.php
│   ├── create_product_photos_table.php
│   ├── create_sale_points_table.php
│   └── category_sale_point.php
resources/
├── css/
│   └── app.css
├── js/
│   ├── Components/
│   │   ├── ApplicationLogo.vue
│   │   ├── Dropdown.vue
│   │   └── NavLink.vue
│   ├── Layouts/
│   │   └── AuthenticatedLayout.vue
│   └── Pages/
│       ├── Welcome.vue
│       ├── Dashboard.vue
│       ├── Categories/Index.vue
│       ├── Products/Index.vue
│       ├── SalePoints/Index.vue
│       └── Shop/Index.vue
```

## 🗄️ Base de Datos

### Modelos y Relaciones

- **Product**: `belongsTo` Category, `hasMany` ProductPhoto
- **Category**: `hasMany` Products, `belongsToMany` SalePoints
- **SalePoint**: `belongsToMany` Categories
- **ProductPhoto**: `belongsTo` Product

## 🎨 Características de UI/UX

- Diseño oscuro elegante con acentos verdes (#2a4c3b)
- Componentes reutilizables con Bootstrap 5
- Iconos de Bootstrap Icons integrados
- Modales para formularios
- Validación en tiempo real
- Mensajes flash de éxito/error
- Navegación contextual según estado de autenticación

## 🔐 Autenticación

El sistema utiliza Laravel Breeze con Inertia.js para proporcionar:
- Registro de usuarios
- Login/Logout
- Recuperación de contraseña
- Gestión de perfil

## 📝 Rutas Principales

### Públicas
- `/` - Home
- `/shop` - Catálogo de productos

### Autenticadas
- `/dashboard` - Panel principal
- `/categories` - Gestión de categorías
- `/products` - Gestión de productos
- `/sale-points` - Gestión de puntos de venta
- `/profile` - Perfil de usuario

## 🤝 Contribución

Este es un proyecto personalizado para Zin Fábrica. Para contribuciones o consultas, contactar al equipo de desarrollo.

## 📄 Licencia

Este proyecto es propiedad de Zin Fábrica. Todos los derechos reservados.

