CREATE DATABASE IF NOT EXISTS cardiovascular_db;
USE cardiovascular_db;

-- =====================================
-- USUARIOS
-- =====================================

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    rol ENUM(
        'Administrador',
        'Usuario'
    ) DEFAULT 'Usuario',
    edad INT,
    genero ENUM('M','F','Otro'),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================
-- EVALUACIONES DE RIESGO CARDIOVASCULAR
-- =====================================

CREATE TABLE evaluaciones_riesgo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    edad INT NOT NULL,
    peso DECIMAL(5,2) NOT NULL,
    altura DECIMAL(4,2) NOT NULL,
    imc DECIMAL(5,2),
    presion_sistolica INT NOT NULL,
    presion_diastolica INT NOT NULL,
    nivel_colesterol DECIMAL(5,2),
    fumador BOOLEAN DEFAULT FALSE,
    diabetico BOOLEAN DEFAULT FALSE,
    actividad_fisica BOOLEAN DEFAULT FALSE,
    antecedentes_familiares BOOLEAN DEFAULT FALSE,
    puntaje INT NOT NULL,
    resultado_riesgo ENUM(
        'Bajo',
        'Moderado',
        'Alto'
    ) NOT NULL,
    recomendaciones TEXT,
    fecha_evaluacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_evaluacion_usuario
        FOREIGN KEY (usuario_id)
        REFERENCES usuarios(id)
        ON DELETE CASCADE
);

-- =====================================
-- CONTENIDOS EDUCATIVOS
-- =====================================

CREATE TABLE contenidos_educativos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    contenido LONGTEXT,
    tipo ENUM(
        'articulo',
        'video',
        'infografia'
    ) NOT NULL,

    url VARCHAR(500),

    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================
-- CUESTIONARIOS
-- =====================================

CREATE TABLE cuestionarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================
-- PREGUNTAS DE CUESTIONARIOS
-- =====================================

CREATE TABLE preguntas_cuestionario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cuestionario_id INT NOT NULL,
    pregunta TEXT NOT NULL,

    FOREIGN KEY (cuestionario_id)
    REFERENCES cuestionarios(id)
    ON DELETE CASCADE
);

-- =====================================
-- OPCIONES DE RESPUESTA
-- =====================================

CREATE TABLE opciones_respuesta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pregunta_id INT NOT NULL,
    texto_opcion VARCHAR(255) NOT NULL,
    es_correcta BOOLEAN DEFAULT FALSE,

    FOREIGN KEY (pregunta_id)
    REFERENCES preguntas_cuestionario(id)
    ON DELETE CASCADE
);

-- =====================================
-- RESULTADOS DE AUTOEVALUACIONES
-- =====================================

CREATE TABLE resultados_cuestionario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    cuestionario_id INT NOT NULL,
    puntaje INT NOT NULL,
    fecha_realizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id)
    REFERENCES usuarios(id)
    ON DELETE CASCADE,

    FOREIGN KEY (cuestionario_id)
    REFERENCES cuestionarios(id)
    ON DELETE CASCADE
);

-- =====================================
-- RECURSOS PREVENTIVOS
-- =====================================

CREATE TABLE recursos_preventivos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    tipo ENUM(
        'alimentacion',
        'ejercicio',
        'habito_saludable',
        'advertencia'
    ) NOT NULL,

    contenido TEXT,

    url VARCHAR(500),

    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);