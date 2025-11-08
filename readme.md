# Stock Management System &nbsp;📦

[![PHP](https://img.shields.io/badge/PHP-7.1%2B-8892bf?logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-5.5-ff2d20?logo=laravel&logoColor=white)](https://laravel.com/)
[![License](https://img.shields.io/badge/License-MIT-0a0a0a.svg)](LICENSE)
[![Made With Love](https://img.shields.io/badge/Made%20with-%E2%9D%A4-ff69b4)](#-overview)

> 🚀 A modern Laravel application for teams that need a single source of truth for stock levels, supplier relationships, and purchasing workflows.

---

## 🧭 Table of Contents
- [✨ Overview](#-overview)
- [🌟 Key Features](#-key-features)
- [🧱 Technology Stack](#-technology-stack)
- [🗂️ Project Structure](#️-project-structure)
- [⚙️ Getting Started](#️-getting-started)
- [🛠 Command Cheat Sheet](#-command-cheat-sheet)
- [🧪 Testing](#-testing)
- [💾 Database & Seeding](#-database--seeding)
- [🖼 Screenshots](#-screenshots)
- [🚧 Roadmap](#-roadmap)
- [🤝 Contributing](#-contributing)
- [📄 License](#-license)

---

## ✨ Overview
Stock Management System helps retail and wholesale teams keep inventory, supplier, and purchasing data consistent and actionable. The platform streamlines the full lifecycle of stock management—from cataloging brands and SKUs to reconciling supplier invoices and generating printable PDFs for every transaction.

---

## 🌟 Key Features
- **Centralized Inventory**: Create, categorize, and update product data with real-time stock balances.
- **Invoice Engine**: Produce PDF invoices for new stock entries and adjustments with a single click.
- **Supplier CRM**: Track supplier metadata, payment history, and outstanding balances in one module.
- **Payment Configuration**: Tailor payment methods to match internal accounting guidelines.
- **Audit-Ready Logs**: Preserve every movement in a relational database for later reporting.
- **Responsive UI**: Bootstrap-powered dashboards provide clarity across devices.

---

## 🧱 Technology Stack
- **Backend**: Laravel 5.5, PHP 7.1+
- **Frontend**: Blade templates, Bootstrap, vanilla JavaScript
- **Tooling**: Laravel Mix, Webpack, npm
- **Database**: MySQL (or any Laravel-supported relational database)
- **PDF Generation**: DomPDF integration for high-fidelity invoice exports

---

## 🗂️ Project Structure
```text
Stock-Management/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Requests/
│   │   └── Resources/
│   ├── Models/
│   └── Services/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── public/
├── resources/
│   ├── assets/
│   └── views/
├── routes/
├── tests/
└── readme.md
```

---

## ⚙️ Getting Started
1. **Clone the repository**
   ```bash
   git clone https://github.com/sangit0/Stock-Management.git
   cd Stock-Management
   ```
2. **Install PHP dependencies**
   ```bash
   composer install
   ```
3. **Install JavaScript dependencies**
   ```bash
   npm install
   ```
4. **Bootstrap the environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
5. **Configure the database**
   Update `.env` with your database credentials and queue/mail driver settings.
6. **Run migrations and seeders**
   ```bash
   php artisan migrate --seed
   ```
7. **Serve the application**
   ```bash
   php artisan serve
   ```

---

## 🛠 Command Cheat Sheet
```bash
# Start the local development server
php artisan serve

# Compile assets once
npm run dev

# Watch assets with hot reload
npm run watch

# Clear compiled caches
php artisan cache:clear
php artisan config:clear

# Run the test suite
php artisan test
```

---

## 🧪 Testing
- Feature and integration tests live under `tests/Feature`.
- Execute all tests with:
  ```bash
  php artisan test
  ```
- Use `php artisan test --filter=SupplierManagementTest` to target a single suite.

---

## 💾 Database & Seeding
- Default seeders provision demo products, suppliers, and sample adjustments.
- To refresh the database with seed data:
  ```bash
  php artisan migrate:fresh --seed
  ```
- Customize seed data inside `database/seeders` to reflect your business context.

---

## 🖼 Screenshots
![Dashboard view](https://github.com/sangit0/Stock-Management/blob/master/screenshot/1.png)
![Stock management](https://github.com/sangit0/Stock-Management/blob/master/screenshot/2.png)
![Invoice PDF](https://github.com/sangit0/Stock-Management/blob/master/screenshot/10.png)
![Supplier payment](https://github.com/sangit0/Stock-Management/blob/master/screenshot/7.png)
![Brand settings](https://github.com/sangit0/Stock-Management/blob/master/screenshot/6.png)
![Product list](https://github.com/sangit0/Stock-Management/blob/master/screenshot/8.png)
![Payment methods](https://github.com/sangit0/Stock-Management/blob/master/screenshot/9.png)
![Stock entry](https://github.com/sangit0/Stock-Management/blob/master/screenshot/3.png)
![Reports](https://github.com/sangit0/Stock-Management/blob/master/screenshot/4.png)

---

## 🚧 Roadmap
- [ ] REST API endpoints for inventory syncing
- [ ] Multi-warehouse support with stock transfers
- [ ] Notifications for low-stock alerts
- [ ] Role-based access control for audit compliance

---

## 🤝 Contributing
Contributions, bug reports, and feature requests are always welcome! Please open an issue to discuss proposed changes or submit a pull request aligned with our coding standards.

---

## 📄 License
This project is distributed under the MIT License. See the `LICENSE` file for details.
