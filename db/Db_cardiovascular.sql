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
    rol ENUM('Administrador','Usuario') DEFAULT 'Usuario',
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
    genero INT NOT NULL,        
    altura INT NOT NULL,                
    peso DECIMAL(5,2) NOT NULL,            
    presion_sistolica INT NOT NULL,
    presion_diastolica INT NOT NULL,
    nivel_colesterol INT NOT NULL DEFAULT 1,
    glucosa INT NOT NULL DEFAULT 1,     
    fumador BOOLEAN DEFAULT FALSE,
    alcohol BOOLEAN DEFAULT FALSE,
    actividad_fisica BOOLEAN DEFAULT FALSE,
    imc DECIMAL(5,2),
    probabilidad_riesgo DECIMAL(5,3), 
    resultado_riesgo ENUM('Bajo','Moderado','Alto') NOT NULL,
    recomendaciones TEXT,
    fecha_evaluacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_fecha (usuario_id, fecha_evaluacion)
);
-- =====================================
-- CONTENIDOS EDUCATIVOS
-- =====================================
CREATE TABLE contenidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    tipo ENUM('articulo','video','infografia') NOT NULL,
    categoria ENUM('alimentacion','ejercicio','habito_saludable','advertencia') NOT NULL,
    public_id VARCHAR(255),
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

CREATE TABLE preguntas_cuestionario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cuestionario_id INT NOT NULL,
    pregunta TEXT NOT NULL,
    FOREIGN KEY (cuestionario_id) REFERENCES cuestionarios(id) ON DELETE CASCADE
);

CREATE TABLE opciones_respuesta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pregunta_id INT NOT NULL,
    texto_opcion VARCHAR(255) NOT NULL,
    es_correcta BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (pregunta_id) REFERENCES preguntas_cuestionario(id) ON DELETE CASCADE
);

CREATE TABLE resultados_cuestionario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    cuestionario_id INT NOT NULL,
    puntaje INT NOT NULL,
    fecha_realizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (cuestionario_id) REFERENCES cuestionarios(id) ON DELETE CASCADE
);

-- =====================================
-- JUEGOS
-- =====================================
CREATE TABLE juegos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    tipo ENUM('clasifica_habitos','memoria_cardiaca','froggy_cardio') NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================
-- JUEGO SESIONES
-- =====================================
CREATE TABLE juego_sesiones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    juego_id INT NOT NULL,
    puntaje INT DEFAULT 0,
    tiempo_segundos INT DEFAULT NULL,
    completado BOOLEAN DEFAULT FALSE,
    fecha_inicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_fin TIMESTAMP NULL,

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (juego_id) REFERENCES juegos(id) ON DELETE CASCADE
);

-- =====================================
-- DETALLE DE RESULTADOS
-- =====================================
CREATE TABLE juego_resultados_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sesion_id INT NOT NULL,
    juego_id INT NOT NULL,
    item_id INT NULL,
    respuesta_usuario TEXT,
    es_correcto BOOLEAN DEFAULT NULL,

    FOREIGN KEY (sesion_id) REFERENCES juego_sesiones(id) ON DELETE CASCADE,
    FOREIGN KEY (juego_id) REFERENCES juegos(id) ON DELETE CASCADE
);

-- =====================================
-- CLASIFICA LOS HÁBITOS
-- =====================================
CREATE TABLE juego_categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    juego_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    orden INT DEFAULT 0,
    FOREIGN KEY (juego_id) REFERENCES juegos(id) ON DELETE CASCADE
);

CREATE TABLE juego_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    juego_id INT NOT NULL,
    texto VARCHAR(255) NOT NULL,
    categoria_correcta_id INT NOT NULL,
    orden INT DEFAULT 0,
    FOREIGN KEY (juego_id) REFERENCES juegos(id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_correcta_id) REFERENCES juego_categorias(id)
);

-- =====================================
-- MEMORIA CARDÍACA
-- =====================================
CREATE TABLE juego_memoria_cartas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    juego_id INT NOT NULL,
    contenido VARCHAR(255) NOT NULL,
    tipo ENUM('imagen','texto') DEFAULT 'texto',
    par_id INT NOT NULL,

    FOREIGN KEY (juego_id) REFERENCES juegos(id) ON DELETE CASCADE,
    FOREIGN KEY (par_id) REFERENCES juego_memoria_cartas(id)
);

-- =====================================
-- ÍNDICES
-- =====================================
CREATE INDEX idx_sesiones_usuario ON juego_sesiones(usuario_id);
CREATE INDEX idx_sesiones_juego ON juego_sesiones(juego_id);
CREATE INDEX idx_resultados_sesion ON juego_resultados_detalle(sesion_id);