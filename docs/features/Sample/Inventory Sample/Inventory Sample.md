# Fitur Inventory Sample

## Ringkasan
Fitur **Inventory Sample** adalah mode sample dari modul **Inventory Stock**. Halaman ini menampilkan daftar sample aktif per warehouse dengan bucket:

- `stock_bucket = sample`

Implementasi aktif tetap memakai:
- `InventoryStockController`
- `resources/views/pages/inventory_stock/index.blade.php`

Perbedaannya dengan stock reguler ada pada query `type=sample` dan bucket sample yang dipakai saat mengambil data.

## Halaman dan Route
Route yang dipakai:
- `product.inventory-stock.index` dengan `?type=sample`
- `product.inventory_stock.sample_history`

Mode sample:
- list tetap memakai halaman Inventory Stock yang sama
- history sample memakai route terpisah

Filter yang aktif:
- `warehouse`
- `show_all`

Aturan filter:
- default hanya menampilkan sample dengan saldo `> 0`
- jika `show_all=1`, sistem juga menampilkan saldo `0` atau negatif bila ada anomali

## Data yang Ditampilkan
Kolom list aktif pada mode sample:
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

Catatan:
- `Stock Bucket` pada mode ini akan bernilai `Sample`
- supplier diambil dari `getPoStock()`
- list operasional sample mengikuti penggabungan quantity aktif yang dipakai sistem

## Action dan Perilaku
Action yang aktif pada mode sample:
- tombol `History`
  - membuka `product.inventory_stock.sample_history`
  - hanya tampil jika sample bisa ditelusuri ke PO
- tombol `Return Stock`
  - tetap tampil jika quantity sample masih `> 0`

Perilaku umum:
- filter `warehouse` dan `show_all` sama seperti mode stock reguler
- list sample menjadi tampilan operasional utama untuk stok sample aktif

## Catatan Penting
- Mode sample tetap memakai halaman list yang sama dengan Inventory Stock
- History sample dipisah ke controller dan service terpisah dari history stock reguler
- Dokumentasi ini mengikuti implementasi aktif pada `InventoryStockController`, `InventoryStock`, dan `index.blade.php`
