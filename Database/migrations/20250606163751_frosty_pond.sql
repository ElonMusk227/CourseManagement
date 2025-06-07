-- ESCEP School Management System Database Schema

CREATE DATABASE IF NOT EXISTS escep_school;
USE escep_school;

-- Users table (for authentication)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    tel VARCHAR(20),
    mail VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'teacher', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Teachers table
CREATE TABLE teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date_prise_fonction DATE NOT NULL,
    departement VARCHAR(100) NOT NULL,
    indice INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Students table
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    annee_entree YEAR NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Subjects table
CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL,
    credits INT NOT NULL DEFAULT 3,
    departement VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Teacher-Subject assignments
CREATE TABLE teacher_subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT NOT NULL,
    subject_id INT NOT NULL,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_assignment (teacher_id, subject_id)
);

-- Student-Subject enrollments
CREATE TABLE student_subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (student_id, subject_id)
);

-- Grades table
CREATE TABLE grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    teacher_id INT NOT NULL,
    note DECIMAL(4,2) NOT NULL CHECK (note >= 0 AND note <= 20),
    date_evaluation DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE
);

-- Insert sample data
INSERT INTO users (nom, prenom, tel, mail, password, role) VALUES
('Admin', 'System', '0123456789', 'admin@escep.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Dupont', 'Marie', '0123456790', 'marie.dupont@escep.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher'),
('Martin', 'Pierre', '0123456791', 'pierre.martin@escep.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher'),
('Doe', 'John', '0123456792', 'john.doe@student.escep.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('Smith', 'Jane', '0123456793', 'jane.smith@student.escep.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student');

INSERT INTO teachers (user_id, date_prise_fonction, departement, indice) VALUES
(2, '2020-09-01', 'Informatique', 350),
(3, '2018-09-01', 'Mathématiques', 380);

INSERT INTO students (user_id, annee_entree) VALUES
(4, 2023),
(5, 2022);

INSERT INTO subjects (nom, code, credits, departement) VALUES
('Programmation Web', 'PROG001', 4, 'Informatique'),
('Base de Données', 'BD001', 3, 'Informatique'),
('Mathématiques Appliquées', 'MATH001', 5, 'Mathématiques'),
('Statistiques', 'STAT001', 3, 'Mathématiques');

INSERT INTO teacher_subjects (teacher_id, subject_id) VALUES
(1, 1), (1, 2), (2, 3), (2, 4);

INSERT INTO student_subjects (student_id, subject_id) VALUES
(1, 1), (1, 2), (1, 3),
(2, 1), (2, 3), (2, 4);

INSERT INTO grades (student_id, subject_id, teacher_id, note, date_evaluation) VALUES
(1, 1, 1, 15.5, '2024-01-15'),
(1, 2, 1, 13.0, '2024-01-20'),
(2, 1, 1, 17.5, '2024-01-15'),
(2, 3, 2, 14.0, '2024-01-25');