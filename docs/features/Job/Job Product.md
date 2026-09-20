# Job Product Feature

## Overview Fitur
Fitur **Job Product** mencatat produk yang diasosiasikan ke sebuah Job melalui tabel pivot `jobs_has_products`.

Struktur data aktif:
- `product_id` (relasi utama ke `products.id`)
- `quantity`
- `price`
- `note`

Struktur data legacy:
- `inventory_stock_id` masih disimpan sebagai backup historis, dengan comment:
  - `LEGACY BACKUP ONLY - SAFE TO DROP AFTER JOB PRODUCT MIGRATION VALIDATED`

Fitur ini berada pada tab **Product** di halaman edit Job. Cakupan utama:
- menampilkan daftar product pada Job,
- menambah product ke Job,
- mengubah product pada Job,
- menghapus product dari Job,
- menyiapkan item untuk **Export Invoice**.

Catatan bisnis:
- Job Product **tidak mengurangi stok fisik**.
- Pengurangan stok fisik tetap terjadi di modul **Job Statement**.
- Konteks **warehouse tidak dipakai** lagi di flow Job Product.

## Alur End-to-End (UI -> Route -> Controller -> DB -> UI)
### 1) Menampilkan tab Product
1. User membuka halaman edit Job dan memilih tab **Product**.
2. Request masuk ke `GET job-product/{job}` (`job_product.index`).
3. `JobProductController@index()` menyiapkan `job`, `mainCategories`, dan `bankAccounts`.
4. View tab Product merender tombol Add Product, modal Export Invoice, dan datatable.
5. `JobProductsDataTable` mengambil data dari join `jobs_has_products`, `products`, `product_types`, `specifications`, `packagings`, `jobs`.

### 2) Menambah Job Product
1. User klik **Add Product**.
2. Form menampilkan filter `Main Category`, `Supplier`, `Product`, `Quantity`, `Unit Price`, `Note`.
3. Supplier dimuat dari:
   - `GET /api/supplier/options` (`api.supplier.options`).
4. Product options dimuat dari:
   - `GET /api/job-product/product-options/{selected?}` (`api.job_product.product_options`),
   - filter: `main_category_id`, `supplier_id`, `selected`.
   - product dengan `qty = 0` tetap ditampilkan jika cocok dengan filter kategori dan supplier.
5. Detail stock display dimuat dari:
   - `GET /api/job-product/product-detail/{product}` (`api.job_product.product_detail`),
   - source stock: `products.qty` + `products.unit_id`.
6. Submit ke `POST job-product/{job}` (`job_product.store`):
   - validasi `product_id` unik per `job_id`,
   - simpan pivot (`product_id`, `quantity`, `price`, `note`),
   - simpan activity log.

### 3) Mengubah Job Product
1. User klik edit pada baris datatable.
2. Modal dimuat via `GET job-product/{job_id}/{id}/edit-modal` (`job_product.edit_modal`).
3. Data edit mengambil `jobs_has_products` + `products`.
4. Submit ke `PUT job-product/{job}/{job_product_id}` (`job_product.update`):
   - update pivot berdasarkan `jobs_has_products.id`,
   - field yang diupdate: `product_id`, `quantity`, `price`, `note`.

### 4) Menghapus Job Product
1. User klik delete.
2. Request ke `DELETE job-product/{job}/destroy/{id}` (`job_product.destroy`).
3. Controller delete row pivot berdasarkan `jobs_has_products.id`.
4. Activity log delete tetap dicatat.

### 5) Export Invoice
1. User buka modal Export Invoice dari tab Product.
2. Submit ke `POST /job/{job}/export-invoice` (`job.export_invoice`).
3. `JobInvoiceExport` mengambil item dari `jobs_has_products` + `products` (tanpa `inventory_stocks`).

## Daftar Route & Endpoint
### A. Route utama Job Product (`routes/web.php`)
| Method | URI | Route Name | Controller@Method | Fungsi |
| :--- | :--- | :--- | :--- | :--- |
| GET | `job-product/{job}` | `job_product.index` | `JobProductController@index` | Menampilkan tab Product |
| POST | `job-product/{job}` | `job_product.store` | `JobProductController@store` | Menyimpan Job Product baru |
| GET | `job-product/{job_id}/{id}/edit-modal` | `job_product.edit_modal` | `JobProductController@editModal` | Memuat modal edit |
| PUT | `job-product/{job}/{job_product_id}` | `job_product.update` | `JobProductController@update` | Update item pivot berdasarkan id |
| DELETE | `job-product/{job}/destroy/{id}` | `job_product.destroy` | `JobProductController@destroy` | Hapus item pivot berdasarkan id |

### B. Endpoint pendukung form Job Product
| Method | URI | Route Name | Controller@Method | Fungsi |
| :--- | :--- | :--- | :--- | :--- |
| GET | `api/supplier/options` | `api.supplier.options` | `SupplierController@getApiOptions` | Opsi supplier by filter main category |
| GET | `api/job-product/product-options/{selected?}` | `api.job_product.product_options` | `JobProductController@getProductOptions` | Opsi product untuk form Job Product |
| GET | `api/job-product/product-detail/{product}` | `api.job_product.product_detail` | `JobProductController@getProductDetail` | Detail qty dan unit product |

### C. Endpoint terkait layar Product tetapi bukan CRUD inti
| Method | URI | Route Name | Controller@Method | Fungsi |
| :--- | :--- | :--- | :--- | :--- |
| POST | `job/{job}/export-invoice` | `job.export_invoice` | `JobListController@exportInvoice` | Export invoice berbasis item Job Product |

## Struktur Tabel & Relasi
### 1) `jobs_has_products`
Pivot utama Job Product.

Kolom aktif:
- `id`
- `job_id`
- `product_id` (nullable saat fase transisi data orphan)
- `quantity`
- `price`
- `note`
- `created_at`, `updated_at`

Kolom backup legacy:
- `inventory_stock_id` (nullable, backup historis, tidak dipakai logic baru)

### 2) `products`
Sumber utama opsi product dan detail stock display di form Job Product.

Kolom yang dipakai:
- `id`
- `sku`
- `supplier_id`
- `main_category_id`
- `product_type_id`
- `specification_id`
- `packaging_id`
- `unit_id`
- `qty`

Catatan perilaku:
- Product tetap boleh dipilih di Job Product walaupun `qty = 0`.
- Nilai `qty` dipakai untuk informasi stock display saja, bukan sebagai filter kelayakan product di dropdown.

### 3) `jobs`
Konteks bisnis Job dan status lock action (`open/close`).

### 4) `product_types`, `specifications`, `packagings`
Dipakai untuk label tampilan product pada datatable dan invoice.

### 5) `bank_accounts`
Dipakai untuk pilihan rekening saat Export Invoice.

### 6) `activity_log`
Audit create/update/delete item Job Product.

### Relasi utama
- `jobs` 1..n `jobs_has_products`
- `products` 1..n `jobs_has_products`
- `products` n..1 `main_categories`
- `products` n..1 `suppliers`
- `products` n..1 `product_types`
- `products` n..1 `specifications`
- `products` n..1 `packagings`

## Perubahan Penting dari Versi Sebelumnya
- Source opsi product pindah dari `inventory_stocks` ke `products`.
- Payload form berubah dari `inventory_stock_id` ke `product_id`.
- Route update item berubah dari param `{stock_id}` ke `{job_product_id}`.
- Tampilan dan flow Job Product tidak lagi memakai warehouse.
- Query datatable dan export invoice tidak lagi join `inventory_stocks`.
- Opsi product Job Product sekarang tetap menampilkan product dengan stok `0` selama cocok dengan filter kategori dan supplier.

## Catatan Migrasi Data
Migrasi `2026_03_17_120000_migrate_job_products_to_product_id` melakukan:
- add `product_id`,
- backfill `product_id` dari `inventory_stock_id` bila mapping tersedia,
- mempertahankan row orphan (`product_id = null`) agar migrasi tidak gagal,
- logging warning jika ditemukan orphan,
- mempertahankan `inventory_stock_id` sebagai backup legacy.

Cleanup migration sudah disiapkan terpisah di `database/migrations_drafts`:
- `2026_03_17_130000_drop_legacy_inventory_stock_id_from_jobs_has_products.php`

File draft cleanup tidak dijalankan otomatis sebelum validasi produksi selesai.

## Peta File Terkait
### A. Backend inti
- `app/Http/Controllers/JobProductController.php`
- `app/DataTables/JobProductsDataTable.php`
- `app/Models/Job.php`
- `app/Models/Product.php`

### B. Backend pendukung
- `app/Http/Controllers/JobListController.php`
- `app/Http/Controllers/SupplierController.php`
- `app/Exports/JobInvoiceExport.php`
- `routes/web.php`
- `routes/api.php`
- `database/migrations/2026_03_17_120000_migrate_job_products_to_product_id.php`

### C. UI
- `resources/views/pages/job/edit/components/job-product-form-script.blade.php`
- `resources/views/pages/job/edit/components/modal-add-job-product-config.blade.php`
- `resources/views/pages/job/edit/components/modal-edit-job-product-config.blade.php`
- `resources/views/pages/job/js/createJobProduct.blade.php`
- `resources/views/pages/job/components/create/job-product-section.blade.php`

## Test Plan (Dokumentasi)
1. Verifikasi route `job_product.update` menggunakan `{job_product_id}`.
2. Verifikasi form add/edit mengirim `product_id`.
3. Verifikasi endpoint product options/detail Job Product berfungsi.
4. Verifikasi datatable Job Product tidak memiliki kolom warehouse.
5. Verifikasi invoice export tetap jalan dengan join `products`.
6. Verifikasi `inventory_stock_id` tetap ada sebagai backup dengan comment legacy.
7. Verifikasi row orphan (`product_id = null`) tidak memblokir migrasi.
