CREATE DATABASE UniversityRegistration;
USE UniversityRegistration;




-- Drop tables in reverse order of dependency to avoid foreign key constraint issues
-- USE UniversityRegistration;
-- DROP TABLE IF EXISTS Fees;
-- DROP TABLE IF EXISTS Enrollments;
-- DROP TABLE IF EXISTS Students;
-- DROP TABLE IF EXISTS application;
-- DROP TABLE IF EXISTS Courses;
-- DROP TABLE IF EXISTS Programme;
-- DROP TABLE IF EXISTS guardian;
-- DROP TABLE IF EXISTS address;
-- DROP TABLE IF EXISTS Department;
-- DROP TABLE IF EXISTS Faculty;


CREATE TABLE Faculty (
    faculty_id INT PRIMARY KEY AUTO_INCREMENT,
    faculty_name VARCHAR(100) NOT NULL,
    date_added DATE DEFAULT CURRENT_TIMESTAMP,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE Department (
    department_id INT PRIMARY KEY AUTO_INCREMENT,
    department_name VARCHAR(100) NOT NULL,
    faculty_id INT,
    date_added DATE DEFAULT CURRENT_TIMESTAMP,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_faculty FOREIGN KEY (faculty_id) REFERENCES Faculty(faculty_id)
);

CREATE TABLE Programme (
    Programme_id INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(100) NOT NULL,
    Degree VARCHAR(100),
    Date_added DATE DEFAULT CURRENT_TIMESTAMP,
    last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    department_id INT,
    CONSTRAINT fk_department FOREIGN KEY (department_id) REFERENCES Department(department_id)
);


CREATE TABLE Courses (
    course_id INT PRIMARY KEY AUTO_INCREMENT,
    course_name VARCHAR(100) NOT NULL,
    programme_id INT, 
    semester VARCHAR(50) NOT NULL, 
    academic_year INT NOT NULL, 
    course_credits INT NOT NULL, 
    FOREIGN KEY (programme_id) REFERENCES Programme(Programme_id)
);

CREATE TABLE address (
    address_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    country VARCHAR(50),
    region VARCHAR(50),
    house_address VARCHAR(100)
);

CREATE TABLE guardian (
    guardian_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    guardian_first_name VARCHAR(100),
    guardian_last_name VARCHAR(100),
    guardian_relation VARCHAR(100),
    guardian_occupation VARCHAR(100),
    guardian_contact_number VARCHAR(15)
);

CREATE TABLE application (
    applicant_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    last_name VARCHAR(100) NOT NULL,
    birthdate DATE NOT NULL,
    gender VARCHAR(10) NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    email VARCHAR(100) NOT NULL,
    nationality VARCHAR(100) NOT NULL,
    shs_name VARCHAR(100) NOT NULL,
    wassce_index_number VARCHAR(15) NOT NULL,
    programme_id INT NOT NULL,
    session VARCHAR(50) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    consent_to_keep_data TINYINT(1) NOT NULL DEFAULT 0,
    guardian_id INT NOT NULL,
    address_id INT NOT NULL,
    -- Setting up foreign keys
    FOREIGN KEY (programme_id) REFERENCES Programme(Programme_id),
    FOREIGN KEY (guardian_id) REFERENCES guardian(guardian_id),
    FOREIGN KEY (address_id) REFERENCES address(address_id)
);

CREATE TABLE students (
    student_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL, 
    middle_name VARCHAR(100), 
    last_name VARCHAR(100) NOT NULL,
    student_level INT(11) NOT NULL, 
    birthdate DATE NOT NULL, 
    gender VARCHAR(10) NOT NULL, 
    phone_number VARCHAR(15) NOT NULL, 
    email VARCHAR(100) NOT NULL, 
    nationality VARCHAR(100) NOT NULL, 
    shs_name VARCHAR(100), 
    wassce_index_number VARCHAR(15), 
    programme_id INT(11) NOT NULL, 
    session VARCHAR(50) NOT NULL, 
    registration_date DATE DEFAULT CURRENT_DATE, 
    fees_paid TINYINT(1) DEFAULT 0, 
    registration_status VARCHAR(50) DEFAULT 'pending', -- or completed
    consent_to_keep_data TINYINT(1) DEFAULT 0,
    guardian_id INT(11),
    address_id INT(11),
    FOREIGN KEY (programme_id) REFERENCES Programme(Programme_id),
    FOREIGN KEY (guardian_id) REFERENCES Guardian(guardian_id),
    FOREIGN KEY (address_id) REFERENCES Address(address_id) 
);


CREATE TABLE Enrollments (
    enrollment_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    course_id INT,
    enrollment_date DATE DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES Students(student_id),
    FOREIGN KEY (course_id) REFERENCES Courses(course_id)
);

CREATE TABLE Fees (
    fee_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    semester VARCHAR(20),
    total_due DECIMAL(10, 2),
    amount_paid DECIMAL(10, 2) DEFAULT 0.00,
    date_paid DATE,
    status VARCHAR(20) DEFAULT 'pending',  -- 'pending', 'partially_paid', 'paid'
    FOREIGN KEY (student_id) REFERENCES Students(student_id)
);

-- INSERT statements
INSERT INTO Faculty (faculty_name) 
VALUES ('Faculty of Business'), 
       ('Faculty of Science'), 
       ('Faculty of Arts');

INSERT INTO Department (department_name, faculty_id) VALUES 
('Business Administration', 1), 
('Computer Science', 2), 
('Communication Studies', 3);

-- Insert courses for BSc Management, Semester 1, Year 1 (8 courses)
INSERT INTO Courses (course_name, programme_id, semester, academic_year, course_credits) 
VALUES 
('Introduction to Management', 1, 'Semester 1', 1, 3),
('Business Mathematics', 1, 'Semester 1', 1, 3),
('Economics 101', 1, 'Semester 1', 1, 3),
('Accounting 101', 1, 'Semester 1', 1, 3),
('Communication Skills', 1, 'Semester 1', 1, 2),
('Marketing Fundamentals', 1, 'Semester 1', 1, 3),
('Business Law', 1, 'Semester 1', 1, 3),
('Organizational Behavior', 1, 'Semester 1', 1, 3);

-- Insert courses for BSc Computer Science, Semester 1, Year 1 (8 courses)
INSERT INTO Courses (course_name, programme_id, semester, academic_year, course_credits) 
VALUES 
('Introduction to Computer Science', 2, 'Semester 1', 1, 3),
('Programming 101', 2, 'Semester 1', 1, 3),
('Mathematics for Computer Science', 2, 'Semester 1', 1, 3),
('Data Structures', 2, 'Semester 1', 1, 3),
('Communication Skills', 2, 'Semester 1', 1, 2),
('Algorithms', 2, 'Semester 1', 1, 3),
('Database Systems', 2, 'Semester 1', 1, 3),
('Operating Systems', 2, 'Semester 1', 1, 3);

-- Insert courses for BA Communication, Semester 1, Year 1 (8 courses)
INSERT INTO Courses (course_name, programme_id, semester, academic_year, course_credits) 
VALUES 
('Introduction to Communication', 3, 'Semester 1', 1, 3),
('Mass Communication', 3, 'Semester 1', 1, 3),
('Writing and Editing Skills', 3, 'Semester 1', 1, 3),
('Media and Society', 3, 'Semester 1', 1, 3),
('Communication Skills', 3, 'Semester 1', 1, 2),
('Advertising and PR', 3, 'Semester 1', 1, 3),
('Digital Media Production', 3, 'Semester 1', 1, 3),
('Public Speaking', 3, 'Semester 1', 1, 3);

UPDATE Programme SET department_id = 1 WHERE Programme_id = 1;  -- Link BSc Management to Business Administration
UPDATE Programme SET department_id = 2 WHERE Programme_id = 2;  -- Link BSc Computer Science to Computer Science
UPDATE Programme SET department_id = 3 WHERE Programme_id = 3;  -- Link BA Communication to Communication Studies
