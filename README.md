# Employee Management System

A **web-based employee management system** built with **Laravel 12** and **PHP 8.2**, designed to manage staff information, profile images, and roles. This project includes both admin and user access for testing.

---

## **Database Setup**

1. I have provided a database file named **`sample-php.sql`** containing intial setup- later you can just migrate through bash.
2. Create a new database in your system with db_name: sample-php.
3. Import the **`sample-php.sql`** file into the newly created database.
    - This will import all necessary tables along with sample data.
      Run: php artisan storage:link

---

## **Trial Users**

You can use the following credentials to log in:

| Role  | Email             | Password |
| ----- | ----------------- | -------- |
| Admin | admin@example.com | admin123 |
| Staff | sg@example.com    | 12345    |

---

## Installation & Setup

1. Clone the repository:

```bash
git clone https://github.com/sarishmaghale/employees-app.git
cd employees-app
```

Install Dependencies and other required commands:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate

```

---

## Folder structure

```
employees-app/
│
├── app/
│ ├── Http/
│ │ └── Controllers/ # Handles routing & validation
│ └── Repositories/ # Handles backend logic & database operations
│
├── resources/
│ └── views/ # Blade templates (UI)
│
├── public/
│ ├── css/ # CSS files
│ ├── js/ # JS files
│ └── storage/ # Linked to storage/app/public for images
│
├── routes/
│ └── web.php # Application routes
│
└── README.md
```
