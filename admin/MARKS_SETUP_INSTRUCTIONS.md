# Marks Management System - Setup Instructions

## Database Setup

### Step 1: Create the Marks Table
Run the SQL query from `marks_table.sql` in your MySQL database:

```sql
CREATE TABLE IF NOT EXISTS marks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_code VARCHAR(50) NOT NULL,
    student_roll_no VARCHAR(50) NOT NULL,
    student_name VARCHAR(255) NOT NULL,
    exam_name VARCHAR(255) NOT NULL,
    exam_date DATE NOT NULL,
    passing_marks INT NOT NULL,
    total_marks INT NOT NULL,
    actual_marks INT DEFAULT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_student_exam (student_roll_no, exam_name, user_id),
    INDEX idx_class_code (class_code),
    INDEX idx_user_id (user_id),
    INDEX idx_exam_name (exam_name)
);
```

## Features Implemented

### 1. Edit Exam Functionality (all_exam.html)
- Click the pen icon on any exam card to open edit modal
- Edit date, time, total marks, and passing marks
- Click "Save Update" to save changes to database
- Click "Add Marks" button to navigate to marks entry page

### 2. Add Marks Page (add_marks.php)
- Displays exam details at the top (exam name, class name, date, marks)
- Shows all students from the class in a table
- Each student has a marks input field with validation
- Marks cannot exceed total marks
- Three action buttons:
  - **Save Marks**: Saves all entered marks to database
  - **Download Sample CSV**: Downloads a CSV template with student data
  - **Import Marks**: Imports marks from CSV file

### 3. CSV Import/Export Features
- **Download Sample**: Generates CSV with Roll No, Student Name, and Marks columns
- **Import Marks**: 
  - Validates student names match exactly
  - Checks roll numbers exist in the class
  - Prevents duplicate entries
  - Validates marks are within range (0 to total marks)
  - Shows detailed error messages for any issues

### 4. Multi-User Support
- All queries filter by user_id from session
- Each user only sees their own exams and marks
- Prevents data leakage between users

### 5. Database Features
- Unique constraint prevents duplicate marks for same student-exam combination
- ON DUPLICATE KEY UPDATE allows updating existing marks
- Indexes for better query performance
- Timestamps track when marks were created/updated

## File Structure

```
admin/
├── all_exam.html (Updated with edit modal)
├── add_marks.php (New - marks entry page)
├── marks_table.sql (Database schema)
├── php/
│   ├── update_exam.php (New - updates exam details)
│   ├── save_marks.php (New - saves marks to database)
│   ├── get_all_exams.php (Updated - includes class_code)
│   └── config.php (Existing - database connection)
```

## Usage Flow

1. User views all exams in `all_exam.html`
2. Clicks pen icon to edit exam details
3. In edit modal, clicks "Add Marks" button
4. Navigates to `add_marks.php?exam_id=X`
5. Enters marks manually OR imports from CSV
6. Clicks "Save Marks" to store in database
7. Marks are saved with duplicate prevention

## CSV Format

The CSV file should have exactly 3 columns:
```
Roll No,Student Name,Marks Obtained
101,John Doe,85
102,Jane Smith,92
```

**Important**: 
- Student names must match exactly (case-sensitive)
- Roll numbers must exist in the class
- Marks must be between 0 and total marks
- No duplicate entries allowed

## Security Features

- Session-based authentication
- SQL injection prevention using prepared statements
- Input validation on both client and server side
- User isolation (multi-user support)
- XSS prevention with htmlspecialchars()

## Browser Compatibility

- Modern browsers (Chrome, Firefox, Safari, Edge)
- Responsive design for mobile devices
- CSV import/export works in all modern browsers
