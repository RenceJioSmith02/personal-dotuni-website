# 🎓 DotUni CMS

*A Laravel-based Content Management System with AdminLTE Dashboard*

---

## 📌 Overview

**DotUni CMS** is a web-based Content Management System developed using Laravel and AdminLTE. It is designed to streamline the management of website content such as news, galleries, and other administrative modules through a clean and user-friendly dashboard.

This system was developed as part of academic training (OJT/Capstone) and demonstrates modern web development practices including MVC architecture, AJAX interactions, and role-based authentication.

---

## 🚀 Features

### 🔐 Authentication & Authorization

* Secure login system
* Role-based access control (Admin/User)
* Protected routes using middleware

### 📰 Content Management

* News Management (Create, Read, Update, Delete)
* Gallery Management (Image upload & organization)

### 📊 Data Management

* DataTables integration
* Sorting, searching, and pagination
* Dynamic table updates via AJAX

### ⚡ System Functionality

* Modal-based forms
* Real-time UI updates
* CSRF protection
* Validation handling

### 🎨 User Interface

* AdminLTE dashboard template
* Responsive design
* FontAwesome icons integration

---

## 🛠️ Technology Stack

| Layer        | Technology               |
| ------------ | ------------------------ |
| Backend      | Laravel (PHP)            |
| Frontend     | HTML, CSS, JavaScript    |
| UI Framework | AdminLTE                 |
| Database     | MySQL                    |
| Libraries    | jQuery, DataTables, AJAX |

---

## ⚙️ Installation Guide

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/dotuni-cms.git
cd dotuni-cms
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Setup Environment

```bash
cp .env.example .env
```

Update `.env` file:

```env
APP_NAME=DotUniCMS
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dotuni_cms
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Run Migrations

```bash
php artisan migrate
```

(Optional: Seed database)

```bash
php artisan db:seed
```

### 6. Run the Application

```bash
php artisan serve
```

Open in browser:

```
http://127.0.0.1:8000
```

---

## 👤 Default Credentials (if seeded)

```
Admin Account:
Email: admin@example.com
Password: password
```

---

## 📂 Project Structure

```
app/
 ├── Http/
 │   ├── Controllers/
 │   ├── Middleware/
 │
 ├── Models/

resources/
 ├── views/
 │   ├── layouts/
 │   ├── admin/

routes/
 ├── web.php

database/
 ├── migrations/
 ├── seeders/

public/
 ├── assets/
```

---

## 🧑‍💻 Developer Guide

### 🧱 Architecture

The system follows the **MVC (Model-View-Controller)** architecture:

* **Model** → Handles database logic
* **View** → Blade templates (UI)
* **Controller** → Handles request logic

---

### 🔄 Typical CRUD Flow

1. User clicks action (Add/Edit/Delete)
2. Modal form appears
3. Form submitted via AJAX
4. Controller processes request
5. Data stored in database
6. JSON response returned
7. DataTable updates dynamically

---

### 🧾 Coding Practices

* Use RESTful controllers
* Follow Laravel naming conventions
* Validate all inputs
* Use AJAX for better UX
* Separate logic into services when needed

---

### 🏷️ Naming Conventions

| Component  | Format     |
| ---------- | ---------- |
| Controller | PascalCase |
| Model      | Singular   |
| Table      | Plural     |
| Route      | kebab-case |

---

## 📘 User Manual

### 🔐 Login

1. Open the system
2. Enter credentials
3. Click **Login**

---

### 📰 Managing News

* Navigate to **News Module**
* Click **Add News**
* Fill in required fields
* Click **Save**

Actions available:

* Edit news
* Delete news
* Search & filter

---

### 🖼️ Managing Gallery

* Go to **Gallery Module**
* Upload images
* View image list
* Edit or delete items

---

### 📊 Using DataTables

* Use search bar to filter data
* Click column headers to sort
* Navigate pages using pagination controls

---

## 🧪 Testing

Run automated tests:

```bash
php artisan test
```

---

## 🚀 Deployment Guide

### Requirements

* PHP >= 8.x
* Composer
* MySQL
* Apache/Nginx

### Steps

1. Upload project to server
2. Configure `.env`
3. Run:

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan optimize
```

4. Set permissions:

```bash
chmod -R 775 storage bootstrap/cache
```

---

## 🔐 Security Features

* CSRF protection
* Input validation
* Authentication middleware
* Role-based authorization

---

## 🐛 Known Issues

* DataTable sorting issue on computed columns
* Some AdminLTE icons may not render due to FontAwesome version mismatch

---

## 🔮 Future Improvements

* API integration
* Role & permission management module
* Activity logs
* Dashboard analytics
* File manager system

---

## 🤝 Contribution Guide

1. Fork the repository
2. Create a new branch

```bash
git checkout -b feature/your-feature
```

3. Commit changes
4. Push to GitHub
5. Create Pull Request

---

## 📄 License

This project is intended for academic and internal use.

---

## 👨‍💻 Developers

* Rence Dingle
* (Add your team members here)

---

## 📞 Support

For inquiries or issues:

📧 Email: [your-email@example.com](mailto:your-email@example.com)

---

## ⭐ Acknowledgements

* Laravel Framework
* AdminLTE Template
* Open-source community

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
