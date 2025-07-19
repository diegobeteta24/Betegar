# Betegar - Sistema de Gestión Empresarial

Betegar es un sistema integral de gestión empresarial diseñado para optimizar y automatizar los procesos administrativos de su negocio. Esta plataforma web moderna proporciona herramientas completas para el manejo de inventarios, ventas, compras, finanzas y administración general.

## Descripción General

Betegar es una solución robusta y escalable que centraliza todas las operaciones comerciales de su empresa en una sola plataforma. El sistema está diseñado para facilitar la toma de decisiones estratégicas mediante el acceso inmediato a información actualizada y reportes detallados.

## Características Principales

### 🏪 Gestión de Almacenes e Inventarios
- Administración de múltiples almacenes
- Control de inventarios en tiempo real
- Seguimiento de movimientos de productos (entradas y salidas)
- Sistema Kardex para control detallado de stocks
- Transferencias entre almacenes
- Alertas de stock mínimo

### 💰 Sistema Bancario y Financiero
- Gestión de cuentas bancarias múltiples
- Control de transacciones financieras
- Seguimiento de ingresos y egresos
- Categorización de gastos
- Reportes financieros detallados
- Control de flujo de caja

### 📦 Gestión de Productos y Categorías
- Catálogo completo de productos
- Organización por categorías
- Gestión de precios y costos
- Importación masiva de productos
- Imágenes y descripciones detalladas

### 🛒 Ventas y Facturación
- Generación de cotizaciones
- Procesamiento de ventas
- Gestión de pagos
- Facturación automática
- Historial completo de transacciones

### 🏭 Compras y Proveedores
- Gestión de órdenes de compra
- Administración de proveedores
- Control de recepción de mercancías
- Seguimiento de pagos a proveedores

### 👥 Gestión de Clientes y Usuarios
- Base de datos de clientes
- Historial de compras por cliente
- Sistema de usuarios con roles
- Autenticación segura con 2FA
- Control de permisos granular

### 📊 Reportes y Exportaciones
- Reportes en tiempo real
- Exportación a Excel y PDF
- Dashboards interactivos
- Análisis de tendencias
- Reportes personalizables

### 🔧 Características Técnicas
- Interfaz web responsiva (PWA)
- Acceso desde cualquier dispositivo
- Sincronización en tiempo real
- Backup automático de datos
- Seguridad avanzada

## Instalación

### Requisitos del Sistema

- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL >= 8.0 o PostgreSQL >= 13
- Servidor web (Apache/Nginx)

### Pasos de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/diegobeteta24/Betegar.git
   cd Betegar
   ```

2. **Instalar dependencias de PHP**
   ```bash
   composer install
   ```

3. **Instalar dependencias de Node.js**
   ```bash
   npm install
   ```

4. **Configurar el archivo de entorno**
   ```bash
   cp .env.example .env
   ```
   
   Edite el archivo `.env` con la configuración de su base de datos:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=betegar
   DB_USERNAME=tu_usuario
   DB_PASSWORD=tu_contraseña
   ```

5. **Generar la clave de aplicación**
   ```bash
   php artisan key:generate
   ```

6. **Ejecutar las migraciones de base de datos**
   ```bash
   php artisan migrate
   ```

7. **Generar datos de prueba (opcional)**
   ```bash
   php artisan db:seed
   ```

8. **Compilar los recursos frontend**
   ```bash
   npm run build
   ```

9. **Iniciar el servidor local**
   ```bash
   php artisan serve
   ```

El sistema estará disponible en `http://localhost:8000`

### Configuración Adicional

- Configure el servidor de correo en el archivo `.env` para el envío de notificaciones
- Ajuste los permisos de las carpetas `storage` y `bootstrap/cache`
- Configure las tareas programadas (cron jobs) para el mantenimiento automático

## Uso del Sistema

1. **Primer Acceso**: Acceda al sistema con las credenciales de administrador predeterminadas
2. **Configuración Inicial**: Complete la configuración de la empresa, almacenes y categorías
3. **Importación de Datos**: Utilice las herramientas de importación para cargar productos y clientes
4. **Operación Diaria**: Comience a registrar ventas, compras y movimientos de inventario

## Soporte y Documentación

Para obtener ayuda adicional o reportar problemas, contacte al equipo de desarrollo o consulte la documentación técnica incluida en el sistema.

## Créditos

**Betegar** ha sido desarrollado por **Diego Beteta**.

Sistema diseñado para optimizar la gestión empresarial mediante tecnología moderna y una interfaz intuitiva.

---

© 2025 Diego Beteta. Todos los derechos reservados.
