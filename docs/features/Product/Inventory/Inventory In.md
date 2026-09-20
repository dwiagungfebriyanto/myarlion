# Fitur Inventory In

## Ringkasan
Fitur **Inventory In** digunakan untuk mencatat barang masuk ke inventory berdasarkan **PO Stock**. Halaman ini menampilkan daftar seluruh data barang masuk, menyediakan entry point untuk menambah data baru dari PO, serta menyediakan action button pada tabel untuk melihat detail, melihat histori pemakaian stock ke job, dan mengubah data yang sudah ada.

Controller utama fitur ini adalah `App\Http\Controllers\InventoryInController`, dengan view utama `resources/views/pages/inventory_in/index.blade.php`.

## Tujuan Halaman dan Sumber Data

### Tujuan Halaman
Halaman `product.inventory-in.index` dipakai untuk:
- melihat daftar barang masuk ke inventory dari PO,
- menambah data inventory masuk baru dari PO,
- melihat detail data inventory masuk,
- melihat histori pemakaian stock ke job,
- mengubah data inventory masuk yang sudah tercatat.

### Sumber Data Utama
Pada halaman index, data utama diambil melalui:

`InventoryIn::orderByDesc('datetime')->get()`

Artinya, daftar `Inventory In` ditampilkan dengan urutan datetime terbaru lebih dulu.

### Data Pendukung yang Dipakai Saat Render
Controller juga menyiapkan data pendukung berikut:

- daftar product,
- daftar unit yang diurutkan berdasarkan `unit_name`,
- daftar warehouse aktif dari `Warehouse::getActiveWarehouse()`,
- opsi PO dari `PurchaseOrderController::getPoOptions()`,
- relasi product, warehouse, unit, dan PO yang dipakai di tabel, modal detail, modal edit, dan halaman history.

### Catatan Implementasi
Class `InventoryInDataTable` masih ada di codebase, tetapi halaman aktif tidak memakai server-side Yajra DataTable.

Perilaku aktual halaman saat ini:
- tabel dirender langsung melalui loop Blade terhadap `$inventoryIns`,
- kolom `Action` memakai komponen `resources/views/pages/inventory_in/components/action-button-2.blade.php`.

## Data yang Ditampilkan di Tabel
Tabel pada halaman `Inventory In` menampilkan kolom:

- `ID`
- `Datetime`
- `PO`
- `Product`
- `Status`
- `Quantity`
- `Warehouse`
- `Purchase Cost`
- `Purchase Cost per Unit`
- `Action`

### Logika Penting per Kolom

#### Datetime
Kolom `Datetime` menampilkan nilai `datetime` dari data `InventoryIn`.

#### PO
Kolom `PO` menampilkan:
- `po_type`,
- link ke detail PO stock jika relasi `poStock` tersedia.

Jika relasi `poStock` ada, format yang ditampilkan adalah:

`{po_type}: {unique_id}`

dengan `unique_id` sebagai link yang dibuka di tab baru.

#### Product
Kolom `Product` memakai `product->skuFormat()`, sehingga yang ditampilkan adalah SKU product dalam format aplikasi.

#### Status
Kolom `Status` menampilkan nilai status yang disimpan pada record `inventory_ins`.

#### Quantity
Kolom `Quantity` memakai method `quantityValue()`.

Logikanya:
- jika unit barang adalah `Kg`, nilai diambil dari `weight`,
- selain itu, nilai diambil dari `amount`,
- hasil akhirnya ditampilkan bersama nama unit.

#### Warehouse
Kolom `Warehouse` menampilkan `warehouse->warehouse_name`.

#### Purchase Cost dan Purchase Cost per Unit
Kedua nilai ini sudah terformat lewat accessor model `InventoryIn`, sehingga tampil sebagai nilai mata uang.

### Entry Point Tambah Data
Tombol `Add New` tampil di header halaman sebagai entry point untuk create data baru.

Kondisi tampil:
- hanya muncul jika user memiliki permission `add inventory in`.

Tombol ini bukan bagian dari action button pada kolom `Action`, tetapi tetap penting sebagai pintu masuk proses create.

## Action Button pada Datatable dan Kondisi Tampil

### Detail
**Fungsi**

Membuka modal detail dan mengambil data dari `InventoryInController@show`.

**Data yang ditampilkan**

Modal detail menampilkan:
- PO,
- product,
- status,
- datetime,
- quantity,
- warehouse,
- notes,
- purchase cost,
- purchase cost per unit.

**Cara kerja**

- Tombol memakai class `btn-detail`.
- Saat diklik, browser mengirim AJAX `GET` ke route `product.inventory-in.show`.
- Response JSON dipakai untuk mengisi elemen di modal `#detailModal`.

**Kondisi tampil**

- Selalu tampil pada setiap row di tabel aktif.

### History
**Fungsi**

Membuka halaman histori pemakaian stock ke job.

**Route**

`inventory_in.history`

**Cara kerja**

- Tombol berupa link yang dibuka di tab baru.
- Halaman history menampilkan ringkasan data inventory in, current stock, dan timeline penggunaan stock ke job.

**Kondisi tampil**

- Selalu tampil pada setiap row di tabel aktif.

**Catatan**

Data histori diambil dari `JobStatement` berdasarkan `inventory_stock_id` milik `InventoryIn`.

### Edit
**Fungsi**

Membuka modal edit dan mengambil data awal dari `InventoryInController@edit`.

**Data awal yang dimuat**

Modal edit memuat:
- PO,
- SKU,
- status,
- datetime,
- unit,
- quantity,
- warehouse options,
- notes,
- purchase cost,
- action URL untuk update.

**Cara kerja**

- Tombol memakai class `btn-edit`.
- Saat diklik, browser mengirim AJAX `GET` ke route `product.inventory-in.edit`.
- Response dipakai untuk mengisi field pada modal `#editModal`.
- Submit edit mengarah ke route `product.inventory-in.update`.

**Kondisi tampil**

- Hanya tampil jika user memiliki permission `edit inventory in`.

**Catatan aturan edit**

- quantity edit tidak boleh lebih kecil dari total stock yang sudah terpakai untuk `inventory out` dan `inventory lost`,
- jika quantity berubah, UI meminta ulang purchase cost melalui endpoint `po_stock.get_po_product_purchase_cost`.

### Catatan Perilaku Aktual di Kode
- Komponen aktif yang dipakai tabel adalah `action-button-2.blade.php`.
- Masih ada komponen lama `action-button.blade.php` yang memuat pola serupa dan jejak tombol delete yang dikomentari.
- Komponen lama tersebut bukan komponen aktif pada halaman `Inventory In` saat ini.

## Alur Bisnis Create, Detail, Edit, dan History

### Alur Create
Ringkasan alurnya:

1. User klik `Add New`.
2. User memilih PO pada modal create.
3. UI memanggil endpoint `po_stock.get_po_products`.
4. Setiap produk dalam PO dibuatkan input quantity sendiri di area form quantity.
5. User dapat mengisi `0` untuk SKU yang belum datang.
6. Form dikirim ke `InventoryInController@store`.

### Perilaku Store
Pada `InventoryInController@store`, proses yang terjadi adalah:

- request divalidasi oleh `StoreInventoryInRequest`,
- PO diambil berdasarkan `po_stock`,
- controller melakukan loop untuk setiap detail produk pada PO,
- hanya produk dengan qty masuk `!= 0` yang diproses,
- dibuat record `inventory_ins`,
- `po_stock_product.remaining_qty` dikurangi sesuai qty masuk,
- dibuat record `inventory_stocks` melalui relasi `inventoryStock()`,
- `inventory_type` untuk stock hasil create menjadi:
  - `in` jika `po_type = stock`,
  - `sample` jika `po_type` bukan `stock`,
- setelah itu controller memanggil helper:
  - `updateHighestPriceProduct(...)`,
  - `updateAvgOnAddInventory(...)`,
  - `updateProductStock(...)`.

### Perhitungan Harga (Store/Update)
Berikut komponen perhitungan harga yang terjadi saat proses Inventory In.

#### 1) `purchase_cost` (total biaya pembelian per baris Inventory In)
Pada proses store, nilai `purchase_cost` dihitung dengan kondisi:

- jika `qtyIn === poDetail->qty` **dan** jumlah item PO hanya 1:

`purchase_cost = po_stock.total`

- selain itu:

`purchase_cost = poDetail.purchase_cost * qtyIn`

#### 2) `purchase_cost_per_unit` (biaya per unit)
- pada proses store:

`purchase_cost_per_unit = poDetail.purchase_cost`

- pada proses update:

`purchase_cost_per_unit = purchase_cost / usedValue`

dengan:
- `usedValue = amount` jika unit `Pcs`,
- `usedValue = weight` jika unit `Kg`.

#### 3) `harga_rata_rata` produk (weighted average)
Setelah Inventory In tersimpan, helper `updateAvgOnAddInventory(...)` akan memperbarui `product.harga_rata_rata`.

- jika belum ada riwayat stock sebelumnya:

`harga_rata_rata = costPerPcs`

- jika sudah ada riwayat stock:

`harga_rata_rata = ((latestStock * latestAvg) + (orderQty * costPerPcs)) / (latestStock + orderQty)`

dengan:
- `latestStock` = `product.qty` saat ini,
- `latestAvg` = `product.harga_rata_rata` saat ini (atau nilai historis saat delete/recompute),
- `orderQty` = qty incoming (`qtyIn`),
- `costPerPcs` = `purchase_cost_per_unit`.

Selain update ke tabel `products`, nilai rata-rata hasil hitung juga disimpan ke:

`inventory_stocks.history_overall_avg`

#### 4) `harga_tertinggi` produk
Setelah Inventory In tersimpan atau diupdate, helper `updateHighestPriceProduct(...)` menghitung ulang:

`harga_tertinggi = MAX(inventory_stocks.purchase_cost_per_unit)`

Filter yang dipakai:
- hanya `inventory_stocks` dengan `unit_id` yang sama dengan product,
- hanya baris dengan kuantitas aktif `> 0` (field `amount` atau `weight` sesuai unit).

### Perilaku Show
`InventoryInController@show` mengembalikan response JSON yang dipakai modal detail.

Data yang disiapkan meliputi:
- data `inventoryIn`,
- `poStock`,
- `product`,
- `productSkuFormat`,
- `quantityValue`,
- `warehouse`.

### Perilaku Edit dan Update
`InventoryInController@edit` menyiapkan data awal untuk modal edit, termasuk warehouse options dan URL update.

Pada `InventoryInController@update`, proses utamanya adalah:

- request divalidasi oleh `UpdateInventoryInRequest`,
- dihitung `stockReduction = inventoryOutsSum() + inventoryLostsSum()`,
- nilai amount/weight baru ditentukan berdasarkan unit (`Pcs` atau `Kg`),
- `inventory_ins` diperbarui,
- `purchase_cost_per_unit` dihitung ulang dengan formula:

`purchase_cost / usedValue`

- `po_stock_product.remaining_qty` disinkronkan dengan formula:

`(remaining_qty + oldQty) - usedValue`

- `inventory_stock` terkait ikut diperbarui,
- jika warehouse berubah, maka warehouse pada data turunan juga disinkronkan:
  - `inventory_stock`,
  - `inventory_outs`,
  - `inventory_losts`,
- setelah itu controller memanggil helper:
  - `updateHighestPriceProduct(...)`,
  - `updateProductStock(...)`.

### Perilaku History
`InventoryInController@history`:

- mengambil `JobStatement` berdasarkan `inventoryIn->inventoryStock->id`,
- menampilkan total quantity yang sudah dipakai ke job,
- menampilkan timeline pemakaian per job pada halaman history.

### Catatan tentang Destroy
Controller `InventoryInController` memiliki method `destroy`.

Perilakunya secara singkat:
- menghapus `inventory_in`,
- menghapus `inventory_stock` terkait,
- menghapus relasi `inventory_outs` dan `inventory_losts` terkait,
- memanggil helper sinkronisasi ulang stock dan average product.

Namun pada datatable aktif saat ini tidak ada tombol hapus, sehingga aksi delete bukan bagian dari action button halaman aktif.

## Validasi, Permission, dan Aturan Bisnis

### Permission
Permission yang dipakai fitur ini:

- `add inventory in` untuk create,
- `edit inventory in` untuk edit dan update.

### Validasi Create
Validasi create dilakukan oleh `StoreInventoryInRequest`.

Rule yang penting:
- `po_stock` wajib ada dan valid,
- `status` wajib string,
- `datetime` wajib date,
- `warehouse` wajib valid,
- `unit.*` wajib valid,
- `quantity.*` wajib numeric, minimum `0`, dan divalidasi lagi oleh rule `ValidQtyIn`,
- `notes` bersifat nullable.

### Aturan Bisnis Create
- Quantity tidak boleh melebihi `remaining_qty` pada detail PO.
- Quantity `0` diperbolehkan untuk SKU yang belum datang.
- `purchase_cost_per_unit` diambil dari detail PO.
- `purchase_cost` total dihitung berdasarkan qty masuk, kecuali pada kondisi satu detail produk dengan qty penuh, di mana total PO dipakai sebagai purchase cost total.

### Validasi Update
Validasi update dilakukan oleh `UpdateInventoryInRequest`.

Rule yang penting:
- `status` wajib string,
- `datetime` wajib date,
- `warehouse` wajib valid,
- `quantity` minimum sebesar total stock yang sudah keluar/hilang,
- `purchase_cost` wajib numeric dengan minimum `100`,
- `notes` bersifat nullable.

### Aturan Unit Quantity
Unit quantity mengikuti unit barang:

- `Pcs` memakai kolom `amount`,
- `Kg` memakai kolom `weight`.

Logika ini dipakai baik saat create maupun saat update.

## Route dan Endpoint Terkait

- `product.inventory-in.index`
  - halaman daftar Inventory In.

- `product.inventory-in.store`
  - endpoint create Inventory In.

- `product.inventory-in.show`
  - endpoint detail untuk modal.

- `product.inventory-in.edit`
  - endpoint pengambilan data awal modal edit.

- `product.inventory-in.update`
  - endpoint update Inventory In.

- `inventory_in.history`
  - halaman histori pemakaian stock ke job.

- `po_stock.get_po_products`
  - endpoint utilitas untuk mengambil daftar product dalam PO saat create.

- `po_stock.get_po_product_purchase_cost`
  - endpoint utilitas untuk menghitung ulang purchase cost saat edit quantity.

## Skenario Verifikasi
Berikut skenario verifikasi yang relevan agar dokumentasi tetap sesuai dengan implementasi saat ini:

- halaman index memuat daftar `InventoryIn` urut `datetime` descending,
- tombol `Add New` hanya tampil untuk user dengan permission `add inventory in`,
- kolom `Action` aktif hanya memuat `Detail`, `History`, dan `Edit`,
- tombol `Edit` hanya tampil untuk user dengan permission `edit inventory in`,
- modal detail mengambil data dari `show`,
- halaman history mengambil `JobStatement` berdasarkan `inventoryStock` milik `InventoryIn`,
- create dari PO hanya memproses SKU dengan qty masuk non-zero,
- create mengurangi `remaining_qty` dan membuat `inventory_stock`,
- tipe stock hasil create mengikuti `po_type` yaitu `in` atau `sample`,
- update menyinkronkan ulang `inventory_ins`, `inventory_stocks`, dan `po_stock_product.remaining_qty`,
- perubahan warehouse pada update ikut menyinkronkan data turunan yang dikelola controller.
