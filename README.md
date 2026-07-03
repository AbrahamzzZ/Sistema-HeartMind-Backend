# Sistema HeartMind Backend

## Descripción

Este repositorio contiene el backend del proyecto Sistema HeartMind. La aplicación está desarrollada en PHP y se ejecuta dentro de un contenedor Docker que se conecta a una base de datos MySQL. El backend expone una API REST para gestionar usuarios, evaluaciones de riesgo, cuestionarios, contenidos educativos, juegos interactivos (clasificación) y el historial de resultados. Además, integra un modelo supervisado de machine learning para la predicción de riesgo cardiovascular.

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

### Juegos educativos
- **Juego de clasificación**: Juego interactivo donde los usuarios clasifican síntomas o comportamientos en categorías de riesgo cardiovascular.
- Consulta de juegos disponibles.
- Registro de resultados del juego por usuario.
- Historial de desempeño en juegos.

### Modelo supervisado
- **Predicción de riesgo cardiovascular**: Modelo de machine learning entrenado con datos cardiovasculares.
- Endpoints que integran el modelo para realizar predicciones en tiempo real.
- Entrenamiento y evaluación del modelo en el módulo `ModeloSupervisado`.
- Generación de reportes de predicción personalizados.

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
│   │   ├── usuarioController.php
│   │   ├── cuestionarioController.php
│   │   ├── evaluacionRiesgoController.php
│   │   └── contenido/
│   │       ├── contenidoController.php
│   │       └── juego/  (Juego de clasificación)
│   ├── helpers/
│   ├── middleware/
│   ├── models/
│   │   ├── usuario.php
│   │   ├── cuestionario/
│   │   ├── evaluacion/
│   │   └── contenido/
│   │       └── juego/  (Modelos del juego)
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

ModeloSupervisado/
├── Entrenar/
│   ├── train.py  (Script para entrenar el modelo)
│   ├── requirements.txt
│   ├── dockerfile.train
│   └── modelos/  (Modelos entrenados guardados)
└── Prediccion/
    ├── predictor.py  (API para hacer predicciones)
    ├── requirements.txt
    ├── dockerfile
    └── modelos/  (Modelos para usar en predicción)
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
  - Ver su perfil
  - Realizar y consultar evaluaciones de riesgo
  - Consultar contenidos educativos
  - Consultar y resolver cuestionarios
  - Consultar su historial de cuestionarios
  - Jugar y consultar resultados en juegos educativos
  - Obtener predicciones del modelo de riesgo cardiovascular
- Los usuarios normales no pueden crear/editar/eliminar contenidos, cuestionarios, preguntas, opciones ni juegos.

## Modelo supervisado de machine learning

El proyecto incluye un modelo de machine learning para la predicción de riesgo cardiovascular ubicado en `ModeloSupervisado/`:

### Módulo de entrenamiento (`ModeloSupervisado/Entrenar/`)
- Script `train.py` para entrenar el modelo con datos cardiovasculares.
- Archivo `cardio_train.csv` con el dataset de entrenamiento.
- Dockerfile para ejecutar el entrenamiento en un contenedor aislado.
- Los modelos entrenados se guardan en `modelos/` para su posterior uso.

### Módulo de predicción (`ModeloSupervisado/Prediccion/`)
- Script `predictor.py` que expone una API para realizar predicciones en tiempo real.
- Utiliza los modelos entrenados para clasificar el riesgo cardiovascular de los usuarios.
- Se ejecuta en un contenedor Docker independiente.
- Integrado con el backend para proporcionar predicciones personalizadas a través de endpoints específicos.

## Notas finales

Asegúrate de tener Docker instalado y funcionando. Si necesitas cambiar credenciales o el secreto JWT, actualiza el archivo `docker-compose.yml` y vuelve a levantar los contenedores.
