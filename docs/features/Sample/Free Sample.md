# Fitur Free Sample

## Ringkasan
Fitur **Free Sample - non Job** dipakai untuk mencatat pengeluaran sample gratis ke customer tanpa membuat job. Controller utamanya adalah `FreeSampleController`.

Fitur ini berfokus pada:
- menampilkan daftar free sample yang sudah tercatat,
- memfilter data berdasarkan warehouse,
- menambah transaksi free sample baru dari inventory yang tersedia,
- mengurangi saldo inventory sumber setelah transaksi disimpan.

Modul ini mendukung dua mode recipient:
- `by inquiry`: free sample dikaitkan ke inquiry non-job, termasuk inquiry customer baru yang belum punya `customer_id`
- `by customer`: free sample dikaitkan langsung ke customer tanpa inquiry

## Entry Point dan Route
Resource route aktif:
- `free_sample.index`
- `free_sample.store`

Resource URL:
- `free-samples`

Entry point UI utama:
- sidebar `Sample > Free Sample - non Job`
- halaman list: `pages.free_sample.index`
- modal tambah: `pages.free_sample.components.modal_add`

Endpoint pendukung:
- `api.inventory_stock.ready_stock_options`

## Data dan Relasi Utama
Field utama yang dipakai saat create:
- `inventory_stock_id`
- `recipient_type`
- `inquiry`
- `customer`
- `quantity`
- `date`

Field yang disimpan pada model `FreeSample`:
- `inventory_stock_id`
- `customer_id`
- `inquiry_id`
- `quantity`
- `purchase_cost`
- `purchase_cost_per_unit`
- `date`

Relasi yang dipakai oleh halaman list dan proses simpan:
- `customer`
- `inquiry`
- `inventoryStock`
- `inventoryStock.product`
- `inventoryStock.unit`
- `inventoryStock.warehouse`

Catatan model:
- model memakai global scope `OfferedSampleScope`
- accessor `date` memformat nilai ke `Y F d`
- accessor `created_at` dan `updated_at` ditampilkan dalam format relative time

## Alur Halaman
### List Free Sample
Halaman `free_sample.index` memuat:
- daftar inquiry non-job
- daftar supplier
- daftar warehouse
- daftar free sample yang inventory source-nya masih lolos filter warehouse

Filter aktif:
- `warehouse`

Perilaku filter:
- jika `warehouse` kosong atau `all`, semua free sample ditampilkan
- jika warehouse dipilih, query dibatasi ke relasi `inventoryStock.warehouse_id`

Kolom tabel yang tampil:
- `#`
- `Delivered Date`
- `Inquiry Date`
- `Customer`
- `Product`
- `Quantity`
- `Warehouse`
- `Source Inventory`
- `PO Number`
- `Created at`
- `Updated at`

Perilaku data tabel:
- `Delivered Date` memakai nilai raw `date` lalu diformat `d/m/Y`
- `Inquiry Date` diambil dari tanggal inquiry, atau `-` jika kosong
- `Product` memakai `skuFormat()` dari product pada inventory source
- `Quantity` memakai `unitFormatted()`
- `Source Inventory` menampilkan bucket source via `getStockBucket()`
- `PO Number` menampilkan link ke `po_stock.detail` jika lineage PO tersedia

### Add Free Sample
Tombol `+ Add New` hanya tampil untuk user dengan permission `add free sample`.

Field form modal:
- `recipient_type`
- `warehouse`
- `supplier`
- `inventory_stock_id`
- `quantity`
- `inquiry` untuk mode `by inquiry`
- `customer` readonly autofill untuk mode `by inquiry`
- `customer` select untuk mode `by customer`
- `date`

Perilaku form:
- pilihan `recipient_type` menentukan field recipient yang aktif
- saat `recipient_type = inquiry`, user memilih inquiry lalu recipient preview mengikuti customer jika ada, atau nama inquiry jika belum ada customer
- saat `recipient_type = customer`, user memilih customer langsung dan field inquiry dinonaktifkan
- saat user mengganti `recipient_type`, field recipient yang tidak aktif akan di-reset
- jika submit gagal, modal otomatis terbuka lagi dan input sebelumnya dipertahankan
- perubahan `warehouse` atau `supplier` memicu AJAX `POST` ke `api.inventory_stock.ready_stock_options`
- request opsi inventory mengirim:
  - `warehouse_id`
  - `supplier_id`
  - `show_bucket=1`
- dropdown inventory menampilkan label bucket jika source berasal dari `stock` atau `sample`
- saat inventory dipilih, form mengisi:
  - unit ke elemen `#sampleUnit`
  - stok tersedia ke hidden field `#sampleStock`

Validasi form di UI:
- quantity dibandingkan dengan `sampleStock`
- submit diblok jika quantity lebih besar dari stok tersedia
- validation error backend dan runtime error ditampilkan di dalam modal, bukan hanya lewat toast global

## Validasi dan Simpan Data
Request class yang dipakai:
- `StoreFreeSampleRequest`

Validasi backend:
- `inventory_stock_id` wajib dan harus ada di `inventory_stocks`
- `recipient_type` wajib dan hanya boleh bernilai `inquiry` atau `customer`
- `inquiry` wajib jika `recipient_type = inquiry` dan harus ada di `inquiries` dengan syarat:
  - `job_id` null
- `customer` wajib jika `recipient_type = customer` dan harus ada di `customers`
- `quantity` wajib numeric dan `> 0`
- `quantity` tidak boleh lebih besar dari `unitValue()` inventory source
- `date` wajib dan valid sebagai tanggal

Otorisasi request:
- user harus punya permission `add free sample`

Pesan validasi dibuat eksplisit untuk:
- inquiry wajib untuk mode `by inquiry`
- customer wajib untuk mode `by customer`
- inquiry yang sudah punya job tidak bisa dipakai
- quantity melebihi stok tersedia

Alur simpan pada `FreeSampleController@store`:
- validasi request dijalankan terlebih dahulu
- transaksi database dibungkus `DB::transaction(...)`
- inventory source di-lock dengan `InventoryStockBalanceService::lockStock(...)`
- jika `recipient_type = inquiry`, inquiry di-lock dengan `lockForUpdate()` lalu diambil ulang dengan syarat non-job
- `customer_id` diisi dari `inquiry.customer_id` dan boleh `null` untuk inquiry customer baru
- jika `recipient_type = customer`, customer diambil langsung dari input dan `inquiry_id` diset `null`
- `purchase_cost_per_unit` diambil dari raw `purchase_cost_per_unit` inventory source
- `purchase_cost` dihitung dari `unit_cost * quantity`
- record `FreeSample` baru disimpan dengan `inventory_stock_id`, `inquiry_id`, `customer_id`, `quantity`, `purchase_cost`, `purchase_cost_per_unit`, dan `date`
- saldo inventory dikurangi lewat `InventoryStockBalanceService::subtractFromStock(...)`
- `updateProductStock($inventoryStock->product, $inventoryStock)` dipanggil setelah saldo source diperbarui
- setelah sukses, controller `redirect()->back()` dengan flash message `Free Sample stored successfully.`
- jika terjadi error database atau runtime:
  - controller `redirect()->back()->withInput()`
  - halaman menampilkan toast `error`
  - modal menampilkan alert yang menjelaskan alasan kegagalan

### Kemungkinan Gagal Simpan
Contoh kegagalan yang sekarang ditangani dengan pesan jelas:
- inquiry atau customer belum dipilih sesuai `recipient_type`
- inquiry sudah terhubung ke job
- quantity lebih besar dari stok tersedia
- database nyata belum menjalankan migration yang membuat `free_samples.customer_id` nullable, sehingga inquiry customer baru tidak bisa disimpan
- error runtime tak terduga saat proses lock stock, save, atau update saldo

## Catatan Penting
- Route yang aktif hanya `index` dan `store`
- Modul ini khusus untuk **Free Sample - non Job**
- mode recipient tidak disimpan sebagai kolom terpisah; data dianggap `by inquiry` jika `inquiry_id` terisi, dan `by customer` jika `inquiry_id` null
- `free_samples.customer_id` dapat bernilai `null` untuk mode inquiry yang berasal dari customer baru
- Sumber inventory bisa berasal dari bucket `stock` atau `sample`, karena opsi inventory memakai `getReadyStock()` dengan sample tetap ikut disertakan
- Label source inventory di halaman list berasal dari `InventoryStock::getStockBucket()`
- Dokumen ini mengikuti implementasi aktif pada `FreeSampleController`, `StoreFreeSampleRequest`, `FreeSample`, `pages.free_sample.index`, dan `pages.free_sample.components.modal_add`
