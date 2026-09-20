# PO Stock - Sample Feature

## 1. Ringkasan Fitur
Fitur **PO Stock - Sample** digunakan untuk mencatat pembelian barang berdasarkan supplier dan kategori utama, dengan dua tipe PO:
- `stock`: barang masuk ke alur stok reguler.
- `sample`: barang masuk ke alur sample.

Fitur ini menjadi sumber data untuk modul lain, terutama:
- **Inventory In** (penerimaan barang dari PO),
- **Inventory Stock / Inventory Sample** (hasil stok akhir),
- **Job Statement (PO Stock section)**,
- **Cost/Outcome (referensi PO pada biaya tertentu)**.

### 1.1 Update Terbaru (2026-03-12)
Perubahan penting untuk mencegah mismatch product vs supplier/category pada PO:
- Alur **Edit PO** dipindah ke **halaman dedicated** (`pages/po_stock/edit.blade.php`), tombol Edit pada DataTable sekarang redirect ke halaman edit.
- Endpoint `purchase_orders.edit` sekarang **HTML-only** (halaman edit), tanpa fallback modal.
- Ditambahkan guard race-condition di modal **create** (`abort + token check`) agar response AJAX lama tidak menimpa pilihan SKU terbaru.
- Validasi `StorePoRequest` dan `UpdatePoStockRequest` diperketat:
  - `product_id` wajib array non-empty.
  - product harus match dengan `main_category` + `supplier` PO.
  - `unit[product_id]` harus sesuai unit produk.
  - quantity/price wajib ada untuk setiap product terpilih.
- Proses `store` dan `update` sekarang dibungkus `DB::transaction` untuk mencegah partial write.
- Ditambahkan guard server-side pada edit/update: PO yang sudah `complete` atau sudah terpakai (`remaining_qty != qty`) ditolak untuk diedit.
- Data mismatch lama **tidak diubah otomatis** (audit-only policy).

### 1.2 Update Terbaru (2026-05-26)
Perubahan pada halaman history PO:
- `PO Stock History` dan `PO Sample History` sekarang memakai layout 2 card.
- Card pertama hanya menampilkan info PO.
- Card kedua memakai tab per item PO (`product_id + unit_id`), dan tiap tab berisi tabel history product terkait.
- Event `PO -> Inventory` dan `PO -> Job` tetap ditampilkan sebagai ledger kronologis, tetapi tidak lagi dicampur lintas product dalam satu tabel.
- Kolom `Stock` pada halaman ini berarti **remaining qty PO untuk product pada tab tersebut setelah event pada baris itu**, bukan stok inventory aktif.

## 2. Batasan Proses (Stock vs Sample)
- Perbedaan utama `stock` dan `sample` terjadi saat proses **Inventory In**:
  - jika `po_type = stock` maka `inventory_stocks.inventory_type = in`,
  - jika `po_type = sample` maka `inventory_stocks.inventory_type = sample`.
- Pada level PO, proses create/edit/status/histori berlaku sama untuk kedua tipe.

## 3. Peta Implementasi (File per Lapisan)

### 3.1 Routes
- `routes/web.php`
  - `Route::resource('purchase-orders', PurchaseOrderController::class)->names('purchase_orders');`
  - Endpoint tambahan:
    - `po_stock.get_po_products`
    - `po_stock.get_po_options`
    - `po_stock.change_status`
    - `po_stock.history`
    - `po_stock.detail`
    - `po_stock.export_excel`
    - `po_stock.export_pdf`

### 3.2 Controller
- `app/Http/Controllers/PurchaseOrderController.php`
  - `index`, `store`, `show`, `detail`, `edit`, `update`, `destroy`, `changeStatus`, `history`, `exportExcel`, `exportPdf`, `getPoProducts`, `getPoOptions`.
  - Catatan update:
    - `edit` menggunakan implicit binding `PoStock $purchase_order` (selaras dengan route resource `{purchase_order}`) dan return halaman edit.
    - `edit`/`update` menolak perubahan jika PO complete atau sudah dipakai transaksi turunan.
    - `store`/`update` memakai transaksi DB.
    - `update` redirect kembali ke halaman edit + flash message (flow halaman penuh, tanpa response JSON modal).
- Controller dampak lintas modul:
  - `app/Http/Controllers/InventoryInController.php`
  - `app/Http/Controllers/JobStatementController.php`
  - `app/Http/Controllers/AccountingCostController.php`

### 3.3 Request Validation
- `app/Http/Requests/PurchaseOrder/StorePoRequest.php`
- `app/Http/Requests/UpdatePoStockRequest.php`
- `app/Http/Requests/PurchaseOrder/FilterPoRequest.php`
- Terkait dampak:
  - `app/Http/Requests/StoreInventoryInRequest.php`
  - `app/Http/Requests/UpdateInventoryInRequest.php`
  - `app/Http/Requests/StoreJobPoStockRequest.php`
  - `app/Http/Requests/UpdateJobPoStockRequest.php`
  - `app/Rules/ValidQtyIn.php`

### 3.4 DataTable
- `app/DataTables/PurchaseOrderDataTable.php`
  - Menentukan filter list PO (`po_type`, `status`) dan logika disable tombol Edit/Delete berdasarkan perubahan `remaining_qty`.

### 3.5 Model
- Utama:
  - `app/Models/PoStock.php`
  - `app/Models/PoStockProduct.php`
- Terkait dampak:
  - `app/Models/InventoryIn.php`
  - `app/Models/InventoryStock.php`
  - `app/Models/JobPoStock.php`
  - `app/Models/OutcomeCheque.php`
  - `app/Models/ReturnStock.php`

### 3.6 Views
- Halaman utama PO:
  - `resources/views/pages/po_stock/index.blade.php`
  - `resources/views/pages/po_stock/edit.blade.php`
  - `resources/views/pages/po_stock/detail.blade.php`
  - `resources/views/pages/po_stock/history.blade.php`
- Komponen aksi/modals:
  - `resources/views/pages/po_stock/components/create_modal.blade.php`
  - `resources/views/pages/po_stock/components/edit_status_modal.blade.php`
  - `resources/views/pages/po_stock/components/action_button.blade.php`
  - `resources/views/pages/po_stock/components/history_to_inventory.blade.php`
  - `resources/views/pages/po_stock/components/history_to_job.blade.php`
- Tampilan fitur lain yang memakai data PO:
  - `resources/views/pages/inventory_in/components/create-modal.blade.php`
  - `resources/views/pages/job_statement/components/sections/po_stock_section.blade.php`
  - `resources/views/pages/job_statement/components/modals/add_po_stock_modal.blade.php`

### 3.7 Export
- `app/Exports/PoStockDetailExport.php`
- Template Excel:
  - `storage/app/templates/po/po_stock_detail.xlsx`
- Template PDF:
  - `resources/views/exports/pdf/po-stock-detail.blade.php`

## 4. Peta Tabel Database

## 4.1 Tabel Utama

### `po_stocks`
Fungsi: header PO Stock/Sample.

Kolom kunci:
- `id`
- `po_type` (`stock`/`sample`)
- `unique_id`
- `main_category_id`
- `supplier_id`
- `shipping_cost`
- `additional_expenses`
- `total`
- `status` (`incomplete`/`complete`)
- `arrived_warehouse`
- `note`
- `created_at`, `updated_at`

### `po_stock_product`
Fungsi: detail item per PO.

Kolom kunci:
- `id`
- `po_stock_id`
- `product_id`
- `qty`
- `remaining_qty`
- `unit_id`
- `price`
- `purchase_cost`
- `selling_price` (opsional/nullable)
- `status` (legacy/nullable)
- `created_at`, `updated_at`

## 4.2 Tabel Terdampak Langsung/Tidak Langsung

### `inventory_ins`
Fungsi: pencatatan penerimaan barang dari PO. Menyimpan referensi `po_stock_id`.

Kolom relevan:
- `po_stock_id`
- `product_id`
- `unit_id`
- `amount`, `weight`
- `warehouse_id`
- `purchase_cost`, `purchase_cost_per_unit`

### `inventory_stocks`
Fungsi: stok aktif hasil dari Inventory In.

Kolom relevan:
- `inventory_id`
- `inventory_type` (`in`, `sample`, `out`, `mutation`)
- `product_id`
- `warehouse_id`
- `unit_id`
- `amount`, `weight`
- `purchase_cost`, `purchase_cost_per_unit`

### `job_po_stocks`
Fungsi: pemakaian barang langsung dari PO ke Job Statement (tanpa lewat inventory in).

Kolom relevan:
- `job_id`
- `po_stock_id`
- `product_id`
- `qty`
- `amount`
- `note`

### `outcome_cheques`
Fungsi: catatan biaya, termasuk yang mereferensikan PO (melalui `po_stock_id`) pada skenario tertentu.

Kolom relevan:
- `po_stock_id`
- `code`, `code_type`
- `outcome_type_id`
- `amount`
- `recipient_type`, `recipient_id`

### `return_stocks` (keterkaitan lanjutan)
Fungsi: return stock/sample; supplier dapat ditelusuri kembali ke PO via `inventory_stocks -> inventory_ins -> po_stocks`.

## 4.3 Tabel Master/Referensi yang Dipakai

Tabel berikut tidak selalu ditulis oleh modul PO, tetapi dipakai untuk relasi, validasi, dan tampilan data:
- `main_categories` / model `Main_Category`
  - referensi `po_stocks.main_category_id`.
- `suppliers`
  - referensi `po_stocks.supplier_id`; dipakai juga untuk generate `unique_id`.
- `products`
  - referensi `po_stock_product.product_id`; dipakai di detail/history/options.
- `units`
  - referensi `po_stock_product.unit_id`; dipakai pada qty/price/unit_name.
- `jobs`
  - relasi tidak langsung melalui `job_po_stocks.job_id`.
- `warehouses`
  - dipakai pada proses turunan Inventory In dan Inventory Stock.

## 5. Matriks Perubahan Data per Aksi

## 5.1 Aksi di Modul PO Stock - Sample

| Aksi | Entry Point | Tabel Ditulis | Perubahan Data |
| :--- | :--- | :--- | :--- |
| Create PO | `PurchaseOrderController@store` | `po_stocks`, `po_stock_product` | Buat header PO dan detail item **dalam transaksi DB**. `remaining_qty` awal = `qty`. `purchase_cost` dihitung proporsional terhadap ongkir + additional expense. |
| Update PO | `PurchaseOrderController@update` | `po_stocks`, `po_stock_product` | Update header + replace detail item **dalam transaksi DB**. `remaining_qty` di-reset ke `qty` detail baru. |
| Delete PO | `PurchaseOrderController@destroy` | `po_stock_product`, `po_stocks` | Detach detail produk lalu hapus header PO dalam transaksi DB. |
| Change Status | `PurchaseOrderController@changeStatus` | `po_stocks` | Ubah nilai `status` (`complete`/`incomplete`). |

Catatan validasi PO (store/update):
- Supplier harus sesuai kategori utama PO.
- Product pada detail harus sesuai supplier + kategori utama PO.
- Unit detail harus cocok dengan unit pada product master.
- `product_id`, `quantity`, `price`, `unit` wajib konsisten per item.

## 5.2 Dampak ke Inventory In

| Aksi | Entry Point | Tabel Ditulis | Perubahan Data |
| :--- | :--- | :--- | :--- |
| Inventory In dari PO | `InventoryInController@store` | `inventory_ins`, `inventory_stocks`, `po_stock_product` | Untuk setiap detail PO yang qty masuk > 0: buat `inventory_ins`, kurangi `po_stock_product.remaining_qty`, lalu buat `inventory_stocks` dengan `inventory_type = in/sample` sesuai `po_type`. |
| Edit Inventory In | `InventoryInController@update` | `inventory_ins`, `inventory_stocks`, `po_stock_product` | Rehitung kuantitas masuk dan sinkronkan kembali `remaining_qty` dengan formula `(remaining + oldQty) - usedValue`. |

Catatan validasi:
- `StoreInventoryInRequest` + rule `ValidQtyIn` mencegah qty masuk melebihi `remaining_qty`.

## 5.3 Dampak ke Job Statement (PO Stock Section)

| Aksi | Entry Point | Tabel Ditulis | Perubahan Data |
| :--- | :--- | :--- | :--- |
| Add PO ke Job | `JobStatementController@storeJobPoStock` | `job_po_stocks`, `po_stock_product` | Insert baris job_po_stock lalu `remaining_qty` dikurangi sesuai qty terpakai. |
| Edit PO pada Job | `JobStatementController@updateJobPoStock` | `job_po_stocks`, `po_stock_product` | Update qty/amount/note lalu sesuaikan `remaining_qty` dengan `(remaining + oldQty) - newQty`. |
| Delete PO dari Job | `JobStatementController@destroyJobPoStock` | `job_po_stocks`, `po_stock_product` | Hapus baris job_po_stock dan kembalikan `remaining_qty` (`+ qty lama`). |

Catatan validasi:
- `StoreJobPoStockRequest` membatasi `quantity <= remaining_qty`.
- `UpdateJobPoStockRequest` membatasi `quantity <= remaining_qty + qty_lama`.

## 5.4 Efek ke Fitur Lain

- **History PO**:
  - Sumber data inventory: relasi `poStock->inventoryIns()` + join ke `inventory_stocks`.
  - Sumber data job: query ke `job_po_stocks`.
- **Cost reference**:
  - Pada kondisi tertentu di `AccountingCostController` (kode type + kategori), `outcome_cheques.po_stock_id` diisi dari input PO.
- **Inventory Stock/Sample Listing**:
  - Data hasil PO akan terlihat di daftar Inventory Stock/Sample sesuai `inventory_type`.

## 6. Matriks Syarat Enable/Disable Tombol

## 6.1 Halaman PO List (`pages/po_stock`)

| Tombol | Lokasi | Permission | Kondisi Data | Status UI |
| :--- | :--- | :--- | :--- | :--- |
| Add New | `index.blade.php` + `create_modal` | `add PO stock` | Tidak ada syarat status data | Muncul jika user punya permission |
| Detail | `action_button.blade.php` | Tidak dibatasi `@can` di view ini | Selalu | Selalu aktif |
| History | `action_button.blade.php` | Tidak dibatasi `@can` di view ini | Selalu | Selalu aktif |
| Edit | `action_button.blade.php` | `edit PO stock` | Hanya jika PO **bukan** `complete` dan semua detail masih `remaining_qty == qty` | Redirect ke halaman edit; dapat menjadi `disabled` |
| Delete | `action_button.blade.php` | `delete PO stock` | Hanya jika PO **bukan** `complete` dan semua detail masih `remaining_qty == qty` | Tombol dapat menjadi `disabled` |
| Change Status | `action_button.blade.php` | `edit PO stock` | Tidak cek `remaining_qty`; tersedia selama user boleh edit | Aktif |

Sumber logika disable Edit/Delete:
- `PurchaseOrderDataTable@dataTable` menghitung:
  - disable jika ada detail dengan `remaining_qty !== qty`.

## 6.2 Tombol/Options pada Fitur Terkait

### Inventory In (Add)
- Pilihan PO (`getPoOptions`) hanya menampilkan:
  - PO dengan `status = incomplete`, dan
  - memiliki minimal satu detail `remaining_qty > 0`.
- Efeknya: PO completed atau habis kuota tidak tersedia untuk dipilih.

### Job Statement (Add PO Stock)
- Modal add menggunakan sumber opsi PO yang sama (`getPoOptions`).
- Produk pada PO ditampilkan melalui `getPoProducts` beserta `remaining_qty`.
- Validasi server-side tetap membatasi qty agar tidak melebihi sisa.

### Job Statement (Edit/Delete PO Stock row)
- Section action hanya tampil saat `job->statusIsOpen()`.
- Tombol edit/delete juga dibatasi permission:
  - `job statement edit PO stock`
  - `job statement delete PO stock`

## 7. Alur End-to-End Singkat
1. User membuat PO bertipe `stock` atau `sample` di modul PO.
2. Sistem menyimpan header (`po_stocks`) dan detail (`po_stock_product`) dengan `remaining_qty = qty`.
3. Saat PO diedit, proses edit dilakukan melalui halaman edit dedicated (bukan modal); server juga memvalidasi PO masih editable.
4. Saat PO dipakai:
   - lewat **Inventory In**: terbentuk `inventory_ins` dan `inventory_stocks`, `remaining_qty` berkurang.
   - lewat **Job PO Stock**: terbentuk `job_po_stocks`, `remaining_qty` berkurang.
5. Perubahan/edit/hapus transaksi turunan akan menyesuaikan kembali `remaining_qty`.
6. Status PO bisa diubah manual ke `complete/incomplete`; dan memengaruhi ketersediaan opsi PO di fitur lain (karena filter `getPoOptions` memakai status `incomplete`).

## 8. Aturan Validasi Penting
- Create PO:
  - `po_type` wajib `stock` atau `sample`.
  - qty minimal 1, harga minimal 0, total minimal 0, note wajib.
- Update PO:
  - qty minimal 0, total minimal 100, status wajib.
- Inventory In:
  - qty per produk minimal 0 dan tidak boleh melebihi `remaining_qty`.
- Job PO Stock:
  - qty tidak boleh melebihi sisa (`remaining_qty`) sesuai konteks create/update.

## 9. Catatan Implementasi
- Dokumentasi ini merefleksikan implementasi terbaru berdasarkan source code aktual.
- Perubahan route tidak ada; penyesuaian utama ada di perilaku UI edit (modal -> halaman) dengan endpoint edit/update berorientasi halaman penuh.

## 10. Catatan Integritas Data (Update 2026-03-10)

### 10.1 Kasus Mismatch Historis
- Ditemukan kasus historis PO yang detail produknya tidak sesuai `main_category`/`supplier` header.
- Akar masalah utama: data edit modal dapat tertukar antar-row pada implementasi lama, lalu lolos karena validasi backend belum memeriksa relasi lintas entitas.

### 10.2 Kebijakan Penanganan
- Perbaikan saat ini fokus pada **prevention** (guard frontend + validasi backend + transaksi).
- Data historis mismatch tetap dipertahankan untuk audit manual (tidak auto-fix).
