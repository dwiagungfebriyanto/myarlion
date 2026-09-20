# Fitur Inventory Stock

## Ringkasan

Fitur **Inventory Stock** menampilkan daftar stok inventory aktif per warehouse untuk dua bucket:

- `stock` untuk stok reguler
- `sample` untuk stok sample

Implementasi aktif berada di `InventoryStockController` dengan halaman utama:

- `resources/views/pages/inventory_stock/index.blade.php` untuk tampilan operasional aktif

Pemisahan reguler vs sample saat ini mengikuti field `stock_bucket`, dengan fallback dari `inventory_type` melalui `InventoryStock::getStockBucket()`.

## Halaman dan Route

Route utama:

- `product.inventory-stock.index`
- `product.inventory_stock.history`
- `product.inventory_stock.sample_history`

Mode halaman:

- jika query `type` bukan `sample`, halaman memakai bucket `stock`
- jika query `type=sample`, halaman memakai bucket `sample`

Filter yang dipakai pada list:

- `warehouse`
- `show_all`

Aturan filter:

- default hanya menampilkan saldo `> 0`
- jika `show_all=1`, sistem juga menampilkan saldo `0` atau negatif bila ada anomali

Catatan:

- list inventory saat ini menjadi tampilan operasional utama

## Struktur Field Penting

Field aktif yang paling mempengaruhi fitur ini:

- `po_stock_id`
- `stock_bucket`
- `product_id`
- `warehouse_id`
- `unit_id`
- `amount`
- `weight`
- `purchase_cost`
- `purchase_cost_per_unit`
- `history_overall_qty`
- `history_overall_avg`

Aturan interpretasi:

- unit `Kg` membaca quantity dari `weight`
- unit selain `Kg` membaca quantity dari `amount`
- `purchase_cost_per_unit` menyimpan biaya per unit yang dibawa dari sumber inventory
- untuk stock yang memiliki lineage PO, `purchase_cost` menyimpan nilai referensi total produk PO:

  ```text
  inventory_stocks.purchase_cost = po_stock_product.qty * po_stock_product.purchase_cost
  inventory_stocks.purchase_cost_per_unit = po_stock_product.purchase_cost
  ```

- contoh: `qty PO = 100`, `purchase_cost detail PO = 5.000/unit`, maka `purchase_cost inventory stock = 500.000`
- nilai reference cost PO sama pada setiap warehouse dan tidak berubah saat quantity berkurang, dikembalikan, dipindahkan, atau menjadi nol
- biaya transaksi keluar tetap dihitung dari `purchase_cost_per_unit * qty transaksi`
- stock tanpa lineage PO tetap memakai valuasi saldo proporsional terhadap quantity (`purchase_cost = qty aktif * purchase_cost_per_unit`)
- stock historis dengan lineage PO tetapi tanpa detail produk mempertahankan nilai cost yang sudah tersimpan
- `stock_bucket` menentukan pemisahan reguler vs sample
- `getStockBucket()` fallback ke:
    - `sample` jika `inventory_type === sample`
    - `stock` untuk selain itu

Pengelolaan saldo aktif dilakukan melalui `InventoryStockBalanceService`:

- `subtractFromStock(...)` dan `restoreToStock(...)` hanya mengubah quantity untuk stock PO
- `transferStock(...)` membawa reference cost dan unit cost source ke warehouse tujuan
- `applyDelta(...)` tetap mengubah total cost secara proporsional untuk stock non-PO

Service legacy `StockUpdateService` tidak lagi menjadi bagian dari flow aktif.

## Data yang Ditampilkan

Kolom list aktif:

- `ID`
- `Stock`
- `Product`
- `PO`
- `Supplier`
- `Warehouse`
- `Purchase Cost`
- `Purchase Cost per Unit`
- `Stock Bucket`
- `Created at`
- `Updated at`
- `Action`

Catatan penting:

- kolom `Source Inventory` bukan lagi kolom list aktif
- supplier diambil dari `getPoStock()`
- `Stock Bucket` ditampilkan sebagai label `Stock` atau `Sample`

## Action dan Endpoint Terkait

Action pada list:

- tombol `History`
    - bucket reguler menuju `product.inventory_stock.history`
    - bucket sample menuju `product.inventory_stock.sample_history`
    - hanya tampil jika stock bisa ditelusuri ke PO
- tombol `Return Stock`
    - hanya tampil jika quantity stock masih `> 0`

Endpoint pendukung opsi stock:

- `api.inventory_stock.ready_stock_options`

Perilaku endpoint saat ini:

- response tetap mengembalikan option per `inventory_stocks.id`
- payload option juga membawa `stock_bucket`
- request bisa memakai flag `show_bucket` untuk menambahkan label bucket pada option

## Integritas Cost dan Koreksi Data

Unit cost transaksi tetap berasal dari histori `inventory_ins`, sedangkan reference cost stock PO berasal dari detail PO:

- pasangan canonical adalah `inventory_stocks.po_stock_id + product_id` ke `po_stock_product`
- reference total dihitung dari `po_stock_product.purchase_cost * po_stock_product.qty`
- `po_stocks.total` tidak dipakai langsung untuk tiap baris stock; nilai itu adalah total/header PO
- stock tanpa pasangan detail PO tidak dikoreksi otomatis

`products.harga_tertinggi` harus sama dengan nilai maksimum `purchase_cost_per_unit` dari stock aktif produk tersebut.

Migration `2026_06_15_120000_fix_inventory_cost_integrity` menjalankan koreksi satu kali untuk:

- saldo aktif `inventory_stocks`
- snapshot cost otomatis pada `free_samples`
- snapshot cost otomatis pada `job_statements`
- `products.harga_tertinggi`

Migration menyimpan nilai sebelum dan sesudah koreksi di tabel
`inventory_cost_integrity_backups_20260615`. Rollback hanya diizinkan jika row terkait belum berubah setelah migration; guard ini mencegah rollback menimpa transaksi operasional baru.

Migration `2026_06_15_120000_fix_inventory_cost_integrity` juga mengubah
`inventory_stocks.purchase_cost` menjadi reference total produk PO pada semua warehouse dan saldo nol.
Stock orphan atau mutation tanpa pasangan detail PO dibiarkan memakai nilai tersimpan. Koreksi reference
cost memakai tabel backup internal migration yang sama dan tidak membuat tabel backup tambahan.

Deployment manual menjalankan migration melalui:

```bash
php artisan migrate --force
```

File SQL audit atau patch manual tidak diperlukan untuk menerapkan koreksi data.

## Catatan Penting

- Halaman list menjadi sumber tampilan operasional utama untuk stock aktif
- Penggabungan quantity operasional saat ini sudah mengikuti key `SKU - PO - Warehouse`
- Quantity stock PO bergerak terpisah dari reference cost; snapshot transaksi tetap memakai unit cost source
- History reguler dan history sample dipisah ke controller, service, dan route yang berbeda
- Dokumentasi ini mengikuti implementasi aktif pada `InventoryStockController`, `InventoryStock`, `InventoryStockBalanceService`, dan `index.blade.php`
