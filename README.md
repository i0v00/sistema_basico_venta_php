# 🍔 Duke's Cakes POS — Sistema de Punto de Venta para Comida Rápida

Un sistema completo, moderno, compacto y responsivo desarrollado en PHP 8.2+ con arquitectura MVC simple (sin frameworks ni dependencias externas pesadas).

---

## 🚀 Características
- **Punto de Venta (POS)**: Interfaz responsiva inspirada en kioscos de comida rápida (diseño similar a McDonald's) con búsqueda interactiva, carrito reactivo e impresión simulada de tickets.
- **Inventario & Materias Primas**: CRUD para insumos con alertas automáticas de stock mínimo.
- **Recetas por Producto**: Asocia materias primas a productos para descontar stock automáticamente al vender (activación opcional en Configuración).
- **Estadísticas**: Dashboard con ingresos del día, semana, mes y gráficos dinámicos con Chart.js.
- **Seguridad Básica**: Sanitización de salidas (`htmlspecialchars`), consultas PDO preparadas y hashing de contraseñas.

---

## 🛠️ Instalación en Laragon / XAMPP

1. **Clonar / Copiar archivos**:
   Copia todos los archivos del proyecto dentro de la carpeta pública de tu servidor (ej: `C:\laragon\www\dukes_cakes_venta\`).

2. **Base de Datos**:
   - Inicia MySQL/MariaDB.
   - Crea la base de datos `dukes_cakes_venta`.
   - Importa el archivo `database/database.sql`.

3. **Configuración (`.env`)**:
   Verifica el archivo `.env` en la raíz del proyecto para ajustar las credenciales de tu base de datos:
   ```env
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_NAME=dukes_cakes_venta
   DB_USER=root
   DB_PASS=
   SESSION_LIFETIME=1800
   ```

4. **Virtual Host (Opcional pero Recomendado)**:
   Apunta la raíz de tu servidor virtual a la carpeta `/public`.
   - En Laragon: Si la carpeta es `dukes_cakes_venta`, Laragon generará automáticamente `http://dukes_cakes_venta.test` que apunta a `/public`.

---

## 🔑 Credenciales de Acceso

- **Usuario**: `admin`
- **Contraseña**: `admin`

*(Puedes cambiar estas credenciales en cualquier momento desde el menú de Configuración del sistema).*
