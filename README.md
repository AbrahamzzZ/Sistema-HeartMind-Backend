# Sistema HeartMind Backend

## Descripción

Este repositorio contiene el backend del proyecto Sistema HeartMind. La aplicación está desarrollada en PHP y se ejecuta dentro de un contenedor Docker que se conecta a una base de datos MySQL. El backend expone una API REST para gestionar usuarios, evaluaciones de riesgo, cuestionarios, contenidos educativos y el historial de resultados.

## Arquitectura del backend

El backend sigue una arquitectura modular con capas bien definidas:

- `api/routes/` - Define los endpoints y la lógica de enrutamiento HTTP.
- `api/controllers/` - Controladores que reciben la petición, procesan datos y devuelven la respuesta JSON.
- `api/services/` - Lógica de negocio, validaciones y orquestación entre repositorios y modelos.
- `api/repositories/` - Acceso a la base de datos mediante PDO.
- `api/models/` - Modelos de datos simples que representan entidades del dominio.
- `api/validator/` - Validaciones de campos y reglas de negocio.
- `api/middleware/` - Autorización y autenticación JWT.
- `api/helpers/` - Funciones de ayuda, como generación y validación de tokens.

## Módulos principales

### Usuarios
- Registro de usuario.
- Inicio de sesión.
- Consulta del perfil mediante token JWT.
- Generación de token JWT con rol y usuario.

### Evaluaciones de riesgo
- Guardado de evaluaciones de riesgo.
- Obtención del historial de evaluaciones por usuario.
- Endpoint protegido por token.

### Cuestionarios
- Consulta de cuestionarios y cuestionarios completos.
- Resolución de cuestionarios.
- Historial de cuestionarios por usuario.
- Administración de cuestionarios, preguntas y opciones (solo admin).

### Contenidos educativos
- Consulta de contenidos educativos.
- Gestión de contenidos (crear, editar, eliminar) reservada a admin.

## Herramientas utilizadas

- PHP 8.x (contenedor Docker con Apache/PHP)
- MySQL 8.0
- Docker / Docker Compose
- phpMyAdmin para administración de la base de datos
- Firebase PHP-JWT para autenticación JWT
- PDO para conexión con MySQL

## Estructura del proyecto

```
Backend/
├── api/
│   ├── controllers/
│   ├── helpers/
│   ├── middleware/
│   ├── models/
│   ├── repositories/
│   ├── routes/
│   ├── services/
│   ├── validator/
│   └── public/index.php
├── db/
│   └── Db_cardiovascular.sql
├── docker-compose.yml
├── .env
└── README.md
```

## Variables y configuración

El contenedor PHP utiliza variables de entorno definidas en `docker-compose.yml`:

- `DB_HOST=mysql`
- `DB_NAME=cardiovascular_db`
- `DB_USER=root`
- `DB_PASSWORD=root123`
- `JWT_SECRET=HeartMindSecretKey2026SuperSecureJWT123456789`

## Clonar el repositorio

```bash
git clone https://github.com/AbrahamzzZ/Sistema-HeartMind-Backend 
cd "Sistema-HeartMind-Backend"
```

## Levantar el proyecto con Docker

Desde la raíz del proyecto:

```bash
docker compose up -d
```

Si tu versión de Docker utiliza `docker-compose` en lugar de `docker compose`:

```bash
docker-compose up -d
```

### Comandos útiles

- Ver contenedores activos:
  ```bash
docker compose ps
```
- Ver logs del backend:
  ```bash
docker compose logs -f php
```
- Detener los contenedores:
  ```bash
docker compose down
```
- Reconstruir y levantar:
  ```bash
docker compose up -d --build
```

## Acceso a servicios

- API backend: `http://localhost:8082`
- phpMyAdmin: `http://localhost:8081`
- MySQL: `localhost:3306`

## Inicializar la base de datos

El volumen `./db` está montado en el contenedor MySQL como `docker-entrypoint-initdb.d`, por lo que el archivo SQL `db/Db_cardiovascular.sql` se ejecuta automáticamente al levantar el contenedor por primera vez.

## Uso del token y permisos

- Los endpoints públicos son:
  - Registro de usuario
  - Login
- El resto de endpoints requieren token JWT en el encabezado `Authorization: Bearer <token>`.
- El rol `Administrador` tiene acceso a todos los endpoints.
- El rol `Usuario` solo puede:
  - ver su perfil
  - realizar y consultar evaluaciones
  - consultar contenidos educativos
  - consultar y resolver cuestionarios
  - consultar su historial de cuestionarios
- Los usuarios normales no pueden crear/editar/eliminar contenidos, cuestionarios, preguntas u opciones.

## Notas finales

Asegúrate de tener Docker instalado y funcionando. Si necesitas cambiar credenciales o el secreto JWT, actualiza el archivo `docker-compose.yml` y vuelve a levantar los contenedores.
