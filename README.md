# RNCrud - Laravel CRUD Generator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rafaelnuansa/rncrud.svg?style=flat-square)](https://packagist.org/packages/rafaelnuansa/rncrud)
[![Total Downloads](https://img.shields.io/packagist/dt/rafaelnuansa/rncrud.svg?style=flat-square)](https://packagist.org/packages/rafaelnuansa/rncrud)
[![License](https://img.shields.io/packagist/l/rafaelnuansa/rncrud.svg?style=flat-square)](https://packagist.org/packages/rafaelnuansa/rncrud)

**RNCrud** is a Laravel package designed to speed up development by automatically generating CRUD boilerplate.

With a single Artisan command, you can instantly create:

* Model
* Migration
* Controller (Resource)
* Route

---

## ✨ Features

* 🚀 **One Command Setup**
  Generate all CRUD files in seconds.

* 📦 **Complete Boilerplate**
  Automatically creates Model, Migration, and Controller.

* 🔗 **Auto Routing**
  Adds `Route::resource()` directly to your `web.php`.

* 🛠 **Customizable Stubs**
  Publish and modify templates to match your coding style.

---

## 📋 Requirements

* PHP: `^8.1 - ^8.5`
* Laravel: `^10.0 | ^11.0 | ^12.0 | ^13.0`

---

## 📦 Installation

Install via Composer:

```bash
composer require rafaelnuansa/rncrud
```

---

## 🚀 Usage

Run the Artisan command:

```bash
php artisan make:crud ModelName
```

### Example

```bash
php artisan make:crud Category
```

---

## 📁 Generated Output

After running the command, RNCrud will generate:

* **Model**
  `app/Models/Category.php`

* **Controller**
  `app/Http/Controllers/CategoryController.php`
  *(includes: index, create, store, edit, update, destroy)*

* **Migration**
  `database/migrations/YYYY_MM_DD_HHMMSS_create_categories_table.php`

* **Route**
  Automatically adds:

  ```php
  Route::resource('categories', CategoryController::class);
  ```

Additionally, a summary table will be displayed in the terminal showing all generated file locations.

---

## 🧩 Customizing Templates (Stubs)

Publish the stub files:

```bash
php artisan vendor:publish --tag=rncrud-stubs
```

Stubs will be available at:

```
stubs/vendor/rncrud/
```

You can modify these `.stub` files to fit your project needs (e.g., admin panel, UI framework, coding style).

---

## 📄 License

This package is open-sourced under the **MIT License**.
See the `LICENSE` file for details.

---

## 👨‍💻 Author

Developed by **Rafael Nuansa**
