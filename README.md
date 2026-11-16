# Student Management System (SMS)

A modern, responsive, full-featured web-based Student Management System built with a PHP RESTful API backend, MySQL database, and a high-performance modern UI supporting dark and light themes with role-based access control (RBAC).

---

## 🌟 Key Features

### 1. Role-Based Portals & Dashboards
- **Student Portal**:
  - Live attendance overview with percentage calculations and exam eligibility indicators.
  - Weekly timetable and daily period breakdown.
  - Department examination schedule with venue details.
  - Leave application system with mentor review tracking and live status updates.
  - Assigned academic mentor profile and announcements.
- **Faculty / Staff Portal**:
  - Daily schedule and class period overview.
  - Mark and review student attendance by batch and subject.
  - View full department timetable and exam schedules.
  - Review and recommend student leave applications.
  - Department enrolled students directory.
- **Head of Department (HOD) Portal**:
  - Comprehensive department analytics and faculty management.
  - Timetable governance: add, edit, or adjust period allocations with automatic conflict detection.
  - Examination schedule management and publishing.
  - Final approval authority for student leave applications.
  - Subject allocation and batch management.
- **Admin Portal**:
  - Institution-wide management of departments, users, batches, and academic calendars.

### 2. Modern UI & User Experience
- **Responsive Glassmorphic Design**: Polished dark and light themes with smooth transitions, CSS variables, and fluid responsive layouts.
- **SPA View Switcher**: Instant tab and view transitions without full page reloads.
- **Componentized Design**: Modern badge pills, data tables, quick action cards, and stat widgets.

### 3. Backend & Security
- **RESTful PHP API**: Clean JSON responses with standardized error handling and status codes.
- **Session Authentication & RBAC**: Strict session-based access control protecting all backend endpoints.
- **SQL Prepared Statements**: Protection against SQL injection across all database queries.

---

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3 (Custom Glassmorphic Theme, Variables, Flexbox/Grid), JavaScript (Vanilla ES6+ Fetch API, DOM manipulation).
- **Backend**: PHP 8.x (REST API endpoints, Session Authentication, JSON serialization).
- **Database**: MySQL / MariaDB (Relational schema with foreign keys, indexes, and cascades).
- **Server Environment**: Apache / XAMPP / PHP Built-in Server.

---

## 🚀 Getting Started

### Prerequisites
- PHP 8.0 or higher
- MySQL / MariaDB (via XAMPP, WAMP, or standalone)
- Web server (Apache or PHP built-in server)

### Installation & Setup

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/shreedharg405-sketch/student-management-system.git
   cd student-management-system
   ```

2. **Database Configuration**:
   - Create a MySQL database (e.g., `studentms`).
   - Import the schema from `backend/database/studentms.sql` or run the database setup script:
     ```sql
     CREATE DATABASE studentms;
     USE studentms;
     SOURCE backend/database/studentms.sql;
     ```
   - Update database credentials in `backend/config/db.php` if needed (default: `localhost`, user `root`, no password).

3. **Run the Application**:
   - Using XAMPP: Place the folder inside `htdocs/studentms` and start Apache and MySQL from XAMPP Control Panel.
   - Or using the PHP built-in server:
     ```bash
     php -S localhost:8080
     ```

4. **Access the Portal**:
   - Open your browser and navigate to: `http://localhost:8080/frontend/login.html` (or `http://localhost/studentms/`).

---

## 👤 Author
- **Shreedhar G** ([@shreedharg405-sketch](https://github.com/shreedharg405-sketch))
