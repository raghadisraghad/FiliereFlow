# FiliereFlow

**Student & Filiere Management System**

FiliereFlow is a Laravel + Vite application designed for **administrators to manage students and filieres**, and for **users (students) to register, log in, and view the filieres they belong to**.

---

## Requirements

* PHP >= 8.1
* Composer
* Node.js >= 18
* NPM >= 9
* SQLite or MySQL (SQLite recommended)
* Git

---

## Setup

### 1. Clone the repository

```bash
git clone https://github.com/raghadisraghad/FiliereFlow.git
cd FiliereFlow
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Environment setup

```bash
cp .env.example .env
```

For SQLite:

```bash
touch database/database.sqlite
```

Update `.env` with database settings and admin credentials.

### 4. Generate app key

```bash
php artisan key:generate
```

### 5. Run migrations & seed data

```bash
php artisan migrate --seed
```

### 6. Create storage symlink (REQUIRED for profile pictures)

```bash
php artisan storage:link
```

---

## Running the Project

Run Laravel + Vite together:

```bash
npm run serve
```

Access the app at:

```
http://127.0.0.1:8000
```

---

## Features

### Admin

* Manage users (students & admins)
* Create, edit, and delete filieres
* Enroll students into multiple filieres
* Remove students from filieres
* Dashboard with overview
* Full user management

### Students / Users

* Register & login
* View dashboard
* See filieres they are enrolled in
* Update profile information
* Upload & update profile picture
* Change password

---

## Tech Stack

* Laravel 10
* Vite
* Tailwind CSS
* SQLite / MySQL
* Laravel Fortify (Authentication)

---

## Notes

* `php artisan storage:link` is required for profile images to display correctly.
* Use `npm run dev` during development for hot reload.
* Configure `.env` properly before production deployment.

---

## License

MIT License
