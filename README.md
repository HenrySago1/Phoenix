# 🚀 Phoenix Orders - MVP (Versión 1)

**Empresa:** Phoenix Financial Group
**Equipo:** Phoenix Builders (José, Henry, Ronald, Nicole y Miguel)

---

## 🛠️ Stack Tecnológico y Versiones

Este proyecto fue desarrollado bajo una **Arquitectura N-Capas** (Presentación, Aplicación, Dominio y Datos) utilizando las siguientes tecnologías:

- **Lenguaje:** PHP 8.2+
- **Framework (Monolito):** Laravel 11.x
- **Frontend Administrativo:** Filament PHP v3
- **Base de Datos:** PostgreSQL
- **Documentación de API:** L5-Swagger (OpenAPI 3.0)
- **Control de Versiones:** Git & GitHub

---

## 📖 Cómo Levantar el Proyecto en Local (Guía para el Equipo)

Sigue estos pasos cuidadosamente para tener tu entorno local funcionando exactamente igual que en producción/desarrollo.

### 1. Requisitos Previos
Asegúrate de tener instalados:
- PHP >= 8.2
- Composer
- Node.js y NPM
- PostgreSQL (y tener PgAdmin o DBeaver para administrarlo)
- Git

### 2. Clonar el repositorio
Abre tu terminal y ejecuta:
```bash
git clone https://github.com/HenrySago1/Phoenix.git
cd Phoenix
```

### 3. Instalar Dependencias
Instala los paquetes de backend (PHP) y frontend (Node):
```bash
composer install
npm install && npm run build
```

### 4. Configurar el Entorno (.env)
Laravel necesita su archivo de configuración:
```bash
cp .env.example .env
```
Abre el archivo `.env` que se acaba de crear y **configura tu conexión a PostgreSQL**. Asegúrate de que los datos coincidan con tu servidor local:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=phoenix_orders
DB_USERNAME=postgres
DB_PASSWORD=tu_contraseña_aqui
```
*(⚠️ Importante: Crea una base de datos vacía llamada `phoenix_orders` en tu PostgreSQL antes de continuar).*

### 5. Generar la Clave de la App y Migrar la BD
```bash
php artisan key:generate
php artisan migrate --seed
```

### 6. Levantar el Servidor
```bash
php artisan serve
```

---

## 🔐 Accesos del Sistema

### 1. Panel Administrativo (Filament)
- **URL:** [http://127.0.0.1:8000/admin](http://127.0.0.1:8000/admin)
- **Usuario Admin Creado por Defecto:**
  - **Email:** `admin@phoenix.com`
  - **Password:** `admin123`

*(Nota: Si necesitas crear otro admin, ejecuta: `php artisan make:filament-user`)*

### 2. Documentación API (Swagger)
- **URL:** [http://127.0.0.1:8000/api/documentation](http://127.0.0.1:8000/api/documentation)
- Para regenerar la documentación si haces cambios en el código de los Controladores:
  `php artisan l5-swagger:generate`

---

## 🏗️ Historial de Construcción (Para Referencia del Equipo)

El proyecto fue inicializado y configurado usando los siguientes comandos (no necesitas correrlos, esto es solo para que entiendan cómo se construyó):

1. **Creación del proyecto base:**
   `composer create-project laravel/laravel Phoenix`
2. **Instalación de Filament:**
   `composer require filament/filament:"^3.2" -W`
   `php artisan filament:install --panels`
3. **Instalación de Swagger:**
   `composer require darkaonline/l5-swagger`
   `php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"`
4. **Generación de las Migraciones:**
   `php artisan make:migration create_customers_table`
   `php artisan make:migration create_products_table`
   `php artisan make:migration create_orders_table`
   `php artisan make:migration create_order_items_table`
5. **Autogeneración del CRUD visual (Filament):**
   `php artisan make:filament-resource Customer --generate --soft-deletes --view`
   `php artisan make:filament-resource Product --generate --soft-deletes --view`
   `php artisan make:filament-resource Order --generate --soft-deletes --view`
6. **Lógica de Descuento de Inventario Automático:**
   `php artisan make:observer OrderItemObserver --model=OrderItem`
7. **Configuración de Rutas de API y Documentación de Swagger (PHP 8 Attributes):**
   `php artisan install:api`
   `php artisan l5-swagger:generate` (Comando vital para compilar los cambios en la API al Swagger)

---
*Desarrollado con pasión por el equipo Phoenix Builders.*
