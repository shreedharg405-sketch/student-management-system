-- ============================================================================
-- STUDENT MANAGEMENT SYSTEM (SMS) - COMPLETE MYSQL RELATIONAL DATABASE SCHEMA
-- Compatible with PHP 8.2+ PDO & MySQL / MariaDB (XAMPP & Cloud Shared Hosting)
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `studentms` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `studentms`;

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `leave_responses`;
DROP TABLE IF EXISTS `leave_requests`;
DROP TABLE IF EXISTS `attendance_records`;
DROP TABLE IF EXISTS `attendance`;
DROP TABLE IF EXISTS `exam_schedule`;
DROP TABLE IF EXISTS `timetable`;
DROP TABLE IF EXISTS `classes`;
DROP TABLE IF EXISTS `subjects`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `staff`;
DROP TABLE IF EXISTS `batches`;
DROP TABLE IF EXISTS `courses`;
DROP TABLE IF EXISTS `departments`;
DROP TABLE IF EXISTS `notices`;
DROP TABLE IF EXISTS `users`;

-- 1. Users Table
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('STUDENT', 'ASSISTANT_PROFESSOR', 'HOD') NOT NULL,
  `is_active` BOOLEAN DEFAULT TRUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_users_email` (`email`),
  INDEX `idx_users_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Departments Table
CREATE TABLE `departments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `code` VARCHAR(20) NOT NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Courses Table
CREATE TABLE `courses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `name` VARCHAR(150) NOT NULL,
  `department_id` INT DEFAULT NULL,
  `credits` INT DEFAULT 4,
  `semester` VARCHAR(20) DEFAULT '1st Semester',
  CONSTRAINT `fk_courses_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Batches Table
CREATE TABLE `batches` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `department_id` INT NOT NULL,
  `year` VARCHAR(20) NOT NULL,
  `section` VARCHAR(10) NOT NULL,
  `batch_name` VARCHAR(50) NOT NULL,
  CONSTRAINT `fk_batches_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Classes Table
CREATE TABLE `classes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `department_id` INT NOT NULL,
  `year` VARCHAR(20) NOT NULL,
  `section` VARCHAR(10) NOT NULL,
  `semester` VARCHAR(20) NOT NULL,
  `class_name` VARCHAR(100) NOT NULL,
  CONSTRAINT `fk_classes_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Staff Table
CREATE TABLE `staff` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL UNIQUE,
  `staff_code` VARCHAR(50) NOT NULL UNIQUE,
  `department_id` INT NOT NULL,
  `designation` VARCHAR(50) DEFAULT 'Assistant Professor',
  `phone` VARCHAR(20) DEFAULT NULL,
  `qualification` VARCHAR(100) DEFAULT NULL,
  `joining_date` DATE DEFAULT NULL,
  CONSTRAINT `fk_staff_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_staff_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Students Table
CREATE TABLE `students` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL UNIQUE,
  `register_number` VARCHAR(50) NOT NULL UNIQUE,
  `department_id` INT NOT NULL,
  `course_id` INT DEFAULT NULL,
  `year` VARCHAR(20) DEFAULT '3rd Year',
  `section` VARCHAR(10) DEFAULT 'A',
  `batch_id` INT DEFAULT NULL,
  `mentor_id` INT DEFAULT NULL,
  `hod_id` INT DEFAULT NULL,
  `dob` DATE DEFAULT NULL,
  `gender` VARCHAR(20) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `status` VARCHAR(20) DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_students_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_students_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_students_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_students_batch` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_students_mentor` FOREIGN KEY (`mentor_id`) REFERENCES `staff` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_students_hod` FOREIGN KEY (`hod_id`) REFERENCES `staff` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Subjects Table
CREATE TABLE `subjects` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `subject_code` VARCHAR(50) NOT NULL UNIQUE,
  `subject_name` VARCHAR(150) NOT NULL,
  `department_id` INT NOT NULL,
  `semester` VARCHAR(20) DEFAULT '5th Semester',
  `credits` INT DEFAULT 3,
  CONSTRAINT `fk_subjects_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Timetable Table
CREATE TABLE `timetable` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `day` VARCHAR(20) NOT NULL,
  `period` INT NOT NULL,
  `start_time` VARCHAR(20) NOT NULL,
  `end_time` VARCHAR(20) NOT NULL,
  `department_id` INT NOT NULL,
  `year` VARCHAR(20) NOT NULL,
  `section` VARCHAR(10) NOT NULL,
  `batch_id` INT DEFAULT NULL,
  `subject_id` INT NOT NULL,
  `faculty_id` INT NOT NULL,
  `room` VARCHAR(50) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_timetable_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_timetable_batch` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_timetable_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_timetable_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Exam Schedule Table
CREATE TABLE `exam_schedule` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `exam_name` VARCHAR(100) NOT NULL,
  `exam_type` VARCHAR(50) NOT NULL,
  `subject_id` INT NOT NULL,
  `department_id` INT NOT NULL,
  `year` VARCHAR(20) NOT NULL,
  `section` VARCHAR(10) NOT NULL,
  `semester` VARCHAR(20) NOT NULL,
  `date` DATE NOT NULL,
  `start_time` VARCHAR(20) NOT NULL,
  `end_time` VARCHAR(20) NOT NULL,
  `room` VARCHAR(50) NOT NULL,
  `is_published` BOOLEAN DEFAULT TRUE,
  `created_by` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_exams_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_exams_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Attendance Header Table
CREATE TABLE `attendance` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `date` DATE NOT NULL,
  `period` INT NOT NULL,
  `subject_id` INT NOT NULL,
  `batch_id` INT NOT NULL,
  `faculty_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_att_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_att_batch` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_att_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  UNIQUE KEY `uq_attendance_slot` (`date`, `period`, `subject_id`, `batch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Attendance Records Table
CREATE TABLE `attendance_records` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `attendance_id` INT NOT NULL,
  `student_id` INT NOT NULL,
  `status` ENUM('Present', 'Absent') NOT NULL DEFAULT 'Present',
  `remarks` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_rec_attendance` FOREIGN KEY (`attendance_id`) REFERENCES `attendance` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rec_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Leave Requests Table
CREATE TABLE `leave_requests` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `leave_type` VARCHAR(50) NOT NULL,
  `from_date` DATE NOT NULL,
  `to_date` DATE NOT NULL,
  `reason` TEXT NOT NULL,
  `attachment_url` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('PENDING_MENTOR', 'MENTOR_APPROVED', 'MENTOR_REJECTED', 'PENDING_HOD', 'HOD_APPROVED', 'HOD_REJECTED') DEFAULT 'PENDING_MENTOR',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_leaves_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Leave Responses Table
CREATE TABLE `leave_responses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `leave_request_id` INT NOT NULL,
  `responder_id` INT NOT NULL,
  `stage` ENUM('MENTOR', 'HOD') NOT NULL,
  `action` ENUM('APPROVED', 'REJECTED') NOT NULL,
  `remarks` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_leave_resp_request` FOREIGN KEY (`leave_request_id`) REFERENCES `leave_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_leave_resp_user` FOREIGN KEY (`responder_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. Notices Table
CREATE TABLE `notices` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) NOT NULL,
  `content` TEXT NOT NULL,
  `target_role` VARCHAR(50) DEFAULT 'ALL',
  `department_id` INT DEFAULT NULL,
  `created_by` VARCHAR(100) DEFAULT 'Administration',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_notices_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;

-- ============================================================================
-- DEMO SEED DATA
-- Passwords hashed using bcrypt (password_hash(..., PASSWORD_DEFAULT))
-- Demo passwords:
-- student123 -> $2y$10$SpTR10TN3TiyiOizAAaCsee3cHN.M/r99kVr/fp3UrVsfDB9YLSyC
-- staff123   -> $2y$10$ssPqv7HaEXFjaZaXxkadXu4qUUU0UAD4Wf5n5gl8NevcUsyh.g.Ii
-- hod123     -> $2y$10$CbNZqIjFJkHCaZOzX.wzNOlg9.Kl5uCMocNTi7iwpdH8BLvRSrDrm
-- ============================================================================

-- Seed Departments
INSERT INTO `departments` (`id`, `name`, `code`) VALUES
(1, 'Electronics & Communication Engineering', 'ECE'),
(2, 'Computer Science & Engineering', 'CSE'),
(3, 'Mechanical Engineering', 'MECH');

-- Seed Courses
INSERT INTO `courses` (`id`, `code`, `name`, `department_id`, `credits`, `semester`) VALUES
(1, 'BTECH-ECE', 'B.Tech in Electronics & Communication Engineering', 1, 160, '5th Semester'),
(2, 'BTECH-CSE', 'B.Tech in Computer Science & Engineering', 2, 160, '5th Semester'),
(3, 'BTECH-MECH', 'B.Tech in Mechanical Engineering', 3, 160, '5th Semester');

-- Seed Batches
INSERT INTO `batches` (`id`, `department_id`, `year`, `section`, `batch_name`) VALUES
(1, 1, '3rd Year', 'A', 'Batch 1'),
(2, 1, '3rd Year', 'A', 'Batch 2'),
(3, 2, '3rd Year', 'A', 'Batch 1'),
(4, 3, '3rd Year', 'A', 'Batch 1');

-- Seed Classes
INSERT INTO `classes` (`id`, `department_id`, `year`, `section`, `semester`, `class_name`) VALUES
(1, 1, '3rd Year', 'A', '5th Semester', 'ECE 3rd Year Section A'),
(2, 2, '3rd Year', 'A', '5th Semester', 'CSE 3rd Year Section A'),
(3, 3, '3rd Year', 'A', '5th Semester', 'MECH 3rd Year Section A');

-- Seed Users
INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `is_active`) VALUES
(1, 'Dr. R. K. Sharma', 'hod1@gmail.com', '$2y$10$CbNZqIjFJkHCaZOzX.wzNOlg9.Kl5uCMocNTi7iwpdH8BLvRSrDrm', 'HOD', 1),
(2, 'Prof. Sarah Jenkins', 'staff1@gmail.com', '$2y$10$ssPqv7HaEXFjaZaXxkadXu4qUUU0UAD4Wf5n5gl8NevcUsyh.g.Ii', 'ASSISTANT_PROFESSOR', 1),
(3, 'Prof. Michael Davis', 'staff2@gmail.com', '$2y$10$ssPqv7HaEXFjaZaXxkadXu4qUUU0UAD4Wf5n5gl8NevcUsyh.g.Ii', 'ASSISTANT_PROFESSOR', 1),
(4, 'Alex Johnson', 'student1@gmail.com', '$2y$10$SpTR10TN3TiyiOizAAaCsee3cHN.M/r99kVr/fp3UrVsfDB9YLSyC', 'STUDENT', 1),
(5, 'Emily Brown', 'student2@gmail.com', '$2y$10$SpTR10TN3TiyiOizAAaCsee3cHN.M/r99kVr/fp3UrVsfDB9YLSyC', 'STUDENT', 1),
(6, 'Liam Wilson', 'student3@gmail.com', '$2y$10$SpTR10TN3TiyiOizAAaCsee3cHN.M/r99kVr/fp3UrVsfDB9YLSyC', 'STUDENT', 1),
(7, 'Sophia Miller', 'student4@gmail.com', '$2y$10$SpTR10TN3TiyiOizAAaCsee3cHN.M/r99kVr/fp3UrVsfDB9YLSyC', 'STUDENT', 1),
(8, 'James Taylor', 'student5@gmail.com', '$2y$10$SpTR10TN3TiyiOizAAaCsee3cHN.M/r99kVr/fp3UrVsfDB9YLSyC', 'STUDENT', 1);

-- Seed Staff
INSERT INTO `staff` (`id`, `user_id`, `staff_code`, `department_id`, `designation`, `phone`, `qualification`, `joining_date`) VALUES
(1, 1, 'HOD-ECE-01', 1, 'Professor & HOD', '+91 98765 43210', 'Ph.D. in VLSI & Embedded Systems', '2015-06-01'),
(2, 2, 'STF-ECE-01', 1, 'Assistant Professor', '+91 98765 43211', 'M.Tech in Communication Systems', '2018-08-15'),
(3, 3, 'STF-ECE-02', 1, 'Assistant Professor', '+91 98765 43212', 'M.Tech in Signal Processing', '2020-01-10');

-- Seed Students
INSERT INTO `students` (`id`, `user_id`, `register_number`, `department_id`, `course_id`, `year`, `section`, `batch_id`, `mentor_id`, `hod_id`, `dob`, `gender`, `phone`, `address`, `status`) VALUES
(1, 4, '21EC001', 1, 1, '3rd Year', 'A', 1, 2, 1, '2003-05-14', 'Male', '+91 91234 56780', '12 Lake View St, Chennai', 'Active'),
(2, 5, '21EC002', 1, 1, '3rd Year', 'A', 1, 2, 1, '2003-08-21', 'Female', '+91 91234 56781', '45 Green Park, Coimbatore', 'Active'),
(3, 6, '21EC003', 1, 1, '3rd Year', 'A', 1, 2, 1, '2003-11-03', 'Male', '+91 91234 56782', '89 Anna Nagar, Madurai', 'Active'),
(4, 7, '21EC004', 1, 1, '3rd Year', 'A', 2, 3, 1, '2003-02-17', 'Female', '+91 91234 56783', '33 Gandhi Road, Salem', 'Active'),
(5, 8, '21EC005', 1, 1, '3rd Year', 'A', 2, 3, 1, '2003-09-29', 'Male', '+91 91234 56784', '77 Cross Cut Rd, Tiruchirappalli', 'Active');

-- Seed Subjects
INSERT INTO `subjects` (`id`, `subject_code`, `subject_name`, `department_id`, `semester`, `credits`) VALUES
(1, 'EC501', 'Database Management Systems', 1, '5th Semester', 3),
(2, 'EC502', 'Digital Signal Processing', 1, '5th Semester', 4),
(3, 'EC503', 'Microcontrollers & Embedded Systems', 1, '5th Semester', 4),
(4, 'EC504', 'VLSI Design & Technology', 1, '5th Semester', 3),
(5, 'EC505', 'Embedded Systems Lab', 1, '5th Semester', 2);

-- Seed Timetable Slots
INSERT INTO `timetable` (`id`, `day`, `period`, `start_time`, `end_time`, `department_id`, `year`, `section`, `batch_id`, `subject_id`, `faculty_id`, `room`) VALUES
(1, 'Monday', 1, '09:00 AM', '10:00 AM', 1, '3rd Year', 'A', 1, 1, 2, 'LH-201'),
(2, 'Monday', 2, '10:00 AM', '11:00 AM', 1, '3rd Year', 'A', 1, 2, 3, 'LH-201'),
(3, 'Monday', 3, '11:15 AM', '12:15 PM', 1, '3rd Year', 'A', 1, 3, 1, 'LH-201'),
(4, 'Monday', 4, '01:15 PM', '02:15 PM', 1, '3rd Year', 'A', 1, 4, 1, 'LH-201'),
(5, 'Monday', 5, '02:15 PM', '03:15 PM', 1, '3rd Year', 'A', 1, 5, 2, 'DSP Lab'),
(6, 'Monday', 6, '03:15 PM', '04:15 PM', 1, '3rd Year', 'A', 1, 5, 2, 'DSP Lab'),
(7, 'Tuesday', 1, '09:00 AM', '10:00 AM', 1, '3rd Year', 'A', 1, 2, 3, 'LH-201'),
(8, 'Tuesday', 2, '10:00 AM', '11:00 AM', 1, '3rd Year', 'A', 1, 1, 2, 'LH-201'),
(9, 'Tuesday', 3, '11:15 AM', '12:15 PM', 1, '3rd Year', 'A', 1, 4, 1, 'LH-201'),
(10, 'Wednesday', 1, '09:00 AM', '10:00 AM', 1, '3rd Year', 'A', 1, 3, 1, 'LH-201'),
(11, 'Wednesday', 2, '10:00 AM', '11:00 AM', 1, '3rd Year', 'A', 1, 1, 2, 'LH-201'),
(12, 'Wednesday', 3, '11:15 AM', '12:15 PM', 1, '3rd Year', 'A', 1, 2, 3, 'LH-201'),
(13, 'Thursday', 1, '09:00 AM', '10:00 AM', 1, '3rd Year', 'A', 1, 4, 1, 'LH-201'),
(14, 'Thursday', 2, '10:00 AM', '11:00 AM', 1, '3rd Year', 'A', 1, 3, 1, 'LH-201'),
(15, 'Friday', 1, '09:00 AM', '10:00 AM', 1, '3rd Year', 'A', 1, 1, 2, 'LH-201'),
(16, 'Friday', 2, '10:00 AM', '11:00 AM', 1, '3rd Year', 'A', 1, 2, 3, 'LH-201'),
-- Add slots for Saturday & Batch 2
(17, 'Saturday', 1, '09:00 AM', '10:00 AM', 1, '3rd Year', 'A', 1, 1, 2, 'LH-201'),
(18, 'Monday', 1, '09:00 AM', '10:00 AM', 1, '3rd Year', 'A', 2, 2, 3, 'LH-202'),
(19, 'Monday', 2, '10:00 AM', '11:00 AM', 1, '3rd Year', 'A', 2, 1, 2, 'LH-202');

-- Seed Exam Schedule
INSERT INTO `exam_schedule` (`id`, `exam_name`, `exam_type`, `subject_id`, `department_id`, `year`, `section`, `semester`, `date`, `start_time`, `end_time`, `room`, `is_published`, `created_by`) VALUES
(1, 'Internal Assessment 1', 'Theory', 1, 1, '3rd Year', 'A', '5th Semester', '2026-10-05', '09:30 AM', '11:00 AM', 'Hall A', 1, 'Dr. R. K. Sharma'),
(2, 'Internal Assessment 1', 'Theory', 2, 1, '3rd Year', 'A', '5th Semester', '2026-10-07', '09:30 AM', '11:00 AM', 'Hall A', 1, 'Dr. R. K. Sharma'),
(3, 'Internal Assessment 1', 'Theory', 3, 1, '3rd Year', 'A', '5th Semester', '2026-10-09', '09:30 AM', '11:00 AM', 'Hall B', 1, 'Dr. R. K. Sharma'),
(4, 'Internal Assessment 1', 'Theory', 4, 1, '3rd Year', 'A', '5th Semester', '2026-10-12', '09:30 AM', '11:00 AM', 'Hall B', 1, 'Dr. R. K. Sharma');

-- Seed Attendance Header & Records
INSERT INTO `attendance` (`id`, `date`, `period`, `subject_id`, `batch_id`, `faculty_id`) VALUES
(1, '2026-09-15', 1, 1, 1, 2),
(2, '2026-09-16', 2, 1, 1, 2),
(3, '2026-09-17', 2, 2, 1, 3);

INSERT INTO `attendance_records` (`attendance_id`, `student_id`, `status`, `remarks`) VALUES
(1, 1, 'Present', 'Attentive'),
(1, 2, 'Present', 'On time'),
(1, 3, 'Absent', 'Informed'),
(2, 1, 'Present', 'Participated'),
(2, 2, 'Present', 'On time'),
(2, 3, 'Present', 'On time'),
(3, 1, 'Present', 'On time'),
(3, 2, 'Absent', 'Medical'),
(3, 3, 'Present', 'On time');

-- Seed Leave Requests & Responses
INSERT INTO `leave_requests` (`id`, `student_id`, `leave_type`, `from_date`, `to_date`, `reason`, `attachment_url`, `status`) VALUES
(1, 1, 'Medical Leave', '2026-09-10', '2026-09-11', 'Severe viral fever and advised medical rest.', '', 'HOD_APPROVED'),
(2, 1, 'Academic Leave', '2026-09-25', '2026-09-26', 'Attending National Level Hackathon at IIT Madras.', '', 'PENDING_MENTOR');

INSERT INTO `leave_responses` (`id`, `leave_request_id`, `responder_id`, `stage`, `action`, `remarks`) VALUES
(1, 1, 2, 'MENTOR', 'APPROVED', 'Medical certificate verified. Recommended.'),
(2, 1, 1, 'HOD', 'APPROVED', 'Approved for 2 days medical rest.');

-- Seed Notices
INSERT INTO `notices` (`id`, `title`, `content`, `target_role`, `department_id`, `created_by`) VALUES
(1, 'Mid-Term Examinations Schedule Released', 'The timetable for Internal Assessment 1 has been published. All students must report to examination halls 15 minutes before the scheduled time.', 'ALL', 1, 'Dr. R. K. Sharma'),
(2, 'Industrial Visit to ISRO Propulsion Complex', '3rd Year ECE students are hereby informed that the department has organized an industrial visit to IPRC Mahendragiri. Interested students register with your mentor.', 'STUDENT', 1, 'Prof. Sarah Jenkins'),
(3, 'Faculty Meeting on NAAC Accreditation', 'All faculty members are requested to attend the NAAC preparatory meeting on Friday at 04:30 PM in the Conference Hall.', 'ASSISTANT_PROFESSOR', 1, 'Dr. R. K. Sharma');
