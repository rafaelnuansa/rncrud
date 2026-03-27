# RNCrud - Laravel CRUD Generator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rafaelnuansa/rncrud.svg?style=flat-square)](https://packagist.org/packages/rafaelnuansa/rncrud)
[![Total Downloads](https://img.shields.io/packagist/dt/rafaelnuansa/rncrud.svg?style=flat-square)](https://packagist.org/packages/rafaelnuansa/rncrud)

**RNCrud** adalah library Laravel yang dirancang untuk mempercepat proses development dengan mengotomatisasi pembuatan boilerplate CRUD. Hanya dengan satu perintah, Anda akan mendapatkan Model, Migration, Controller (Resource), dan Routing secara otomatis.

## ✨ Fitur
- 🚀 **One-Command Setup**: Generate semua file CRUD dalam hitungan detik.
- 📁 **Complete Boilerplate**: Membuat Model, Migration, dan Controller sekaligus.
- 🛣️ **Auto-Routing**: Menambahkan `Route::resource` secara otomatis ke `web.php`.
- 🛠️ **Customizable**: File stub dapat dipublikasikan dan dimodifikasi sesuai kebutuhan proyek Anda.

## 📦 Instalasi

Anda dapat menginstal package ini melalui composer:

```bash
composer require rafaelnuansa/rncrud