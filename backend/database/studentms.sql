-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: studentms
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `department` varchar(50) DEFAULT 'ECE',
  `designation` varchar(100) DEFAULT 'Assistant Professor',
  `qualification` varchar(100) DEFAULT 'M.Tech / Ph.D.',
  `phone` varchar(30) DEFAULT '+91 9876500001',
  `role` varchar(20) NOT NULL DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,NULL,'shree','shree@college.edu','$2y$10$SIq3v5QY/AwO2CoipbYlIeDRBFLZplaaNoqqZyoaSZIgnQ1NJTmH6','ECE','Assistant Professor','M.Tech / Ph.D.','+91 9876500001','staff','2026-07-17 04:24:13'),(2,NULL,'Shree','you@college.edu','$2y$10$WXWR8fCbAHvzviAIwPYJkeR9un93sod2RjQEiHRgKX/5eOeE2Q58O','ECE','Assistant Professor','M.Tech / Ph.D.','+91 9876500001','staff','2026-07-17 04:38:09'),(3,NULL,'you','your@college.edu','$2y$10$lnLXW.OnRSfJlSk7Pjgpfe.7FaMupAEs4zsBbXpFkuRHB2BslnQ/q','ECE','Assistant Professor','M.Tech / Ph.D.','+91 9876500001','staff','2026-07-17 05:39:49'),(4,NULL,'Shreedhar G','Shreedhar@college.edu','$2y$10$5kxfTq7EjwZkC.pbViQWcuZRBGgNxLCGFGm94jb6.SSF61s6jM5nW','ECE','Assistant Professor','M.Tech / Ph.D.','+91 9876500001','staff','2026-07-18 04:33:58'),(5,NULL,'ravi','ravi2006@gmail.com','$2y$10$QZgN53p/YbLp5EOL0/lbp.TwUj1EQd3GNby9R2MUzqxlo2ndzVOqq','ECE','Assistant Professor','M.Tech / Ph.D.','+91 9876500001','staff','2026-08-13 07:42:24'),(6,NULL,'Bharath S','bharath@gmail.com','$2y$10$vIonOM.0OMgxwMtEwtLTq.WXOLssjA4fyJYj3uySoCwi2nnchpzES','ECE','Assistant Professor','M.Tech / Ph.D.','+91 9876500001','staff','2026-08-14 07:58:53'),(7,'STAFF001','Dr. Robert Vance','staff@gmail.com','$2y$10$3asQzh1tB6//m32nIt11nOcY9q.4UjGgLLqC/hLyeY1/RGRc5NVdq','ECE','Head of Department','Ph.D. in Electronics & Communication','+91 9876500001','staff','2026-09-16 17:39:08'),(8,'STAFF001','Dr. Robert Vance','r.vance@example.com','$2y$10$3asQzh1tB6//m32nIt11nOcY9q.4UjGgLLqC/hLyeY1/RGRc5NVdq','ECE','Head of Department','Ph.D. in Electronics & Communication','+91 9876500001','staff','2026-09-16 17:39:08'),(9,'STAFF002','Prof. Sarah Connor','cse.hod@college.edu','$2y$10$3asQzh1tB6//m32nIt11nOcY9q.4UjGgLLqC/hLyeY1/RGRc5NVdq','CSE','Head of Department','Ph.D. in Computer Science','+91 9876500001','staff','2026-09-16 17:39:08'),(10,'STAFF003','Dr. Alan Turing','it.hod@college.edu','$2y$10$3asQzh1tB6//m32nIt11nOcY9q.4UjGgLLqC/hLyeY1/RGRc5NVdq','IT','Head of Department','Ph.D. in Information Systems','+91 9876500001','staff','2026-09-16 17:39:08'),(11,'STAFF004','Dr. Nikola Tesla','mech.hod@college.edu','$2y$10$3asQzh1tB6//m32nIt11nOcY9q.4UjGgLLqC/hLyeY1/RGRc5NVdq','MECH','Head of Department','Ph.D. in Mechanical Engineering','+91 9876500001','staff','2026-09-16 17:39:08'),(12,'STAFF005','Dr. Isambard Brunel','civil.hod@college.edu','$2y$10$3asQzh1tB6//m32nIt11nOcY9q.4UjGgLLqC/hLyeY1/RGRc5NVdq','CIVIL','Head of Department','Ph.D. in Structural & Civil Engg','+91 9876500001','staff','2026-09-16 17:39:08'),(13,'STAFF006','Dr. Michael Faraday','eee.hod@college.edu','$2y$10$3asQzh1tB6//m32nIt11nOcY9q.4UjGgLLqC/hLyeY1/RGRc5NVdq','EEE','Head of Department','Ph.D. in Electrical & Electronic Systems','+91 9876500001','staff','2026-09-16 17:39:08');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `period` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_attendance_slot` (`date`,`period`,`subject_id`,`batch_id`),
  KEY `fk_att_subject` (`subject_id`),
  KEY `fk_att_batch` (`batch_id`),
  KEY `fk_att_faculty` (`faculty_id`),
  CONSTRAINT `fk_att_batch` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_att_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_att_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance`
--

LOCK TABLES `attendance` WRITE;
/*!40000 ALTER TABLE `attendance` DISABLE KEYS */;
INSERT INTO `attendance` VALUES (1,'2026-09-19',1,1,1,2,'2026-09-21 02:41:02');
/*!40000 ALTER TABLE `attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_records`
--

DROP TABLE IF EXISTS `attendance_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attendance_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `status` enum('Present','Absent') NOT NULL DEFAULT 'Present',
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_rec_attendance` (`attendance_id`),
  KEY `fk_rec_student` (`student_id`),
  CONSTRAINT `fk_rec_attendance` FOREIGN KEY (`attendance_id`) REFERENCES `attendance` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rec_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_records`
--

LOCK TABLES `attendance_records` WRITE;
/*!40000 ALTER TABLE `attendance_records` DISABLE KEYS */;
INSERT INTO `attendance_records` VALUES (1,1,1,'Present',NULL,'2026-09-21 02:41:02'),(2,1,2,'Absent',NULL,'2026-09-21 02:41:02'),(3,1,3,'Absent',NULL,'2026-09-21 02:41:02'),(4,1,4,'Absent',NULL,'2026-09-21 02:41:02'),(5,1,5,'Absent',NULL,'2026-09-21 02:41:02');
/*!40000 ALTER TABLE `attendance_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance_sessions`
--

DROP TABLE IF EXISTS `attendance_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendance_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_date` date NOT NULL,
  `dept_code` varchar(20) NOT NULL,
  `year` varchar(20) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `section` varchar(10) NOT NULL,
  `course_code` varchar(50) NOT NULL,
  `course_name` varchar(150) NOT NULL,
  `staff_id` varchar(50) NOT NULL,
  `staff_name` varchar(100) NOT NULL,
  `total_students` int(11) NOT NULL DEFAULT 0,
  `present_count` int(11) NOT NULL DEFAULT 0,
  `absent_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance_sessions`
--

LOCK TABLES `attendance_sessions` WRITE;
/*!40000 ALTER TABLE `attendance_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `batches`
--

DROP TABLE IF EXISTS `batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `batches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_id` int(11) NOT NULL,
  `year` varchar(20) NOT NULL,
  `section` varchar(10) NOT NULL,
  `batch_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_batches_department` (`department_id`),
  CONSTRAINT `fk_batches_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `batches`
--

LOCK TABLES `batches` WRITE;
/*!40000 ALTER TABLE `batches` DISABLE KEYS */;
INSERT INTO `batches` VALUES (1,1,'3rd Year','A','Batch 1'),(2,1,'3rd Year','A','Batch 2'),(3,1,'3rd Year','B','Batch 1'),(4,2,'3rd Year','A','Batch 1'),(5,3,'3rd Year','A','Batch 1'),(6,4,'3rd Year','A','Batch 1');
/*!40000 ALTER TABLE `batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `credits` int(11) DEFAULT 4,
  `semester` varchar(20) DEFAULT '1st Semester',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `fk_courses_department` (`department_id`),
  CONSTRAINT `fk_courses_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courses`
--

LOCK TABLES `courses` WRITE;
/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
INSERT INTO `courses` VALUES (1,'BTECH-ECE','B.Tech Electronics and Communication',1,160,'6th'),(2,'BTECH-CSE','B.Tech Computer Science and Engineering',2,160,'6th'),(3,'BTECH-EEE','B.Tech Electrical & Electronics',3,160,'6th'),(4,'BTECH-MECH','B.Tech Mechanical Engineering',4,160,'6th');
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,'Electronics & Communication Engineering','ECE','2026-09-21 02:40:56'),(2,'Computer Science & Engineering','CSE','2026-09-21 02:40:56'),(3,'Electrical & Electronics Engineering','EEE','2026-09-21 02:40:56'),(4,'Mechanical Engineering','MECH','2026-09-21 02:40:56');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_schedule`
--

DROP TABLE IF EXISTS `exam_schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exam_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exam_name` varchar(100) NOT NULL,
  `exam_type` varchar(50) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `year` varchar(20) NOT NULL,
  `section` varchar(10) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `date` date NOT NULL,
  `start_time` varchar(20) NOT NULL,
  `end_time` varchar(20) NOT NULL,
  `room` varchar(50) NOT NULL,
  `is_published` tinyint(1) DEFAULT 1,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_exams_subject` (`subject_id`),
  KEY `fk_exams_department` (`department_id`),
  CONSTRAINT `fk_exams_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_exams_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_schedule`
--

LOCK TABLES `exam_schedule` WRITE;
/*!40000 ALTER TABLE `exam_schedule` DISABLE KEYS */;
INSERT INTO `exam_schedule` VALUES (1,'Mid Term Assessment I','Internal',1,1,'3rd Year','A','5th Semester','2026-10-12','09:30 AM','11:00 AM','Hall 101',1,'Dr. R. K. Sharma (HOD)','2026-09-21 02:41:02'),(2,'Mid Term Assessment I','Internal',2,1,'3rd Year','A','5th Semester','2026-10-14','09:30 AM','11:00 AM','Hall 101',1,'Dr. R. K. Sharma (HOD)','2026-09-21 02:41:02'),(3,'End Semester Examination','Semester',1,1,'3rd Year','A','5th Semester','2026-11-20','09:30 AM','12:30 PM','Exam Auditorium',1,'Dr. R. K. Sharma (HOD)','2026-09-21 02:41:02'),(4,'End Semester Examination','Semester',3,1,'3rd Year','A','5th Semester','2026-11-24','09:30 AM','12:30 PM','Exam Auditorium',1,'Dr. R. K. Sharma (HOD)','2026-09-21 02:41:02');
/*!40000 ALTER TABLE `exam_schedule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exam_schedules`
--

DROP TABLE IF EXISTS `exam_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exam_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department` varchar(50) NOT NULL,
  `academic_year` varchar(50) NOT NULL DEFAULT '2026 - 2027',
  `student_year` varchar(50) NOT NULL DEFAULT '3rd Year',
  `semester` varchar(50) NOT NULL DEFAULT 'VI Semester',
  `section` varchar(10) NOT NULL DEFAULT 'A',
  `exam_type` varchar(50) NOT NULL DEFAULT 'Semester Exam',
  `subject` varchar(150) NOT NULL,
  `subject_code` varchar(50) NOT NULL,
  `exam_date` date NOT NULL,
  `start_time` varchar(20) NOT NULL,
  `end_time` varchar(20) NOT NULL,
  `room` varchar(50) NOT NULL DEFAULT 'Main Exam Hall A',
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exam_schedules`
--

LOCK TABLES `exam_schedules` WRITE;
/*!40000 ALTER TABLE `exam_schedules` DISABLE KEYS */;
INSERT INTO `exam_schedules` VALUES (4,'ECE','2026 - 2027','1st Year','I Semester','A','Semester Exam','Digital Signal Processing','EC602','0000-00-00','09:30 AM','12:30 PM','Auditorium 2','Dr. Robert Vance (STAFF001)','2026-09-17 08:02:45','2026-09-17 08:02:45'),(5,'ECE','2026 - 2027','1st Year','I Semester','C','Semester Exam','Digital Signal Processing','602','2026-10-28','10:00 AM','12:30 PM','Main Exam Hall A','Dr. Robert Vance (STAFF001)','2026-09-17 09:51:23','2026-09-17 09:51:23');
/*!40000 ALTER TABLE `exam_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `examinations`
--

DROP TABLE IF EXISTS `examinations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `examinations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exam_code` varchar(50) NOT NULL,
  `course_code` varchar(50) NOT NULL,
  `course_name` varchar(150) NOT NULL,
  `dept_code` varchar(20) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `exam_date` date NOT NULL,
  `time_slot` varchar(50) NOT NULL,
  `hall_no` varchar(50) NOT NULL DEFAULT 'Main Exam Hall A',
  `invigilator` varchar(100) NOT NULL DEFAULT 'Dr. Robert Vance',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `exam_code` (`exam_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `examinations`
--

LOCK TABLES `examinations` WRITE;
/*!40000 ALTER TABLE `examinations` DISABLE KEYS */;
/*!40000 ALTER TABLE `examinations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_requests`
--

DROP TABLE IF EXISTS `leave_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leave_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `leave_type` varchar(50) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `reason` text NOT NULL,
  `attachment_url` varchar(255) DEFAULT NULL,
  `status` enum('PENDING_MENTOR','MENTOR_APPROVED','MENTOR_REJECTED','PENDING_HOD','HOD_APPROVED','HOD_REJECTED') DEFAULT 'PENDING_MENTOR',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_leaves_student` (`student_id`),
  CONSTRAINT `fk_leaves_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_requests`
--

LOCK TABLES `leave_requests` WRITE;
/*!40000 ALTER TABLE `leave_requests` DISABLE KEYS */;
INSERT INTO `leave_requests` VALUES (1,1,'Medical','2026-09-25','2026-09-27','Viral fever and medical rest as advised by physician.',NULL,'PENDING_MENTOR','2026-09-21 02:41:02','2026-09-21 02:41:02');
/*!40000 ALTER TABLE `leave_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_responses`
--

DROP TABLE IF EXISTS `leave_responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leave_responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `leave_request_id` int(11) NOT NULL,
  `responder_id` int(11) NOT NULL,
  `stage` enum('MENTOR','HOD') NOT NULL,
  `action` enum('APPROVED','REJECTED') NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_leave_resp_request` (`leave_request_id`),
  KEY `fk_leave_resp_user` (`responder_id`),
  CONSTRAINT `fk_leave_resp_request` FOREIGN KEY (`leave_request_id`) REFERENCES `leave_requests` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_leave_resp_user` FOREIGN KEY (`responder_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_responses`
--

LOCK TABLES `leave_responses` WRITE;
/*!40000 ALTER TABLE `leave_responses` DISABLE KEYS */;
/*!40000 ALTER TABLE `leave_responses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marks`
--

DROP TABLE IF EXISTS `marks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` varchar(50) NOT NULL,
  `student_roll` varchar(50) NOT NULL,
  `course_code` varchar(50) NOT NULL,
  `subject_name` varchar(150) NOT NULL,
  `internal_marks` int(11) NOT NULL DEFAULT 0,
  `exam_marks` int(11) NOT NULL DEFAULT 0,
  `total_marks` int(11) NOT NULL DEFAULT 0,
  `grade` varchar(5) NOT NULL DEFAULT 'A',
  `result` enum('PASS','FAIL') NOT NULL DEFAULT 'PASS',
  `semester` varchar(20) NOT NULL DEFAULT 'VIII Semester',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `stud_course` (`student_id`,`course_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marks`
--

LOCK TABLES `marks` WRITE;
/*!40000 ALTER TABLE `marks` DISABLE KEYS */;
/*!40000 ALTER TABLE `marks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notices`
--

DROP TABLE IF EXISTS `notices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `target_role` varchar(50) DEFAULT 'ALL',
  `department_id` int(11) DEFAULT NULL,
  `created_by` varchar(100) DEFAULT 'Administration',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_notices_department` (`department_id`),
  CONSTRAINT `fk_notices_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notices`
--

LOCK TABLES `notices` WRITE;
/*!40000 ALTER TABLE `notices` DISABLE KEYS */;
/*!40000 ALTER TABLE `notices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `staff_code` varchar(50) NOT NULL,
  `department_id` int(11) NOT NULL,
  `designation` varchar(50) DEFAULT 'Assistant Professor',
  `phone` varchar(20) DEFAULT NULL,
  `qualification` varchar(100) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `staff_code` (`staff_code`),
  KEY `fk_staff_department` (`department_id`),
  CONSTRAINT `fk_staff_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_staff_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff`
--

LOCK TABLES `staff` WRITE;
/*!40000 ALTER TABLE `staff` DISABLE KEYS */;
INSERT INTO `staff` VALUES (1,1,'HOD-ECE-01',1,'Head of Department','9876543200','Ph.D in Microelectronics, Senior IEEE Member',NULL),(2,2,'STF-ECE-01',1,'Assistant Professor','9876543201','M.Tech (VLSI), Ph.D',NULL),(3,3,'STF-ECE-02',1,'Assistant Professor','9876543202','M.Tech (Signal Processing)',NULL);
/*!40000 ALTER TABLE `staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `register_number` varchar(50) NOT NULL,
  `department_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `year` varchar(20) DEFAULT '3rd Year',
  `section` varchar(10) DEFAULT 'A',
  `batch_id` int(11) DEFAULT NULL,
  `mentor_id` int(11) DEFAULT NULL,
  `hod_id` int(11) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `register_number` (`register_number`),
  KEY `fk_students_department` (`department_id`),
  KEY `fk_students_course` (`course_id`),
  KEY `fk_students_batch` (`batch_id`),
  KEY `fk_students_mentor` (`mentor_id`),
  KEY `fk_students_hod` (`hod_id`),
  CONSTRAINT `fk_students_batch` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_students_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_students_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_students_hod` FOREIGN KEY (`hod_id`) REFERENCES `staff` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_students_mentor` FOREIGN KEY (`mentor_id`) REFERENCES `staff` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_students_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (1,4,'21EC001',1,NULL,'3rd Year','A',1,2,1,'2004-03-15','Male','9874561001','Room 204, Ganga Boys Hostel, Campus','Active','2026-09-21 02:40:59'),(2,5,'21EC002',1,NULL,'3rd Year','A',1,2,1,'2004-06-20','Female','9874561002','Greenwood Campus Enclave','Active','2026-09-21 02:41:00'),(3,6,'21EC003',1,NULL,'3rd Year','A',1,2,1,'2004-06-20','Male','9874561003','Greenwood Campus Enclave','Active','2026-09-21 02:41:00'),(4,7,'21EC004',1,NULL,'3rd Year','A',1,2,1,'2004-06-20','Female','9874561004','Greenwood Campus Enclave','Active','2026-09-21 02:41:01'),(5,8,'21EC005',1,NULL,'3rd Year','A',1,2,1,'2004-06-20','Male','9874561005','Greenwood Campus Enclave','Active','2026-09-21 02:41:02');
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_code` varchar(50) NOT NULL,
  `subject_name` varchar(150) NOT NULL,
  `department_id` int(11) NOT NULL,
  `semester` varchar(20) DEFAULT '5th Semester',
  `credits` int(11) DEFAULT 3,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subject_code` (`subject_code`),
  KEY `fk_subjects_department` (`department_id`),
  CONSTRAINT `fk_subjects_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
INSERT INTO `subjects` VALUES (1,'EC301','Database Management System',1,'5th Semester',4),(2,'EC302','Digital Signal Processing',1,'5th Semester',4),(3,'EC303','VLSI Design & Technology',1,'5th Semester',3),(4,'EC304','Communication Theory & Networks',1,'5th Semester',3),(5,'EC305','Microprocessors & Embedded Systems',1,'5th Semester',3),(6,'CS301','Algorithms & Data Structures',2,'5th Semester',4);
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timetable`
--

DROP TABLE IF EXISTS `timetable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `timetable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `day` varchar(20) NOT NULL,
  `period` int(11) NOT NULL,
  `start_time` varchar(20) NOT NULL,
  `end_time` varchar(20) NOT NULL,
  `department_id` int(11) NOT NULL,
  `year` varchar(20) NOT NULL,
  `section` varchar(10) NOT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `subject_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `room` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_timetable_department` (`department_id`),
  KEY `fk_timetable_batch` (`batch_id`),
  KEY `fk_timetable_subject` (`subject_id`),
  KEY `fk_timetable_faculty` (`faculty_id`),
  CONSTRAINT `fk_timetable_batch` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_timetable_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_timetable_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_timetable_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timetable`
--

LOCK TABLES `timetable` WRITE;
/*!40000 ALTER TABLE `timetable` DISABLE KEYS */;
INSERT INTO `timetable` VALUES (1,'Monday',1,'09:00 AM','10:00 AM',1,'3rd Year','A',1,1,2,'ECE Lab','2026-09-21 02:41:02'),(2,'Monday',2,'10:00 AM','11:00 AM',1,'3rd Year','A',NULL,2,2,'Hall 201','2026-09-21 02:41:02'),(3,'Monday',3,'11:15 AM','12:15 PM',1,'3rd Year','A',NULL,3,1,'Hall 201','2026-09-21 02:41:02'),(4,'Monday',4,'01:15 PM','02:15 PM',1,'3rd Year','A',1,4,2,'ECE Lab','2026-09-21 02:41:02'),(5,'Monday',5,'02:15 PM','03:15 PM',1,'3rd Year','A',NULL,5,2,'Room 304','2026-09-21 02:41:02'),(6,'Tuesday',1,'09:00 AM','10:00 AM',1,'3rd Year','A',1,1,2,'ECE Lab','2026-09-21 02:41:02'),(7,'Tuesday',2,'10:00 AM','11:00 AM',1,'3rd Year','A',NULL,2,2,'Hall 201','2026-09-21 02:41:02'),(8,'Tuesday',3,'11:15 AM','12:15 PM',1,'3rd Year','A',NULL,3,1,'Hall 201','2026-09-21 02:41:02'),(9,'Tuesday',4,'01:15 PM','02:15 PM',1,'3rd Year','A',1,4,2,'ECE Lab','2026-09-21 02:41:02'),(10,'Tuesday',5,'02:15 PM','03:15 PM',1,'3rd Year','A',NULL,5,2,'Room 304','2026-09-21 02:41:02'),(11,'Wednesday',1,'09:00 AM','10:00 AM',1,'3rd Year','A',1,1,2,'ECE Lab','2026-09-21 02:41:02'),(12,'Wednesday',2,'10:00 AM','11:00 AM',1,'3rd Year','A',NULL,2,2,'Hall 201','2026-09-21 02:41:02'),(13,'Wednesday',3,'11:15 AM','12:15 PM',1,'3rd Year','A',NULL,3,1,'Hall 201','2026-09-21 02:41:02'),(14,'Wednesday',4,'01:15 PM','02:15 PM',1,'3rd Year','A',1,4,2,'ECE Lab','2026-09-21 02:41:02'),(15,'Wednesday',5,'02:15 PM','03:15 PM',1,'3rd Year','A',NULL,5,2,'Room 304','2026-09-21 02:41:02'),(16,'Thursday',1,'09:00 AM','10:00 AM',1,'3rd Year','A',1,1,2,'ECE Lab','2026-09-21 02:41:02'),(17,'Thursday',2,'10:00 AM','11:00 AM',1,'3rd Year','A',NULL,2,2,'Hall 201','2026-09-21 02:41:02'),(18,'Thursday',3,'11:15 AM','12:15 PM',1,'3rd Year','A',NULL,3,1,'Hall 201','2026-09-21 02:41:02'),(19,'Thursday',4,'01:15 PM','02:15 PM',1,'3rd Year','A',1,4,2,'ECE Lab','2026-09-21 02:41:02'),(20,'Thursday',5,'02:15 PM','03:15 PM',1,'3rd Year','A',NULL,5,2,'Room 304','2026-09-21 02:41:02'),(21,'Friday',1,'09:00 AM','10:00 AM',1,'3rd Year','A',1,1,2,'ECE Lab','2026-09-21 02:41:02'),(22,'Friday',2,'10:00 AM','11:00 AM',1,'3rd Year','A',NULL,2,2,'Hall 201','2026-09-21 02:41:02'),(23,'Friday',3,'11:15 AM','12:15 PM',1,'3rd Year','A',NULL,3,1,'Hall 201','2026-09-21 02:41:02'),(24,'Friday',4,'01:15 PM','02:15 PM',1,'3rd Year','A',1,4,2,'ECE Lab','2026-09-21 02:41:02'),(25,'Friday',5,'02:15 PM','03:15 PM',1,'3rd Year','A',NULL,5,2,'Room 304','2026-09-21 02:41:02'),(26,'Saturday',1,'09:00 AM','10:00 AM',1,'3rd Year','A',1,1,2,'ECE Lab','2026-09-21 02:41:02'),(27,'Saturday',2,'10:00 AM','11:00 AM',1,'3rd Year','A',NULL,2,2,'Hall 201','2026-09-21 02:41:02'),(28,'Saturday',3,'11:15 AM','12:15 PM',1,'3rd Year','A',NULL,3,1,'Hall 201','2026-09-21 02:41:02'),(29,'Saturday',4,'01:15 PM','02:15 PM',1,'3rd Year','A',1,4,2,'ECE Lab','2026-09-21 02:41:02'),(30,'Saturday',5,'02:15 PM','03:15 PM',1,'3rd Year','A',NULL,5,2,'Room 304','2026-09-21 02:41:02');
/*!40000 ALTER TABLE `timetable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('STUDENT','ASSISTANT_PROFESSOR','HOD') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Dr. R. K. Sharma','hod1@gmail.com','$2b$12$uZdkFSG.YULE8ofSe6jZwO7CFg/EEpvMpIsXn5W08aNcihAd9tsOq','HOD',1,'2026-09-21 02:40:57'),(2,'Prof. Sarah Jenkins','staff1@gmail.com','$2b$12$jsl2LQic6rYQ.lAyYTpzqeILeg.PRYIRXYWKaNoU3f2OHgsG61muS','ASSISTANT_PROFESSOR',1,'2026-09-21 02:40:58'),(3,'Prof. David Miller','staff2@gmail.com','$2b$12$IojxreT4ABY10JAFhgN9hOWCeUDaXpXr9nUcF.l2dM6DTKjS7H9ri','ASSISTANT_PROFESSOR',1,'2026-09-21 02:40:58'),(4,'Student 1','student1@gmail.com','$2b$12$TseES6cgBVKZyrwpXSoHleZIBoALZuYVUGCmsqor9BVlVrWGDYuxO','STUDENT',1,'2026-09-21 02:40:59'),(5,'Student 2','student2@gmail.com','$2b$12$kpUut1108ZFdUmWG248LCuELvOzvBaY2qqs54wG32OB8kp8y9Jt4q','STUDENT',1,'2026-09-21 02:41:00'),(6,'Student 3','student3@gmail.com','$2b$12$IV1s5pLHykHWoe.kNNFKbOJAEySbt4R3RLNIIK.Fx1y1cfecBXrSC','STUDENT',1,'2026-09-21 02:41:00'),(7,'Student 4','student4@gmail.com','$2b$12$SYBhf5nHMSDScJ5A6BbjTOIscE1iHABSx0N5zHIPnbKqc76EjnJ4.','STUDENT',1,'2026-09-21 02:41:01'),(8,'Student 5','student5@gmail.com','$2b$12$5mRLlAUhDoDdMoHIRpQ7yOPG7DJS3QroGyQVf2j9a29RpwfpU/BrK','STUDENT',1,'2026-09-21 02:41:02');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-21 13:41:16
