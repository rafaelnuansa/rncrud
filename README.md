# RNCrud - Advanced Laravel CRUD Generator 🚀

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rafaelnuansa/rncrud.svg?style=flat-square)](https://packagist.org/packages/rafaelnuansa/rncrud)
[![Total Downloads](https://img.shields.io/packagist/dt/rafaelnuansa/rncrud.svg?style=flat-square)](https://packagist.org/packages/rafaelnuansa/rncrud)
[![License](https://img.shields.io/packagist/l/rafaelnuansa/rncrud.svg?style=flat-square)](https://packagist.org/packages/rafaelnuansa/rncrud)

**RNCrud** is a professional Laravel package designed to accelerate development by generating smart, interactive, and flexible CRUD boilerplates. It is perfect for building both APIs and Blade-based applications.

---

## ✨ Features (v1.0.6)

* 🤖 **Interactive Prompts**  
  Visually choose which files to generate (Model, Controller, Migration, Views, or Routes).

* 🌐 **API & Web Support**  
  Choose between a standard Controller (Blade) or an API Controller (JSON response).

* 📂 **Multi-Namespace Support**  
  Create files inside sub-folders (e.g., `Admin/Product`).

* 🔗 **Smart ORM Relations**  
  Automatically detects foreign keys (ending with `_id`) to generate `belongsTo` relationships in Models and `constrained()` in Migrations.

* 🛠 **Dynamic Field Generation**  
  Automatically generates Migration columns, Controller validation, and Blade forms/tables based on `--fields` input.

* 🎨 **Tailwind Ready**  
  Generates Blade files (`index`, `create`, `edit`, `show`) with basic Tailwind CSS styling.

---

## 📋 Requirements

* PHP: `^8.1 - ^8.5`
* Laravel: `^10.0 | ^11.0 | ^12.0 | ^13.0`

---

## 📦 Installation

Install via Composer:

```bash
composer require rafaelnuansa/rncrud
````

---

## 🚀 Usage

Run the Artisan command:

```bash
php artisan make:crud ModelName --fields="column_name:type"
```

### Pro Example

Create a Product CRUD inside an Admin folder with a relation to Category:

```bash
php artisan make:crud Admin/Product --fields="category_id:foreign,name:string,price:integer,description:text"
```

### How It Works

1. **Interactive Prompt**
   RNCrud asks whether you want **Web (Blade)** or **API (JSON)** mode.

2. **File Selection**
   Select which files you want to generate (e.g., skip Routes if you prefer manual setup).

3. **Smart Logic**

   * **Migration**: `category_id` automatically becomes `foreignId()->constrained()`.
   * **Model**: Automatically includes a `category()` relationship method.
   * **Views**: Generates data tables and input forms based on defined fields.
   * **Routes**: Appends `Route::resource` to the appropriate file (`web.php` or `api.php`).

---

## 🛠 Command Options

| Option     | Description                                               |
| ---------- | --------------------------------------------------------- |
| `--fields` | Define database columns (Format: `name:type,name2:type`). |
| `--force`  | Overwrite existing files without warning.                 |

---

## 🧩 Customizing Templates (Stubs)

Want to customize the Blade UI (e.g., switch to Bootstrap) or modify the default Controller structure? Publish the stubs:

```bash
php artisan vendor:publish --tag=rncrud-stubs
```

The files will be available in:

```
stubs/vendor/rncrud/
```

---

## 📁 Output Structure

* **Model**: `app/Models/Product.php`
* **Controller**: `app/Http/Controllers/Admin/ProductController.php`
* **Migration**: `database/migrations/YYYY_MM_DD_create_products_table.php`
* **Views**: `resources/views/products/*.blade.php`
* **Route**: Automatically registered in `web.php` or `api.php`

---

## 📄 License

This package is open-sourced under the **MIT License**.

---

## 👨‍💻 Author

Developed by **[Rafael Nuansa](https://github.com/rafaelnuansa)**.
