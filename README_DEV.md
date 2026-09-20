# 🐳 Dokumentasi Konfigurasi Docker — Proyek myarlion (Laravel 10 + PHP 8.1)

Dokumentasi ini menjelaskan **cara kerja dan konfigurasi Docker Compose** untuk lingkungan **development Laravel 10**, beserta langkah-langkah yang **aman dari error permission dan dependency**, terutama saat berpindah komputer atau OS.

---

## 🧱 Arsitektur Singkat

Docker environment ini terdiri dari 3 service utama:

| Service | Container | Fungsi Utama |
|----------|------------|--------------|
| `app` | `myarlion_app` | PHP-FPM 8.1 untuk menjalankan Laravel |
| `nginx` | `myarlion_nginx` | Web server (port `8080`) |
| `node` | `myarlion_node` | Build asset dengan NPM/Vite |

Semua service saling terhubung melalui network `myarlion_net`.

---

## 📂 Struktur File Konfigurasi

```

docker-compose.yml
docker/
├── php/
│    ├── Dockerfile
│    └── php.ini
└── nginx/
└── default.conf

````

---

## ⚙️ 1. File `docker-compose.yml`

```yaml
name: myarlion

services:
  app:
    build:
      context: .
      dockerfile: docker/php/Dockerfile
      args:
        UID: ${HOST_ls -lUID:-1000}
        GID: ${HOST_ls -lGID:-1000}
    container_name: myarlion_app
    working_dir: /var/www
    user: "${HOST_UID:-1000}:${HOST_GID:-1000}"
    volumes:
      - ./:/var/www
      - ./docker/php/php.ini:/usr/local/etc/php/conf.d/myarlion.ini:ro
    networks:
      - myarlion_net

  nginx:
    image: nginx:alpine
    container_name: myarlion_nginx
    depends_on:
      - app
    ports:
      - "8080:80"     # Akses app di http://localhost:8080
    volumes:
      - ./:/var/www
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf:ro
    networks:
      - myarlion_net

  node:
    image: node:18-alpine
    container_name: myarlion_node
    working_dir: /var/www
    user: "${HOST_UID:-1000}:${HOST_GID:-1000}"
    volumes:
      - ./:/var/www
    # Biarkan hidup untuk siap pakai npm run dev
    command: sh -c "tail -f /dev/null"
    # (Opsional untuk Vite HMR)
    # ports:
    #   - "5173:5173"
    networks:
      - myarlion_net

networks:
  myarlion_net:
    driver: bridge
````

### Penjelasan penting:

| Bagian                            | Fungsi                                                                    |
| --------------------------------- | ------------------------------------------------------------------------- |
| `user: "${HOST_UID}:${HOST_GID}"` | Menyamakan ID user di container dengan host (hindari *permission denied*) |
| `volumes: ./:/var/www`            | Hot-reload: file di host otomatis sinkron dengan container                |
| `command: tail -f /dev/null`      | Menjaga container `node` tetap hidup untuk perintah manual (npm, vite)    |
| `args UID/GID`                    | Dikirim ke Dockerfile agar user di dalam image juga match dengan host     |

---

## 🐘 2. File `docker/php/Dockerfile`

```dockerfile
FROM php:8.1-fpm-alpine

# Dependencies minimal
RUN apk add --no-cache \
    git curl unzip libzip-dev icu-dev oniguruma-dev \
    bash shadow libpng-dev libjpeg-turbo-dev freetype-dev

# Install extension PHP termasuk GD
RUN docker-php-ext-configure gd \
    --with-jpeg=/usr/include/ \
    --with-freetype=/usr/include/
RUN docker-php-ext-install \
    pdo pdo_mysql mbstring intl zip bcmath gd

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Buat user sesuai host UID/GID (untuk permission)
ARG UID=1000
ARG GID=1000
RUN groupmod -g ${GID} www-data && usermod -u ${UID} -g ${GID} www-data

WORKDIR /var/www
USER www-data
```

### Catatan:

* **Image dasar:** `php:8.1-fpm-alpine` → ringan dan cepat dibuild.
* **Ekstensi penting:** `pdo_mysql`, `mbstring`, `intl`, `zip`, `bcmath`, `gd` (wajib untuk `PhpSpreadsheet`).
* **Composer:** diambil dari image resmi agar ringan.
* **User match host:** mencegah error seperti:

  ```
  file_put_contents(...): Permission denied
  ```

---

## 🧩 3. File `docker/php/php.ini`

```ini
memory_limit=512M
upload_max_filesize=64M
post_max_size=64M
max_execution_time=120
; Dev helpers
error_reporting=E_ALL
display_errors=On
display_startup_errors=On
```

> Setting dev-friendly agar error Laravel mudah dilacak.

---

## 🌐 4. File `docker/nginx/default.conf`

```nginx
server {
    listen 80;
    server_name localhost;
    root /var/www/public;

    index index.php index.html;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass myarlion_app:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_read_timeout 300;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|svg|webp|ico)$ {
        try_files $uri =404;
        expires 7d;
        access_log off;
    }
}
```

---

## 💡 Cara Menjalankan (Langkah Aman)

### 🧭 Langkah 1: Siapkan variabel user

Di terminal host:

```bash
export HOST_UID=$(id -u)
export HOST_GID=$(id -g)
```

> **Kenapa?**
> Agar file yang dibuat container dimiliki oleh user host, bukan root.

### 🧭 Langkah 2: Build & Run

```bash
docker compose up -d --build
```

### 🧭 Langkah 3: Jalankan perintah Laravel di container

```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
```

### 🧭 Langkah 4: Jalankan NPM di container Node

```bash
docker compose exec node npm install
docker compose exec node npm run build
```

### 🧭 Langkah 5: Akses web

```
http://localhost:8080
```

---

## 🧰 Command Penting (Ringkasan)

| Tujuan                   | Perintah                                                                                |
| ------------------------ | --------------------------------------------------------------------------------------- |
| Jalankan semua container | `docker compose up -d`                                                                  |
| Hentikan container       | `docker compose down`                                                                   |
| Jalankan Artisan         | `docker compose exec app php artisan migrate`                                           |
| Jalankan Composer        | `docker compose exec app composer update`                                               |
| Jalankan NPM             | `docker compose exec node npm run dev`                                                  |
| Reset permission         | `docker compose exec -u root app chown -R ${HOST_UID:-1000}:${HOST_GID:-1000} /var/www` |

---

Catatan database:

* Repo ini memakai baseline schema di `database/schema/mysql-schema.sql`
* Environment baru di-bootstrap dari import database terbaru, bukan dari replay seluruh migration historis
* Jalankan `php artisan migrate` hanya setelah import database untuk mengeksekusi migration baru pasca-baseline
* Simpan migration draft di `database/migrations_drafts` sampai siap dipindahkan ke `database/migrations`

---

## 🧩 Error Umum & Solusinya

| Error                                                              | Penyebab                             | Solusi                                                                                |
| ------------------------------------------------------------------ | ------------------------------------ | ------------------------------------------------------------------------------------- |
| `Permission denied` pada `vendor/` atau `storage/`                 | File dibuat oleh user berbeda        | Jalankan: `docker compose exec -u root app chown -R ${HOST_UID}:${HOST_GID} /var/www` |
| `ext-gd missing`                                                   | Ekstensi GD belum diinstall          | Sudah disertakan di Dockerfile (`gd`)                                                 |
| URL Laravel tanpa port ([http://localhost](http://localhost) saja) | `.env` belum diset                   | Pastikan `.env`: `APP_URL=http://localhost:8080`                                      |
| File tidak berubah di browser                                      | Browser cache / volume tidak sinkron | `docker compose restart nginx`                                                        |
| `bash: UID: readonly variable` saat export                         | Variabel `UID` bawaan shell          | Gunakan `HOST_UID` & `HOST_GID` (bukan `UID/GID`)                                     |

---

## 💾 Pindah Komputer / Developer Baru

Agar tidak bingung dan tidak error:

1. **Clone repo**

   ```bash
   git clone <repo-url> myarlion
   cd myarlion
   ```

2. **Pastikan Docker terinstal** (Engine + Compose)

3. **Export UID/GID**

   ```bash
   export HOST_UID=$(id -u)
   export HOST_GID=$(id -g)
   ```

4. **Build ulang**

   ```bash
   docker compose up -d --build
   ```

5. **Jalankan Composer & Artisan**

   ```bash
   docker compose exec app composer install
   docker compose exec app php artisan key:generate
   ```

6. **Cek file permission**

   ```bash
   docker compose exec -u root app chown -R ${HOST_UID}:${HOST_GID} /var/www
   ```

7. **Jalankan NPM**

   ```bash
   docker compose exec node npm install
   docker compose exec node npm run dev
   ```

8. Buka browser → [http://localhost:8080](http://localhost:8080)

---

## ✅ Keunggulan Konfigurasi Ini

* 🔁 **Auto-sync** antara host dan container (tanpa perlu rebuild tiap ubah file)
* 🧍 **User ID selaras dengan host** → bebas dari permission error
* 🪶 **Image ringan** (base `alpine`)
* 🧰 **Composer & npm di container** → host bersih tanpa install PHP/Node
* 🧩 **Siap dipakai untuk CI/CD** (tinggal tambahkan service DB seperti MySQL/Redis jika diperlukan)

---

## 📘 Kesimpulan

Dengan konfigurasi ini:

* Semua pengembang dapat menjalankan environment yang identik.
* Tidak ada dependency atau versi PHP/Node yang bentrok antar komputer.
* Permission tetap aman dan mudah di-reset kapan saja.

> 💡 **Tips:**
> Tambahkan alias ke `.bashrc` / `.zshrc`:
>
> ```bash
> alias dce='docker compose exec app'
> alias dcn='docker compose exec node'
> ```
>
> supaya bisa cepat jalankan `dce php artisan migrate` atau `dcn npm run dev`.

---
