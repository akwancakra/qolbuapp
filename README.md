# QolbuApp 🌟

**QolbuApp** adalah aplikasi berbasis web yang dirancang untuk mempermudah pengelolaan pendapatan dan data anak yatim secara terpusat dan efisien. Dibuat menggunakan **Laravel** sebagai backend, aplikasi ini memberikan kemudahan dalam manajemen dan pelaporan secara real-time.

![QolbuApp Logo](https://www.google.com/url?sa=i&url=https%3A%2F%2Fwww.qolbuhasanah.org%2Ftentang-kami%2F&psig=AOvVaw2lgxYVVmmAHZe3uXwPHZGY&ust=1730814178677000&source=images&cd=vfe&opi=89978449&ved=0CBQQjRxqFwoTCLD5sLTnwokDFQAAAAAdAAAAABAE)

## 🌈 Fitur Utama

- **Manajemen Pendapatan**: Kelola dan catat semua pendapatan dengan mudah.
- **Data Anak Yatim**: Pantau data anak yatim dengan detail lengkap.
- **Laporan Terpusat**: Dapatkan laporan keuangan dan informasi anak yatim secara menyeluruh dan terstruktur.
- **Keamanan**: Aplikasi ini dilengkapi dengan otentikasi aman dan kontrol akses untuk menjaga data sensitif.

## 🚀 Instalasi dan Penggunaan

Berikut adalah langkah-langkah untuk menjalankan **QolbuApp** di lingkungan lokal Anda.

### Persyaratan

- **PHP** >= 8.0
- **Composer**
- **Node.js** & **NPM**
- **MySQL** atau database lain yang didukung oleh Laravel

### Langkah-langkah Instalasi

1. **Clone repositori ini**:
   ```bash
   git clone https://github.com/username/QolbuApp.git
   cd QolbuApp
   ```

2. **Instal dependensi** menggunakan Composer dan NPM:
   ```bash
   composer install
   npm install
   npm run dev
   ```

3. **Konfigurasi lingkungan**:
   Salin file `.env.example` menjadi `.env` dan perbarui pengaturan database Anda.
   ```bash
   cp .env.example .env
   ```

4. **Generate key aplikasi**:
   ```bash
   php artisan key:generate
   ```

5. **Migrasi dan Seed database**:
   Buat tabel dan isi data dasar di database dengan menjalankan:
   ```bash
   php artisan migrate --seed
   ```

6. **Jalankan server lokal**:
   ```bash
   php artisan serve
   ```

7. Akses aplikasi di [http://localhost:8000](http://localhost:8000).

---

## 🧪 Testing
Untuk menjalankan pengujian, gunakan perintah berikut:
```bash
php artisan test
```

## 🔧 Pengaturan Tambahan
- **Storage Link**: Pastikan untuk membuat symlink storage jika diperlukan.
  ```bash
  php artisan storage:link
  ```

- **Caching**: Anda dapat mengoptimalkan performa dengan menggunakan perintah berikut:
  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  ```

## 📜 Lisensi
Proyek ini dilisensikan di bawah lisensi MIT. Lihat [LICENSE](LICENSE) untuk informasi lebih lanjut.

---

Terima kasih telah menggunakan **QolbuApp**! Semoga aplikasi ini bermanfaat bagi Anda dan dapat membantu dalam mempermudah pengelolaan data anak yatim dan pendapatan.
