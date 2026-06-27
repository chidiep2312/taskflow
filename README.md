# 🌸 TaskFlow - Project Management System

TaskFlow is a full-stack Project Management System built with **Laravel 10**, **Vue 3**, and **MySQL**. The project follows a clean architecture using the **Service Layer** and **Repository Pattern** to simulate a real-world project collaboration platform.

## ✨ Features

* User Authentication (Laravel Sanctum)
* Project Management
* Task Management (Kanban Board)
* Project Members (Owner / Member / Viewer)
* Task Comments
* File Attachments
* Activity Logs
* Dashboard Statistics
* Role-based Authorization (Policy)

## 🛠 Tech Stack

### Backend

* Laravel 10
* PHP 8.2+
* MySQL
* Laravel Sanctum

### Frontend

* Vue 3
* Composition API
* Pinia
* Vue Router
* Axios
* Bootstrap 5

## 🏗 Architecture

```
Controller
    ↓
Service
    ↓
Repository
    ↓
Model
    ↓
MySQL
```

## 🚀 Installation

### Backend

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan serve
```

### Frontend

```bash
npm install
npm run dev
```

## 📚 Key Concepts

* Repository Pattern
* Service Layer
* Form Request
* API Resource
* Policy Authorization
* Eloquent Relationship
* Database Transaction
* File Upload
* RESTful API

## 📄 License

This project is for learning and portfolio purposes.
