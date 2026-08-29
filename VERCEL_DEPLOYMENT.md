# 🚀 Panduan Deploy R4VEN Laravel ke Vercel + Full Supabase PostgreSQL

Proyek ini telah dikonfigurasi secara penuh untuk berjalan menggunakan **Full Supabase (PostgreSQL)** dan di-host di **Vercel Serverless**.

---

## 🐘 Langkah 1: Buat Database Gratis di Supabase

1. Buka [Supabase.com](https://supabase.com) dan login/daftar (Gratis).
2. Klik **"New Project"**.
3. Masukkan:
   - **Name**: `r4ven-db` (bebas)
   - **Database Password**: Buat password yang kuat dan **catat password ini**.
   - **Region**: Pilih yang terdekat (contoh: *Singapore / ap-southeast-1*).
4. Klik **"Create new project"** dan tunggu sekitar 1-2 menit hingga status database menjadi *Active*.

---

## 🔗 Langkah 2: Ambil Connection String Supabase

1. Di dashboard project Supabase Anda, buka menu **Project Settings** (ikon gerigi di kiri bawah) > **Database**.
2. Scroll ke bagian **Connection parameters** atau **Connection string**.
3. Pilih tab **URI**.
4. Pilih mode **Transaction** (Port `6543`) atau **Session** (Port `5432`) — *Mode Transaction Port 6543 sangat direkomendasikan untuk Vercel Serverless*:
   Contoh format URI:
   ```
   postgresql://postgres.xxxxxx:[YOUR-PASSWORD]@aws-0-ap-southeast-1.pooler.supabase.com:6543/postgres
   ```
5. Ganti `[YOUR-PASSWORD]` dengan password database yang Anda buat di Langkah 1.

---

## ⚡ Langkah 3: Konfigurasi di Vercel

1. Buka [Vercel Dashboard](https://vercel.com/dashboard) > **Add New... > Project** > Pilih repository **`r4ven`**.
2. **Root Directory**: Klik **Edit** dan pilih folder **`r4ven-laravel`** *(Wajib)*.
3. Pada bagian **Environment Variables**, tambahkan:

| Key | Value (Contoh) |
| :--- | :--- |
| `DATABASE_URL` | `postgresql://postgres.xxxxxx:PasswordAnda@aws-0-ap-southeast-1.pooler.supabase.com:6543/postgres` |
| `APP_KEY` | `base64:7KqW8GZ8Jk3V1N0rS5v9bT6xY4z2A1c3D5e7F9g0H2I=` |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://nama-project-anda.vercel.app` |
| `ADMIN_EMAIL` | `admin@r4ven.local` *(atau email Anda)* |
| `ADMIN_PASSWORD` | `PasswordAdminAnda123` *(buat password rahasia)* |

4. Klik tombol **Deploy**!

---

## ✨ Apa yang Terjadi Secara Otomatis?
- Laravel otomatis mendeteksi koneksi PostgreSQL Supabase melalui `DATABASE_URL`.
- Pada saat cold-boot pertama kali, Laravel menjalankan migrasi tabel (`users`, `settings`, `visitor_logs`, `visitor_snapshots`) dan seeder akun admin ke Supabase secara otomatis.
- Anda dapat langsung melihat data tabel, log target, dan foto yang masuk secara real-time di **Table Editor** Supabase maupun di **Dashboard Admin R4VEN**!

---

## 🎯 Akses URL

- **Visitor Landing**: `https://nama-project.vercel.app/`
- **Admin Login**: `https://nama-project.vercel.app/login`
- **Template Direct Links**:
  - `https://nama-project.vercel.app/p/diamond` (Free Fire Redeem)
  - `https://nama-project.vercel.app/p/penarikandana` (Konfirmasi Penarikan E-Wallet)
  - `https://nama-project.vercel.app/p/chat` (Bantuan PayKita)
  - `https://nama-project.vercel.app/p/chatme` (Chat Me)
