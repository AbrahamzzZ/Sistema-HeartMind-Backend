CREATE DATABASE IF NOT EXISTS cardiovascular_db;
USE cardiovascular_db;

-- =========================
-- USUARIOS
-- =========================

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    age INT,
    gender ENUM('M','F','Other'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- EVALUACIÓN DE RIESGO
-- =========================

CREATE TABLE risk_assessments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,

    age INT NOT NULL,
    systolic_pressure INT NOT NULL,
    diastolic_pressure INT NOT NULL,
    cholesterol_level DECIMAL(5,2),
    bmi DECIMAL(5,2),

    smoker BOOLEAN DEFAULT FALSE,
    diabetic BOOLEAN DEFAULT FALSE,

    risk_result ENUM('Bajo','Moderado','Alto') NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_risk_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
);

-- =========================
-- CONTENIDO EDUCATIVO
-- =========================

CREATE TABLE educational_contents (
    id INT AUTO_INCREMENT PRIMARY KEY,

    title VARCHAR(255) NOT NULL,
    description TEXT,

    content LONGTEXT,

    type ENUM('article','video','infographic') NOT NULL,

    url VARCHAR(500),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- QUIZZES
-- =========================

CREATE TABLE quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,

    title VARCHAR(255) NOT NULL,
    description TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- PREGUNTAS
-- =========================

CREATE TABLE quiz_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,

    quiz_id INT NOT NULL,

    question TEXT NOT NULL,

    FOREIGN KEY (quiz_id)
    REFERENCES quizzes(id)
    ON DELETE CASCADE
);

-- =========================
-- OPCIONES
-- =========================

CREATE TABLE quiz_options (
    id INT AUTO_INCREMENT PRIMARY KEY,

    question_id INT NOT NULL,

    option_text VARCHAR(255) NOT NULL,

    is_correct BOOLEAN DEFAULT FALSE,

    FOREIGN KEY (question_id)
    REFERENCES quiz_questions(id)
    ON DELETE CASCADE
);

-- =========================
-- RESULTADOS DE QUIZZES
-- =========================

CREATE TABLE quiz_results (
    id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,
    quiz_id INT NOT NULL,

    score INT NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE,

    FOREIGN KEY (quiz_id)
    REFERENCES quizzes(id)
    ON DELETE CASCADE
);

-- =========================
-- RECURSOS PREVENTIVOS
-- =========================

CREATE TABLE preventive_resources (
    id INT AUTO_INCREMENT PRIMARY KEY,

    title VARCHAR(255) NOT NULL,

    description TEXT,

    type ENUM(
        'diet',
        'exercise',
        'habit',
        'warning'
    ) NOT NULL,

    content TEXT,

    url VARCHAR(500),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);