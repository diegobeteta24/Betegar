## CI/CD notes

- Deploy workflow builds assets with Node 20 and uploads `public/build/` as an artifact.
- If build step fails with `npm ci` not found or lockfile errors, we use `npm install` to be compatible with the current repo state (no `package-lock.json`).
- Secrets required: `SSH_HOST`, `SSH_USER`, `SSH_KEY`, `APP_DIR`, `APP_URL` (and optionally `SSH_PORT`).

### Tailwind + WireUI in CI

- `tailwind.config.js` loads the WireUI preset only if `vendor/wireui/wireui/tailwind.config.js` exists.
- This avoids build failures in CI where vendor is not present during the asset build stage.

### Flowbite note

- We pinned Flowbite to v2 and disabled its Tailwind plugin in `tailwind.config.js` for CI stability.
- Re-enable by uncommenting the import and plugin entry once the Tailwind/Flowbite versions are aligned or when using Tailwind v4 and Flowbite v3+ with the new directives.

# Betegar - Sistema de Gestión Empresarial

Betegar es un sistema integral de gestión empresarial diseñado para optimizar y automatizar los procesos administrativos de su negocio. Esta plataforma web moderna proporciona herramientas completas para el manejo de inventarios, ventas, compras, finanzas, órdenes de trabajo y gestión de técnicos en campo.

## Descripción General

Betegar es una solución robusta y escalable que centraliza todas las operaciones comerciales de su empresa en una sola plataforma. El sistema está diseñado para facilitar la toma de decisiones estratégicas mediante el acceso inmediato a información actualizada y reportes detallados. Con capacidades avanzadas de gestión de servicios técnicos, notificaciones en tiempo real y funcionalidad móvil PWA, Betegar se adapta tanto a operaciones de oficina como a trabajo en campo.

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
- Control de transacciones financieras con categorización
- Seguimiento de ingresos y egresos
- Categorización avanzada de gastos
- Integración de débitos bancarios como gastos
- Reportes financieros detallados
- Control de flujo de caja
- Transferencias entre cuentas
- Dashboard financiero en tiempo real

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

### 🔧 Gestión de Órdenes de Trabajo
- Sistema completo de órdenes de trabajo
- Asignación de técnicos a servicios
- Seguimiento de progreso en tiempo real
- Registro de avances con imágenes y firmas digitales
- Notificaciones push para técnicos y administradores
- Control de gastos por orden de trabajo
- Historial detallado de actividades

### 👨‍🔧 Gestión de Técnicos
- Sesiones de trabajo y seguimiento de ubicación
- Registro de gastos de campo
- Asignación automática de órdenes de trabajo
- Notificaciones en tiempo real
- Control de horas trabajadas
- Reportes de productividad

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
- Aplicación Web Progresiva (PWA) con funcionalidad offline
- Notificaciones push en tiempo real
- Interfaz web responsiva optimizada para móviles
- Acceso desde cualquier dispositivo
- Instalable como aplicación móvil
- Sincronización en tiempo real
- Service Worker para cacheo inteligente
- Backup automático de datos
- Seguridad avanzada con autenticación 2FA
- Captura de firmas digitales
- Geolocalización para técnicos

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
- Configure las notificaciones push para móviles (opcional)

### Instalación como PWA (Aplicación Móvil)

Betegar puede instalarse como una aplicación móvil nativa:

**En dispositivos móviles:**
1. Abra el sistema en su navegador móvil
2. Busque la opción "Añadir a pantalla de inicio" o "Instalar aplicación"
3. Confirme la instalación

**En computadoras:**
1. Abra el sistema en Chrome, Edge o Safari
2. Busque el ícono de instalación en la barra de direcciones
3. Haga clic en "Instalar Betegar"

La aplicación instalada funcionará offline y recibirá notificaciones push.

## Uso del Sistema

### Configuración Inicial
1. **Primer Acceso**: Acceda al sistema con las credenciales de administrador predeterminadas
2. **Configuración Inicial**: Complete la configuración de la empresa, almacenes y categorías
3. **Importación de Datos**: Utilice las herramientas de importación para cargar productos y clientes
4. **Configuración de Técnicos**: Registre técnicos y configure permisos de acceso

### Operación Diaria
1. **Gestión de Inventarios**: Registre entradas, salidas y transferencias de productos
2. **Procesamiento de Ventas**: Genere cotizaciones, procese ventas y gestione pagos
3. **Órdenes de Trabajo**: Cree, asigne y dé seguimiento a órdenes de servicio
4. **Control Financiero**: Registre transacciones bancarias y categorice gastos
5. **Seguimiento de Técnicos**: Monitoree el progreso de servicios en campo

### Funcionalidades Móviles
- **Aplicación PWA**: Instale la aplicación en dispositivos móviles
- **Trabajo Offline**: Continúe trabajando sin conexión a internet
- **Notificaciones Push**: Reciba alertas importantes en tiempo real
- **Firmas Digitales**: Capture firmas de clientes directamente en dispositivos móviles

## Soporte y Documentación

Para obtener ayuda adicional o reportar problemas, contacte al equipo de desarrollo o consulte la documentación técnica incluida en el sistema.

## Créditos

**Betegar** ha sido desarrollado por **Diego Beteta**.

Sistema diseñado para optimizar la gestión empresarial mediante tecnología moderna y una interfaz intuitiva.

---

© 2025 Diego Beteta. Todos los derechos reservados.
