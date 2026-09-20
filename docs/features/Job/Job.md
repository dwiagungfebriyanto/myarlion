# Job Feature

## Overview Fitur
Fitur **Job** adalah entry point utama untuk mencatat transaksi job dari sisi penjualan. Modul ini mencakup:
- daftar Job,
- create Job dari tiga source: `Inquiry`, `Non Inquiry`, `Reguler`,
- edit data utama Job,
- import template dan import Excel Job,
- export invoice,
- navigasi ke tab turunan: **Team**, **Product**, **Income**, dan **Job Statement**.

Perilaku implementasi saat ini:
- create Job mewajibkan minimal satu `Job Product`,
- submit create menyimpan data Job dan `job_products` dalam satu transaksi,
- setelah Job berhasil dibuat, user diarahkan ke halaman **Product** milik Job tersebut,
- `jobs.id` diposisikan sebagai primary key auto-increment pada perubahan schema terbaru.

## Alur End-to-End
### 1) Menampilkan daftar Job
1. User membuka halaman `Job List`.
2. Request masuk ke `GET job/list` (`job.list.index`).
3. `JobListController@index()` menyiapkan filter `customers` dan `marketings`.
4. `JobsDataTable` mengambil data dari `jobs` yang di-join ke `customers` dan `users`.
5. View `resources/views/pages/job/index.blade.php` menampilkan:
   - tombol **Add New**,
   - tombol **Import Excel**,
   - tombol **Get Template**,
   - filter customer dan marketing,
   - datatable daftar Job.

### 2) Create Job
1. User membuka `GET job/list/create` (`job.list.create`).
2. `JobListController@create()` menyiapkan:
   - `job_id` suggestion,
   - master `customer`, `countries`, `currencies`, `channels`,
   - daftar `inquiries` yang belum punya `job_id`,
   - `mainCategories` untuk draft Job Product.
3. View `resources/views/pages/job/create.blade.php` merender tiga tab source:
   - `Inquiry`,
   - `Non Inquiry`,
   - `Reguler`.
4. Masing-masing tab memakai partial form sendiri dan memiliki section **Job Product** dengan draft item di browser.
5. Draft Job Product dikelola oleh `resources/views/pages/job/js/createJobProduct.blade.php`:
   - load supplier dari `api.supplier.options`,
   - load opsi stock dari `api.inventory_stock.ready_stock_options`,
   - load detail stok dari `job.fetchStock`,
   - simpan sementara item ke hidden JSON `job_products`.
6. Submit create ditangani `resources/views/pages/job/js/addJobStore.blade.php`.
7. Request dikirim ke `POST job/list` (`job.list.store`) dengan payload field Job + `job_products`.
8. `JobListController@store()`:
   - memvalidasi source dan field utama Job,
   - memvalidasi `job_products` minimal satu item,
   - membuat Job,
   - untuk source `inquiry`, memanggil `storeInquiryJob()` dan mengubah data inquiry terkait,
   - menyimpan pivot `jobs_has_products` via relasi `jobHasInventoryStock()`,
   - mencatat activity `job_product_created`,
   - mengembalikan JSON `job_id` dan `redirect_url`.
9. Frontend redirect ke `job_product.index` dari Job yang baru tersimpan.

### 3) Edit Job
1. User membuka `GET job/list/{job}/edit` (`job.list.edit`).
2. `JobListController@edit()` mengambil `job`, `channels`, `countries`, `currencies`, `customer`, dan `marketing`.
3. View `resources/views/pages/job/edit.blade.php` menjadi shell halaman edit dengan tab:
   - `Edit Job`,
   - `Team`,
   - `Product`,
   - `Income`,
   - `Job Statement`.
4. Tab `Edit Job` memakai partial `resources/views/pages/job/edit/tabs/edit-job.blade.php`.
5. Field `amount` di tab `Edit Job` hanya ditampilkan sebagai nilai referensi dan selalu `readonly`.
6. Tombol **Request Edit Amount** tetap menjadi entry point untuk mengajukan perubahan PI Amount jika user punya permission yang sesuai dan tidak ada request `waiting`.
7. `PUT job/list/{job}` (`job.list.update`) memperbarui data utama seperti customer, country, currency, channel, `est_profit`, dan period.
8. Jika request update biasa tetap mengirim payload `amount`, nilai tersebut diabaikan oleh backend.

### 4) Import Job
1. User upload file dari halaman list.
2. Request dikirim ke `POST /job/import` (`job.import`).
3. `JobListController@import()` menjalankan `JobImport`.
4. `JobImport` membaca Excel, memeriksa field wajib, lalu membuat Job baru jika `code` belum ada.

### 5) Export Template dan Export Invoice
1. User dapat mengambil template import dari `GET /job/import-template` (`job.import_template`).
2. User dapat export invoice dari tab Product melalui `POST /job/{job}/export-invoice` (`job.export_invoice`).
3. `JobListController@exportInvoice()` memvalidasi `bank_account_id` lalu memanggil `JobInvoiceExport`.
4. `JobInvoiceExport` mengambil data `jobs`, `jobs_has_products`, `inventory_stocks`, `products`, `product_types`, `specifications`, `units`, dan `bank_accounts`, lalu mengisi template Excel invoice.

## Daftar Route & Endpoint
### A. Route inti modul Job (`routes/web.php`)
| Method | URI | Route Name | Controller@Method | Fungsi |
| :--- | :--- | :--- | :--- | :--- |
| GET | `job/list` | `job.list.index` | `JobListController@index` | Menampilkan daftar Job |
| GET | `job/list/create` | `job.list.create` | `JobListController@create` | Menampilkan form create Job |
| POST | `job/list` | `job.list.store` | `JobListController@store` | Menyimpan Job baru beserta Job Product draft |
| GET | `job/list/{list}/edit` | `job.list.edit` | `JobListController@edit` | Menampilkan shell edit Job |
| PUT/PATCH | `job/list/{list}` | `job.list.update` | `JobListController@update` | Memperbarui data utama Job selain `amount`; payload `amount` diabaikan |
| DELETE | `job/list/{list}` | `job.list.destroy` | `JobListController@destroy` | Route resource tersedia, tetapi tidak menjadi alur utama yang dibahas dokumen ini |
| GET | `job/import-template` | `job.import_template` | `JobListController@importTemplate` | Mengunduh template import Job |
| POST | `job/import` | `job.import` | `JobListController@import` | Import data Job dari Excel |
| POST | `job/{job}/export-invoice` | `job.export_invoice` | `JobListController@exportInvoice` | Export invoice Job |
| GET | `api/fetchExpenseStock/{id}` | `job.fetchExpenseStock` | `JobListController@getTotalExpenseStock` | Mengambil ringkasan total expense/profit job |
| GET | `api/fetchStock/{id}` | `job.fetchStock` | `JobListController@fetchStock` | Mengambil detail stock yang dipilih |

### B. Route tab turunan dari halaman edit Job
| Method | URI/Prefix | Route Name | Controller | Fungsi |
| :--- | :--- | :--- | :--- | :--- |
| resource | `job.team` | `job.team.*` | `JobTeamController` | Mengelola tim marketing pada Job |
| GET/POST/... | `job-income` | `job_income.*` | `JobIncomeController` | Mengelola pemasukan Job |
| GET/POST/... | `job-product` | `job_product.*` | `JobProductController` | Mengelola product Job dan export invoice entry point |
| GET/POST/... | `job-statement` | `job_statement.*` | `JobStatementController` | Mengelola stok aktual, PO stock, dan commission Job |

### C. Endpoint API yang dipakai flow Job Product pada konteks Job
| Method | URI | Route Name | Controller@Method | Fungsi |
| :--- | :--- | :--- | :--- | :--- |
| GET | `api/supplier/options` | `api.supplier.options` | `SupplierController@getApiOptions` | Opsi supplier dengan filter opsional `main_category_id` dan `selected` |
| POST | `api/inventory-stock/ready-stock-options/{selected?}` | `api.inventory_stock.ready_stock_options` | `InventoryStockController@getReadyStockOptions` | Opsi inventory stock siap pakai |

## Struktur Tabel & Relasi
### 1) `jobs`
Tabel utama bisnis untuk modul Job.

Kolom yang dipakai modul:
- `id`: primary key Job, dipakai di seluruh route turunan dan pivot relasi.
- `code`: Job ID bisnis yang ditampilkan di list dan invoice.
- `source`: sumber create Job, bernilai `inquiry`, `non_inquiry`, atau `reguler`.
- `customer_id`: relasi ke `customers.id`.
- `currency_id`: relasi ke `currencies.id`.
- `country_id`: relasi ke `countries.id`.
- `employee_id`: relasi ke user marketing pembuat/owner Job.
- `channel_id`: relasi ke `channels.id`.
- `amount`: total amount / PI Amount Job. Pada halaman edit Job nilainya hanya tampil sebagai referensi dan perubahan nilainya harus melalui flow approval terpisah.
- `est_profit`: estimasi profit.
- `total_expenses`, `gross_profit`, `net_profit`: dipakai pada kalkulasi turunan profit/expense.
- `period_job`: periode Job dan bahan nomor invoice.
- `status_payment`: status transaksi income.
- `status`: status open/closed untuk lock aksi.
- `created_at`, `updated_at`.

Relasi inti:
- `jobs.customer_id -> customers.id`
- `jobs.country_id -> countries.id`
- `jobs.currency_id -> currencies.id`
- `jobs.channel_id -> channels.id`
- `jobs.employee_id -> users.id`
- relasi ke `inquiries` terjadi melalui `inquiries.job_id`

### 2) `customers`
Dipakai pada list, create, edit, dan invoice.

Kolom yang dipakai:
- `id`
- `code`
- `name`
- `email`, `telp`, `country_id` dapat terisi saat source Job berasal dari inquiry customer baru.

### 3) `countries`
Master negara tujuan/customer.

Kolom yang dipakai:
- `id`
- `country_code`
- `country_name`

### 4) `currencies`
Master mata uang Job dan income terkait.

Kolom yang dipakai:
- `id`
- `currency_code`
- `currency_name`

### 5) `channels`
Master channel untuk create/edit Job non inquiry dan reguler.

Kolom yang dipakai:
- `id`
- `channel_name`

### 6) `inquiries`
Dipakai ketika source Job adalah `inquiry`.

Kolom yang dipakai:
- `id`
- `date`
- `name`
- `country_code`
- `channel_id`
- `phone`
- `email`
- `note`
- `status`
- `job_id`
- `customer_category`
- `destination_id`

Catatan implementasi:
- create Job dari inquiry hanya mengambil inquiry yang `job_id` masih `null`,
- saat Job berhasil dibuat, inquiry akan diupdate ke status `sales` dan dihubungkan ke `job_id`,
- jika inquiry berasal dari `new_customer`, data customer baru dapat dibuat dari data inquiry.

### 7) `jobs_has_products`
Pivot antara Job dan inventory stock. Pada create Job, tabel ini sudah diisi sejak halaman create.

Kolom yang dipakai:
- `id`
- `job_id`: relasi ke `jobs.id`
- `inventory_stock_id`: relasi ke `inventory_stocks.id`
- `quantity`
- `price`
- `note`
- `created_at`, `updated_at`

Relasi inti:
- `jobs_has_products.job_id -> jobs.id`
- `jobs_has_products.inventory_stock_id -> inventory_stocks.id`

### 8) `inventory_stocks`
Sumber stok yang dipilih untuk Job Product dan Job Statement.

Kolom yang dipakai:
- `id`
- `inventory_id`
- `inventory_type`
- `product_id`
- `warehouse_id`
- `unit_id`
- `amount`
- `weight`
- `purchase_cost`
- `purchase_cost_per_unit`

### 9) `products`
Master identitas product yang terhubung ke inventory stock.

Kolom yang dipakai:
- `sku`
- `main_category_id`
- `product_type_id`
- `packaging_id`
- `specification_id`
- `supplier_id`
- `unit_id`

Peran pada modul Job:
- membentuk label product pada Job Product dan invoice,
- menjadi target filter main category dan supplier,
- menjadi jembatan dari inventory stock ke atribut product.

### 10) `job_teams`
Dipakai oleh tab Team.

Kolom yang dipakai:
- `id`
- `job_id`
- `user_id`
- `job_percentage`

### 11) `job_incomes`
Dipakai oleh tab Income dan outstanding.

Kolom yang dipakai:
- `id`
- `job_id`
- `date`
- `payment`
- `outstanding`
- `to_idr`
- `nominal`
- `bank_account_id`
- `currency_id`

Catatan:
- sebagian kolom seperti `nominal`, `bank_account_id`, dan `currency_id` ditambahkan oleh migration lanjutan dan dipakai pada flow income saat ini.

### 12) `job_statements`
Dipakai oleh tab Job Statement untuk stok aktual yang benar-benar keluar.

Kolom yang dipakai:
- `id`
- `job_id`
- `inventory_stock_id`
- `quantity`
- `price_per_unit`
- `total`
- `is_sample`

### 13) `bank_accounts`
Dipakai oleh export invoice dan transaksi income.

Kolom yang dipakai:
- `id`
- `bank_id`
- `currency_code`
- `account_number`
- `account_name`

### 14) `activity_log`
Audit trail untuk perubahan Job dan fitur turunannya.

Kolom yang relevan:
- `id`
- `log_name`
- `description`
- `subject_type`, `subject_id`
- `causer_type`, `causer_id`
- `properties`
- `event`
- `created_at`

Catatan:
- create Job Product pada halaman create mencatat event `job_product_created`,
- model `Job` sendiri memakai trait logging sehingga perubahan fillable Job juga tercatat.

## Peta File yang Digunakan
### 1) Backend inti
- `app/Http/Controllers/JobListController.php`
  - controller utama modul Job: list, create, store, edit, update, import, export invoice, fetch stock, dan kalkulasi total expense stock.
- `app/DataTables/JobsDataTable.php`
  - datatable untuk halaman list Job.
- `app/Models/Job.php`
  - model utama Job beserta relasi ke customer, currency, country, marketing, income, team, product, dan statement.

### 2) Backend create/import/export
- `app/Imports/JobImport.php`
  - import Job dari file Excel.
- `app/Exports/JobInvoiceExport.php`
  - generator file invoice Excel berbasis Job dan Job Product.
- `app/Http/Controllers/SupplierController.php`
  - menyediakan endpoint API supplier options untuk filter Job Product.
- `app/Http/Controllers/InventoryStockController.php`
  - menyediakan endpoint inventory stock siap pakai untuk Job Product.

### 3) UI utama modul Job
- `resources/views/pages/job/index.blade.php`
  - halaman list Job.
- `resources/views/pages/job/create.blade.php`
  - shell halaman create Job dengan tiga tab source.
- `resources/views/pages/job/edit.blade.php`
  - shell halaman edit Job dan tab turunan.
- `resources/views/pages/job/components/edit/navs.blade.php`
  - navigasi tab `Edit Job`, `Team`, `Product`, `Income`, `Job Statement`.

### 4) Partial create Job
- `resources/views/pages/job/components/create/create-content-inquiry.blade.php`
  - form create Job source inquiry.
- `resources/views/pages/job/components/create/create-content-nonInquiry.blade.php`
  - form create Job source non inquiry.
- `resources/views/pages/job/components/create/create-content-reguler.blade.php`
  - form create Job source reguler.
- `resources/views/pages/job/components/create/job-product-section.blade.php`
  - section preview draft Job Product pada masing-masing tab create.
- `resources/views/pages/job/components/create/job-product-modal.blade.php`
  - modal add/edit draft Job Product pada halaman create.
- `resources/views/pages/job/js/createJobProduct.blade.php`
  - script client-side untuk add/edit/delete draft Job Product.
- `resources/views/pages/job/js/addJobStore.blade.php`
  - script submit create Job via AJAX beserta payload `job_products`.

### 5) Partial edit dan fitur turunan
- `resources/views/pages/job/edit/tabs/edit-job.blade.php`
  - form update data utama Job.
- `resources/views/pages/job/edit/tabs/product.blade.php`
  - tab Product milik Job.
- `resources/views/pages/job/edit/tabs/team.blade.php`
  - tab Team.
- `resources/views/pages/job/edit/tabs/income.blade.php`
  - tab Income.
- `app/Http/Controllers/JobProductController.php`
  - controller tab Product.
- `app/Http/Controllers/JobTeamController.php`
  - controller tab Team.
- `app/Http/Controllers/JobIncomeController.php`
  - controller tab Income.
- `app/Http/Controllers/JobStatementController.php`
  - controller tab Job Statement.

## Relasi ke Fitur Lain
- **Job Product**
  - sekarang menjadi syarat create Job.
  - setelah Job tersimpan, user diarahkan ke `job_product.index`.
  - export invoice membaca item dari Job Product.

- **Team**
  - dikelola dari tab terpisah di halaman edit Job.
  - memakai relasi `job_teams` dan user marketing.

- **Income**
  - dikelola dari tab Job Income.
  - mempengaruhi outstanding dan profit turunan Job.

- **Job Statement**
  - memakai `inventory_stocks` untuk pengeluaran stok aktual.
  - pengurangan stok fisik terjadi di sini, bukan di Job Product.

- **Inventory Stock**
  - menjadi sumber opsi stock untuk Job Product dan Job Statement.
  - detail stok diambil melalui endpoint shared yang juga dipakai flow create Job.

- **Inquiry**
  - salah satu source create Job.
  - saat Job dibuat dari inquiry, inquiry akan dihubungkan ke Job dan statusnya berubah ke `sales`.

- **Export Invoice**
  - dipicu dari tab Product, tetapi secara arsitektur merupakan capability milik modul Job.
  - memakai data `jobs`, `jobs_has_products`, dan `bank_accounts`.

- **Profit / Outstanding**
  - tersedia sebagai resource route `job/profit` dan `job/outstanding`.
  - modul ini membaca data Job dan transaksi turunannya, bukan entry point create/edit Job.

## Aturan Bisnis & Batasan Perilaku
- Source create Job yang tersedia hanya `Inquiry`, `Non Inquiry`, dan `Reguler`.
- Create Job tidak valid jika `job_products` kosong.
- Satu request create menyimpan Job dan Job Product dalam satu transaksi database.
- `job_products.*.inventory_stock_id` harus distinct dalam satu request create.
- Untuk source `inquiry`, field `inquiry` wajib ada dan inquiry harus valid.
- Untuk source `non_inquiry` dan `reguler`, field `customer`, `channel`, dan `country` wajib ada.
- Setelah create berhasil, redirect menuju halaman Product dari Job yang baru tersimpan.
- Edit data utama Job dilakukan dari tab `Edit Job`, sedangkan data turunan dipisah ke tab masing-masing.
- `amount` tidak dapat diubah langsung dari tab `Edit Job`; perubahan nilai hanya bisa diajukan lewat **Request Edit Amount** dan diterapkan setelah approval.
- Endpoint `job.list.update` tidak lagi menjadi jalur perubahan `jobs.amount`.
- Banyak aksi turunan dibatasi oleh status Job `open`, termasuk pada Team, Product, dan Income.
- Export invoice bergantung pada data Job Product yang sudah tersimpan.
- Dokumentasi ini merekam perilaku implementasi saat ini, termasuk perubahan terbaru bahwa `jobs.id` diposisikan sebagai auto-increment.
