# University Registration System

## Overview

The University Registration System is a web-based application designed to streamline the process of student registration, course enrollment, and management for universities. It facilitates student applications, admissions, and the selection of courses for each academic semester. This project demonstrates the core functionalities of a university registration system.

## Features

- **Student Application**: Prospective students can apply to the university by filling out personal and academic details.
- **Admin Review**: University administrators can review, accept, or reject student applications. Accepted students are automatically assigned a student ID and can proceed to enroll in courses.
- **Course Enrollment**: Enrolled students can select 5 courses from a list of available courses each semester based on their program.
- **User Authentication**: Students can log in using their student ID and birthdate.
- **Profile Management**: Students can view their profile information, including personal details, program, faculty, and department, as well as the courses they are enrolled in for the current semester.
- **Admin Dashboard**: Administrators can manage student applications and view accepted students, including their names, student IDs, and registration timestamps.

## Technologies Used

- **PHP**: Handles server-side logic and database interactions.
- **MySQL**: Manages the database for student applications, courses, enrollments, and fees.
- **HTML/CSS**: Provides the structure and design of the application’s front-end.
- **XAMPP**: Local development environment for running PHP and MySQL.

## Database Schema

- **Faculty**: Stores faculty details like faculty name and timestamps.
- **Department**: Stores department details, including the faculty each department belongs to.
- **Programme**: Manages programs offered by the university, such as BSc Management or BSc Computer Science, and links to departments.
- **Students**: Stores student information, including personal details, program, level, and enrollment status.
- **Courses**: Manages course details, including course name, program, academic year, semester, and credits.
- **Address**: Stores the home address information for students and their guardians.
- **Guardian**: Stores the guardian details of each student.
- **Applications**: Manages applications from prospective students, with fields for personal and academic details.
- **Enrollments**: Links students with courses they have enrolled in each semester.
- **Fees**: Tracks student fee payments, total amounts due, and their payment status.

## Setup Instructions

1. Clone the repository to your local machine:
   ```bash
   git clone https://github.com/shinju-dy/UniversityRegistrationSystem.git
   
2. Install and configure XAMPP or another local development environment that supports PHP and MySQL.

3. Import the UniversityRegistration.sql file into MySQL to create the necessary database and tables:


4. mysql -u root -p < UniversityRegistration.sql
   Update the database connection details in the PHP files if necessary (host, username, password, etc.).

5. Start the XAMPP server and navigate to the web application at:
http://localhost/UniversityRegistrationSystem/

## Usage

Admin Panel:
Admins can log in to review applications, accept or reject students, and manage enrolled students and courses.

Student Login:
Once accepted, students can log in using their student ID and date of birth to access their profile and enroll in courses.

Course Enrollment:
Students can enroll in a maximum of 5 courses each semester.

## Contributing
If you would like to contribute to this project, please follow these steps:

## Fork the repository.
Create a new branch for your feature or bug fix.
Make your changes and commit them with clear and concise messages.
Push your changes to your fork.
Create a pull request to merge your changes into the main branch.

## License
This project is licensed under the MIT License. You are free to use, modify, distribute, and sublicense the software according to the terms of the MIT License.

## Contact
For any questions or inquiries, please contact:
pearl.darkowaa@gmail.com
+233555491813
Pearl Darkowaa Yeboah
