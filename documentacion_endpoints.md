# Guía de Uso y Endpoints de la API

Esta guía detalla los endpoints disponibles en la API del sistema de tickets de soporte, organizados por componentes, junto con los métodos HTTP correspondientes, URLs y los payloads en formato JSON requeridos para realizar pruebas en **Postman** u otros clientes HTTP.

---

## 🔒 Configuración e Identificación

Todos los endpoints protegidos requieren que se envíen las siguientes cabeceras (Headers) en cada petición HTTP:

1. **Authorization**: `QuesitrixSecretSociety`
2. **Content-Type**: `application/json`

> [!NOTE]
> La URL Base por defecto del servidor local en XAMPP es:
> `http://localhost/proyectofinalapi/public/api`

---

## 1. Autenticación (Público)

Endpoints para inicio de sesión y gestión de credenciales. No requieren cabecera `Authorization`.

### 🔑 Iniciar Sesión (Login)
* **Método**: `POST`
* **Ruta**: `/auth/login`
* **JSON Body**:
```json
{
  "email": "admin@empresa.com",
  "password": "admin"
}
```

### 🔄 Restablecer Contraseña
* **Método**: `POST`
* **Ruta**: `/auth/restablecer-contrasena`
* **JSON Body**:
```json
{
  "email": "admin@empresa.com",
  "password": "nueva_contrasena_segura",
  "updated_by": 1
}
```

---

## 2. Gestión de Tickets (CRUD)

Endpoints para el control principal de tickets de soporte. Al crear o modificar el estado de un ticket, el sistema registra automáticamente el movimiento en la tabla de historial.

### ➕ Crear Ticket
* **Método**: `POST`
* **Ruta**: `/tickets`
* **JSON Body**:
```json
{
  "titulo": "Falla en impresora de recepción",
  "descripcion": "La impresora no enciende y muestra un error de hardware en el panel frontal.",
  "estado_ticket_id": 1,
  "prioridad_ticket_id": 2,
  "categoria_ticket_id": 1,
  "created_by": 1
}
```

### 📝 Actualizar Ticket / Cambiar Estado
* **Método**: `PUT`
* **Ruta**: `/tickets/{id}`
* **JSON Body**:
```json
{
  "titulo": "Falla en impresora de recepción - Actualizado",
  "descripcion": "La impresora no enciende y muestra error de hardware (código de error 404).",
  "estado_ticket_id": 2,
  "prioridad_ticket_id": 2,
  "categoria_ticket_id": 1,
  "updated_by": 1
}
```

### 📋 Listar Tickets (Con filtros opcionales en Query String)
* **Método**: `GET`
* **Ruta**: `/tickets`
* **Filtros válidos**: `estado_ticket_id`, `prioridad_ticket_id`, `categoria_ticket_id`, `created_by`, `estado`
* **Ejemplo**: `/tickets?categoria_ticket_id=1&estado_ticket_id=2`

### 🔍 Obtener un Ticket Específico
* **Método**: `GET`
* **Ruta**: `/tickets/{id}`

### ❌ Eliminar Ticket (Borrado Lógico)
* **Método**: `DELETE`
* **Ruta**: `/tickets/{id}?updated_by={usuario_id}`

---

## 3. Comentarios de Tickets

### 💬 Agregar Comentario a un Ticket
* **Método**: `POST`
* **Ruta**: `/tickets/{ticket_id}/comentarios`
* **JSON Body**:
```json
{
  "ticket_id": 1,
  "usuario_id": 1,
  "descripcion": "He revisado la conexión de la impresora y los cables están en orden. Procedo a pedir soporte técnico.",
  "created_by": 1
}
```

### 📋 Obtener Comentarios de un Ticket
* **Método**: `GET`
* **Ruta**: `/tickets/{ticket_id}/comentarios`

---

## 4. Historial de Tickets (Auditoría de Cambios)

Registra de forma automática cada vez que se crea un ticket o cambia de estado (`estado_ticket_id`).

### 📜 Obtener Todo el Historial
* **Método**: `GET`
* **Ruta**: `/historial-tickets`

### 🔍 Obtener Historial de un Ticket Específico
* **Método**: `GET`
* **Ruta**: `/tickets/{ticket_id}/historial`

---

## 5. CRUD de Asignaciones de Tickets

Endpoints para gestionar manualmente qué agentes están encargados de resolver qué tickets.

### ➕ Crear Asignación Manual
* **Método**: `POST`
* **Ruta**: `/asignaciones`
* **JSON Body**:
```json
{
  "ticket_id": 1,
  "agente_id": 1,
  "created_by": 1
}
```

### 🔄 Actualizar Asignación (Reasignar Agente o Ticket)
* **Método**: `PUT`
* **Ruta**: `/asignaciones/{id}`
* **JSON Body**:
```json
{
  "ticket_id": 1,
  "agente_id": 2,
  "updated_by": 1
}
```

### 📋 Listar Todas las Asignaciones
* **Método**: `GET`
* **Ruta**: `/asignaciones`

### 🔍 Obtener una Asignación Específica
* **Método**: `GET`
* **Ruta**: `/asignaciones/{id}`

### ❌ Eliminar Asignación (Borrado Físico)
* **Método**: `DELETE`
* **Ruta**: `/asignaciones/{id}`

---

## 6. Creación de Usuarios y Agentes

### 👤 Crear un Usuario Regular (Empleado)
* **Método**: `POST`
* **Ruta**: `/usuarios`
* **JSON Body**:
```json
{
  "nombre": "Juan",
  "apellido": "Pérez",
  "email": "juan.perez@empresa.com",
  "telefono": "502-55551234",
  "rol_id": 3,
  "departamento_id": 2,
  "password": "password123",
  "created_by": 1
}
```

### 🛠️ Crear un Usuario con Rol de Agente
* **Método**: `POST`
* **Ruta**: `/usuarios`
* **JSON Body**:
```json
{
  "nombre": "Sofía",
  "apellido": "Martínez",
  "email": "sofia.agente@empresa.com",
  "telefono": "502-98765432",
  "rol_id": 2,
  "departamento_id": 3,
  "password": "agente_password",
  "categoria_ticket_id": [1, 2],
  "created_by": 1
}
```
