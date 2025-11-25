-- SQL Query to add subject column to exam table
ALTER TABLE exam ADD COLUMN subject VARCHAR(50) AFTER exam_name;
