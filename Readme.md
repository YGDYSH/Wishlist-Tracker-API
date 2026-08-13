# Wishlist Tracker API

RESTful API untuk mengelola wishlist / daftar keinginan tabungan.
Dibangun dengan PHP native + PDO, tanpa framework.

## Persyaratan

- PHP >= 7.4
- MySQL / MariaDB
- Composer *(opsional, tidak wajib)*

## Instalasi

```bash
git clone <repo-url> wishlist_api
cd wishlist_api
cp .env.example .env   # atau buat .env manual
```

Import database schema:

```sql
CREATE DATABASE wishlist_tracker;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE wishlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    target_price DECIMAL(10,2) NOT NULL,
    saved_amount DECIMAL(10,2) DEFAULT 0,
    category VARCHAR(50),
    target_date DATE,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## Konfigurasi

File `.env`:

```
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=wishlist_tracker
DB_USER=root
DB_PASS=
```

## Base URL

```
http://localhost/wishlist_api
```

---

## Auth

### Register

```
POST /auth/register.php
```

**Body (JSON)**

| Field | Type | Required | Keterangan |
|-------|------|----------|------------|
| name | string | ya | Nama lengkap |
| email | string | ya | Email valid |
| password | string | ya | Min. 6 karakter |

**Response `201`**
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": { "id": 1, "name": "...", "email": "..." }
  }
}
```

### Login

```
POST /auth/login.php
```

**Body (JSON)**

| Field | Type | Required |
|-------|------|----------|
| email | string | ya |
| password | string | ya |

**Response `200`**
```json
{
  "success": true,
  "message": "Login successful",
  "data": { "user": { "id": 1, "name": "...", "email": "..." } }
}
```

> ⚠️ API ini belum memakai token. `user_id` harus dikirim manual pada setiap request wishlist.

---

## Wishlist

> Semua request wishlist membutuhkan field `user_id`.

### Create Wishlist

```
POST /wishlist/create.php
```

**Body (JSON)**

| Field | Type | Required | Keterangan |
|-------|------|----------|------------|
| user_id | int | ya | ID user |
| name | string | ya | Nama wishlist |
| target_price | float | ya | > 0 |
| saved_amount | float | ya | >= 0, <= target_price |
| category | string | tidak | |
| target_date | string | tidak | Format `YYYY-MM-DD` |
| notes | string | tidak | |

**Response `201`**
```json
{
  "success": true,
  "message": "Wishlist created successfully",
  "data": { "id": 1, "user_id": 1, "name": "...", ... }
}
```

### Get Wishlist by User

```
GET /wishlist/index.php?user_id=1
```

**Response `200`**
```json
{
  "success": true,
  "message": "Wishlist retrieved successfully",
  "data": [
    { "id": 1, "user_id": 1, "name": "...", "target_price": 100000, ... }
  ]
}
```

### Update Wishlist

```
POST /wishlist/update.php
```

**Body (JSON)** — sama seperti create, ditambah:

| Field | Type | Required |
|-------|------|----------|
| id | int | ya |

**Response `200`**
```json
{
  "success": true,
  "message": "Wishlist updated successfully",
  "data": { "id": 1, "user_id": 1, ... }
}
```

### Delete Wishlist

```
POST /wishlist/delete.php
```

**Body (JSON)**

| Field | Type | Required |
|-------|------|----------|
| id | int | ya |
| user_id | int | ya |

**Response `200`**
```json
{
  "success": true,
  "message": "Wishlist deleted successfully",
  "data": { "id": 1 }
}
```

---

## Error Response Format

```json
{
  "success": false,
  "message": "Deskripsi error"
}
```

### HTTP Status Codes

| Code | Arti |
|------|------|
| 200 | Berhasil |
| 201 | Berhasil dibuat |
| 400 | Validasi gagal / input tidak valid |
| 401 | Auth gagal |
| 404 | Resource tidak ditemukan |
| 405 | Method tidak diizinkan |
| 500 | Server error |

---

## Fitur

- ✅ Auth (register, login)
- ✅ Wishlist CRUD (create, read, update, delete)
- ✅ Validasi input
- ✅ CORS support (untuk Flutter dev)
- ✅ `.env` config support

---

## 