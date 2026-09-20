# Inventory Sample History

## Ringkasan
Halaman **Inventory Sample History** menampilkan ledger read-only untuk sample pada scope:

- `supplier_id`
- `product_id`
- `warehouse_id`

Halaman ini dipakai untuk:
- melihat urutan event sample secara kronologis
- membaca running balance per event
- membandingkan `Current Stock` dengan `Ledger Ending Stock`

## Entry Point dan Validasi
- Route: `product.inventory_stock.sample_history`
- Controller: `InventorySampleHistoryController@show`
- View: `resources/views/pages/inventory_stock/sample_history.blade.php`

Validasi akses:
- anchor row harus berasal dari `inventory_stocks.inventory_type = sample`
- sample harus punya lineage ke PO melalui `inventoryStock->getPoStock()`

Jika salah satu kondisi tidak terpenuhi, controller mengembalikan `404`.

## Data yang Ditampilkan
Header halaman menampilkan:
- `Supplier`
- `Product`
- `Warehouse`
- `Unit`
- `Current Stock`
- `Ledger Ending Stock`
- `Anchor Sample ID`

`Ledger Ending Stock` memiliki popover penjelasan. Jika nilainya tidak sama dengan `Current Stock`, halaman menampilkan warning mismatch.

Tabel ledger menampilkan:
- `Code`
- `Date`
- `Description`
- `Qty`
- `Stock`
- `Value`
- `Reference`
- `Remark`

Aturan tampil:
- row masuk berwarna kuning muda
- row keluar berwarna biru muda
- `Qty` memakai tanda `+` atau `-`
- `Stock` adalah saldo berjalan setelah event
- `Reference` saat ini ditampilkan sebagai teks biasa
- `Remark` adalah note/remark sederhana, bukan unit-value formatting seperti history stock reguler

## Sumber Data Ledger
Assembly ledger dilakukan oleh `InventorySampleHistoryService`.

Scope ledger diturunkan dari anchor sample:
- `supplier_id` dari `inventoryStock->getPoStock()`
- `product_id` dari anchor sample
- `warehouse_id` dari anchor sample

Event yang saat ini dicakup:
- `Sample In`
- `Transfer Out`
- `Transfer In`
- `Job Sample`
- `Free Sample`
- `Inventory Lost`
- `Return Sample`

Aturan hitung:
- quantity dibaca dari `weight` untuk unit `Kg`, selain itu dari `amount`
- event diurutkan ascending berdasarkan effective date, type order, lalu `id`
- `Ledger Ending Stock` adalah akumulasi seluruh `qty` event
- `Current Stock` adalah total saldo sample aktif agregat pada scope yang sama

## Catatan Penting
- Halaman ini bersifat **read-only**
- History sample dipisah dari history stock reguler pada level route, controller, dan service
- Dokumentasi ini mengikuti implementasi aktif pada `InventorySampleHistoryController`, `InventorySampleHistoryService`, dan `sample_history.blade.php`
