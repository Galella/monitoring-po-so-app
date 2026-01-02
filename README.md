# Trucking Management App

A comprehensive trucking management system built with **Laravel 12** and **Filament 4**. This application is designed to streamline trucking operations, featuring a robust User and Role management foundation.

## 🚀 Features

- **User Management**: Complete system to manage application users with custom profiles.
- **Role-Based Access Control (RBAC)**: Powered by [Filament Shield](https://filamentphp.com/plugins/bezhansalleh-shield), allowing for granular permission control and dynamic role assignment through the UI.
- **Modern Admin Panel**: Built on top of Filament 4 for a responsive and intuitive user interface.

## 🛠 Tech Stack

- **Framework**: Laravel 12
- **Admin Panel**: Filament 4
- **RBAC**: Filament Shield 4
- **Language**: PHP 8.2+

## ⚙️ Installation

### Quick Setup

You can use the built-in setup script to get up and running quickly:

```bash
composer run setup
```

### Manual Setup

If you prefer to run steps manually:

1.  **Clone the repository**

    ```bash
    git clone <repository_url>
    cd trucking-app
    ```

2.  **Install Dependencies**

    ```bash
    composer install
    npm install && npm run build
    ```

3.  **Environment Configuration**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

    _Configure your database settings in the `.env` file._

4.  **Database Migration**

    ```bash
    php artisan migrate
    ```

5.  **Initialize Permissions**
    If this is a fresh install, set up the shield permissions:

    ```bash
    php artisan shield:install
    ```

6.  **Create Admin User**

    ```bash
    php artisan make:filament-user
    ```

7.  **Run the Local Server**

    ```bash
    php artisan serve
    ```

    Visit `http://localhost:8000/admin` to access the dashboard.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
# monitoring-po-so-app
# monitoring-po-so-app
