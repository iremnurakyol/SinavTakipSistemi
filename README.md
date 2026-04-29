# Student Exam Tracking System

A full-stack web application that enables students to track their TYT/AYT exam performance, analyze progress with interactive visualizations, and compare rankings within an institution.

---
## Purpose

This system is designed to help students track exam performance, identify weak subjects, and improve success through data-driven analysis.
##  Features

### Interactive Dashboard
- Real-time score visualization using Chart.js  
- Subject-based filtering and historical comparison  

### Exam Management
- Automated net score calculation (TYT/AYT format)  
- Multi-exam tracking and performance history  

### Ranking System
- Dynamic leaderboard based on student performance  
- Real-time ranking updates  

### Admin Panel
- Student and exam management  
- Score entry and system configuration  

### Security
- Password hashing (bcrypt)  
- Session-based authentication  
- SQL Injection protection (prepared statements)  

### Reporting
- Exportable exam reports  
- Performance analysis tools  

---

## Tech Stack

- Backend: PHP 8.x  
- Database: MySQL  
- Frontend: HTML5, CSS3, JavaScript  
- Charts: Chart.js  

---

## Installation

- Clone the repository  
- Set up XAMPP / WAMP  
- Create database: `db_sinav_takip`  
- Import `veritabani.sql`  
- Configure `config.php`  

---

## Test Accounts

| Role | Email | Password |
|------|------|----------|
| Administrator | admin@test.com | 123 |
| Student (Ali) | ali@test.com | 123 |
