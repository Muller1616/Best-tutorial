-- Create database

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

-- Subjects table
CREATE TABLE IF NOT EXISTS subjects (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    PRIMARY KEY (id)
);

-- Tutorials table
CREATE TABLE IF NOT EXISTS tutorials (
    id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    subject_id INT(11) NOT NULL,
    format ENUM('Online', 'In-Person') NOT NULL,
    location VARCHAR(255) DEFAULT NULL,
    file_url VARCHAR(255) DEFAULT NULL,
    upload_date DATETIME NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id)
);

-- Enrollments table
CREATE TABLE IF NOT EXISTS enrollments (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    tutorial_id INT(11) NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unique_enrollment (user_id, tutorial_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (tutorial_id) REFERENCES tutorials(id)
);

-- Insert sample data

-- Sample admin user (password: admin123)
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@example.com', '$2y$10$hKtAn8HKLhBDSzSM5X.0iu4Els2JA5mMbMG.YNWPXYdi1U7yKaKCO', 'admin');

-- Sample student user (password: student123)
INSERT INTO users (name, email, password, role) VALUES 
('John Doe', 'john@example.com', '$2y$10$cXrGEMXvcBLZOc7wP.TwGOcuA42b/x/5W0imoFLhMuXgxZo8ebxJ.', 'student');

-- Sample subjects
INSERT INTO subjects (name) VALUES 
('Mathematics'),
('Computer Science'),
('Physics'),
('Chemistry'),
('Biology');

-- Sample tutorials
INSERT INTO tutorials (title, description, subject_id, format, location, file_url, upload_date) VALUES 
('Introduction to Calculus', 'Learn the basics of calculus including limits, derivatives, and integrals.', 1, 'Online', NULL, 'https://example.com/calculus.pdf', NOW()),
('Advanced Programming Concepts', 'Explore advanced programming paradigms including object-oriented programming and functional programming.', 2, 'Online', NULL, 'https://example.com/programming.mp4', NOW()),
('Quantum Mechanics Fundamentals', 'An introduction to the principles of quantum mechanics.', 3, 'In-Person', 'Room A101, Building 3', NULL, NOW()),
('Organic Chemistry Lab', 'Hands-on laboratory session covering fundamental organic chemistry reactions.', 4, 'In-Person', 'Chemistry Lab, Building 2', NULL, NOW()),
('Cell Biology', 'Comprehensive overview of cell structure and function.', 5, 'Online', NULL, 'https://example.com/biology.pdf', NOW());

-- Sample enrollments
INSERT INTO enrollments (user_id, tutorial_id) VALUES 
(2, 1),
(2, 2);
