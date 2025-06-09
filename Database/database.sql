


CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    tel VARCHAR(20),
    mail VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'teacher', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)ENGINE=INNODB;


CREATE TABLE teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    date_prise_fonction DATE NOT NULL,
    departement VARCHAR(100) NOT NULL,
    indice INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)ENGINE=INNODB;


CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    annee_entree YEAR NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)ENGINE=INNODB;


CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL,
    credits INT NOT NULL DEFAULT 3,
    departement VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)ENGINE=INNODB;


CREATE TABLE teacher_subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT NOT NULL,
    subject_id INT NOT NULL,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_assignment (teacher_id, subject_id)
)ENGINE=INNODB;


CREATE TABLE student_subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (student_id, subject_id)
)ENGINE=INNODB;


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
)ENGINE=INNODB;

INSERT INTO users (nom, prenom, tel, mail, password, role) VALUES
('Admin', 'System', '0123456789', 'admin@escep.edu', '$$2y$10$ypWDJiAWePz0CcsrU9mIcOhzxZEP7RcrWIa59VKJXHekIwKzTI6Y2', 'admin'),
('Issaka', 'Mounkaila', '0123456790', 'issaka.mounkaila@escep.edu', '$2y$10$pYAhmkJTE3AcrUQRtG2ZMOJy.C/1sFxhJPVagSqeX.gUCpG2UGKCW', 'teacher'),
('Assogba', 'Isaac', '0123456793', 'assogba.isaac@escep.edu', '$$2y$10$m4DtyfcoOJ/r6KV8Yt6ci.8ecw6V89qs0Yzc0/Ad7MehzjKpK3zqK', 'student'),
('André ', 'Lobit', '0123456790', 'andre.lobit@escep.edu', '$2y$10$pYAhmkJTE3AcrUQRtG2ZMOJy.C/1sFxhJPVagSqeX.gUCpG2UGKCW', 'teacher'),
('Aboubakar', 'Alhassane', '0123456793', 'aboubakar.alhassane@escep.edu', '$$2y$10$m4DtyfcoOJ/r6KV8Yt6ci.8ecw6V89qs0Yzc0/Ad7MehzjKpK3zqK', 'student');

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