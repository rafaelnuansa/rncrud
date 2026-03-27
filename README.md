# RNCrud - Advanced Laravel CRUD Generator 🚀

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rafaelnuansa/rncrud.svg?style=flat-square)](https://packagist.org/packages/rafaelnuansa/rncrud)
[![Total Downloads](https://img.shields.io/packagist/dt/rafaelnuansa/rncrud.svg?style=flat-square)](https://packagist.org/packages/rafaelnuansa/rncrud)
[![License](https://img.shields.io/packagist/l/rafaelnuansa/rncrud.svg?style=flat-square)](https://packagist.org/packages/rafaelnuansa/rncrud)

**RNCrud** is a professional Laravel package designed to accelerate development by generating smart, interactive, and flexible CRUD boilerplates. Perfect for building both APIs and Blade-based applications.

**RNCrud** adalah Laravel package profesional untuk mempercepat development dengan men-generate boilerplate CRUD secara cerdas, interaktif, dan fleksibel. Cocok untuk membangun API maupun aplikasi berbasis Blade.

---

## ✨ Features / Fitur Unggulan (v1.0.6)

* 🤖 **Interactive Prompts**: Visually choose which files to generate (Model, Controller, Migration, Views, or Routes).
    *Pilih secara visual file apa saja yang ingin di-generate.*
* 🌐 **API & Web Support**: Choose between standard Controller (Blade) or API Controller (JSON response).
    *Pilih antara Controller standar (Blade) atau API Controller (JSON response).*
* 📂 **Multi-Namespace Support**: Create files inside sub-folders (e.g., `Admin/Product`).
    *Bisa membuat file di dalam sub-folder (Contoh: `Admin/Product`).*
* 🔗 **Smart ORM Relations**: Automatically detects foreign keys (ending in `_id`) to create `belongsTo` relations in Models and `constrained` in Migrations.
    *Otomatis mendeteksi foreign key (akhiran `_id`) untuk membuat relasi `belongsTo` di Model dan `constrained` di Migration.*
* 🛠 **Dynamic Field Generation**: Automatically writes Migration columns, Controller validation, and Blade forms/tables based on `--fields` input.
    *Menulis kolom Migration, validasi Controller, dan form/table Blade secara otomatis berdasarkan input `--fields`.*
* 🎨 **Tailwind Ready**: Automatically generates Blade files (`index`, `create`, `edit`, `show`) with basic Tailwind CSS styling.
    *Otomatis men-generate file Blade dengan styling dasar Tailwind CSS.*

---

## 📋 Requirements / Persyaratan

* PHP: `^8.1 - ^8.5`
* Laravel: `^10.0 | ^11.0 | ^12.0 | ^13.0`

---

## 📦 Installation / Instalasi

Install via Composer:

```bash
composer require rafaelnuansa/rncrud
```

---

## 🚀 Usage / Cara Penggunaan

Run the Artisan command / Jalankan perintah Artisan:

```bash
php artisan make:crud ModelName --fields="column_name:type"
```

### Pro Example / Contoh Pro

To create a Product CRUD inside an Admin folder with a relation to Category:
*Membuat CRUD Produk di dalam folder Admin dengan relasi ke Kategori:*

```bash
php artisan make:crud Admin/Product --fields="category_id:foreign,name:string,price:integer,description:text"
```

### How it works / Cara Kerjanya:
1.  **Interactive Prompt**: RNCrud asks if you want **Web (Blade)** or **API (JSON)** mode.
    *RNCrud bertanya apakah Anda ingin mode Web atau API.*
2.  **File Selection**: Check which files you want to create (e.g., skip Routes if you prefer manual setup).
    *Pilih file mana saja yang ingin dibuat (Misal: lewati Route jika ingin manual).*
3.  **Smart Logic**:
    * **Migration**: `category_id` automatically becomes `foreignId()->constrained()`.
    * **Model**: Automatically includes `public function category()`.
    * **Views**: Generates data tables and input forms based on requested fields.
    * **Routes**: Appends `Route::resource` to the correct file (`web.php` or `api.php`).

---

## 🛠 Command Options / Opsi Perintah

| Option | Description / Deskripsi |
| --- | --- |
| `--fields` | Define database columns (Format: `name:type,name2:type`). |
| `--force` | Overwrite existing files without warning / Timpa file yang sudah ada. |

---

## 🧩 Customizing Templates (Stubs)

Want to change the Blade look to Bootstrap or modify the default Controller structure? Publish the stubs:
*Ingin mengubah tampilan ke Bootstrap atau memodifikasi struktur Controller? Publish stub-nya:*

```bash
php artisan vendor:publish --tag=rncrud-stubs
```

Files will be available at `stubs/vendor/rncrud/`.
*File akan tersedia di folder `stubs/vendor/rncrud/`.*

---

## 📁 Output Structure / Struktur Output

* **Model**: `app/Models/Product.php`
* **Controller**: `app/Http/Controllers/Admin/ProductController.php`
* **Migration**: `database/migrations/YYYY_MM_DD_create_products_table.php`
* **Views**: `resources/views/products/*.blade.php`
* **Route**: Automatically registered in `web.php` or `api.php`.

---

## 📄 License / Lisensi

This package is open-sourced under the **MIT License**.

---

## 👨‍💻 Author

Developed by **[Rafael Nuansa](https://github.com/rafaelnuansa)** in Bogor, Indonesia. 🇮🇩
```

Semoga ini membantu library kamu terlihat lebih "internasional" tapi tetap ramah buat developer lokal! Ada lagi yang mau kamu tambahkan?