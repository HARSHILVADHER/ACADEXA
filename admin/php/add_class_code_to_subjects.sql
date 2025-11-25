-- SQL Query to add class_code column to subjects table
ALTER TABLE subjects ADD COLUMN class_code VARCHAR(50) AFTER subject_code;
