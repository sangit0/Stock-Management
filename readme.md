# Stock Management System

A Laravel-based application for managing inventory, suppliers, and purchasing workflows. It provides tooling for tracking stock levels, generating invoices, and streamlining supplier payments.

## Features
- Manage products, brands, and style/category settings.
- Create and update stock entries, including adjustments to existing invoices.
- Generate PDF invoices for orders and stock movements.
- Maintain supplier records and track payments through a dedicated module.
- Configure payment methods to align with your business processes.

## Technology Stack
- Laravel 5.5 (PHP framework)
- Bootstrap and JavaScript for the user interface
- Laravel Mix and Webpack for asset compilation
- Node.js modules for front-end tooling

## Prerequisites
- PHP 7.1 or newer with the required Laravel extensions
- Composer
- Node.js and npm
- A configured database (MySQL or compatible)

## Installation
1. Clone the repository and navigate to the project directory.
2. Install PHP dependencies:
   ```bash
   composer install
   ```
3. Install JavaScript dependencies:
   ```bash
   npm install
   ```
4. Copy the example environment file and update it with your configuration:
   ```bash
   cp .env.example .env
   ```
5. Generate the application key:
   ```bash
   php artisan key:generate
   ```
6. Run the database migrations and seeders:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

## Usage
- Start the development server:
  ```bash
  php artisan serve
  ```
- Compile assets with hot reloading:
  ```bash
  npm run watch
  ```

## Screenshots
![Dashboard view](https://github.com/sangit0/Stock-Management/blob/master/screenshot/1.png)
![Stock management](https://github.com/sangit0/Stock-Management/blob/master/screenshot/2.png)
![Invoice PDF](https://github.com/sangit0/Stock-Management/blob/master/screenshot/10.png)
![Supplier payment](https://github.com/sangit0/Stock-Management/blob/master/screenshot/7.png)
![Brand settings](https://github.com/sangit0/Stock-Management/blob/master/screenshot/6.png)
![Product list](https://github.com/sangit0/Stock-Management/blob/master/screenshot/8.png)
![Payment methods](https://github.com/sangit0/Stock-Management/blob/master/screenshot/9.png)
![Stock entry](https://github.com/sangit0/Stock-Management/blob/master/screenshot/3.png)
![Reports](https://github.com/sangit0/Stock-Management/blob/master/screenshot/4.png)
