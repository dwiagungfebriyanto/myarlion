# Inventory Stock History

## Ringkasan
Halaman **Inventory Stock History** menampilkan ledger read-only untuk stok reguler pada scope:

- `po_stock_id`
- `product_id`
- `warehouse_id`
- `unit_id`
- `stock_bucket = stock`

Halaman ini dipakai untuk:
- melihat urutan event stok reguler secara kronologis
- membaca running balance per event
- membandingkan `Current Stock` dengan `Ledger Ending Stock`

## Entry Point dan Validasi
- Route: `product.inventory_stock.history`
- Controller: `InventoryStockHistoryController@show`
- View: `resources/views/pages/inventory_stock/history.blade.php`

Validasi akses:
- anchor stock harus lolos `getStockBucket() === 'stock'`
- stock harus bisa ditelusuri ke PO asal melalui `getPoStock()`

Jika salah satu kondisi tidak terpenuhi, controller mengembalikan `404`.

## Data yang Ditampilkan
Header halaman menampilkan:
- `PO`
- `Supplier`
- `Product`
- `Warehouse`
- `Unit`
- `Current Stock`
- `Ledger Ending Stock`
- `Anchor Stock ID`

`Ledger Ending Stock` memiliki popover penjelasan. Jika nilainya tidak sama dengan `Current Stock`, halaman menampilkan warning mismatch.

Tabel ledger menampilkan:
- `SKU`
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
- `Reference` menjadi link jika service mengirim `reference_url`
- `Remark` berisi nilai per unit hasil formatting service, bukan note mentah transaksi

## Sumber Data Ledger
Assembly ledger dilakukan oleh `InventoryStockHistoryService`.

Service membentuk scope dari anchor stock menggunakan:
- `po_stock_id`
- `product_id`
- `warehouse_id`
- `unit_id`
- `stock_bucket`

Event yang saat ini dicakup:
- `Inventory In`
- `Transfer Out`
- `Transfer In`
- `Mutation Out`
- `Mutation In`
- `Job Stock`
- `Free Sample`
- `Inventory Lost`
- `Return Stock`

Aturan hitung:
- unit `Kg` membaca quantity dari `weight`, selain itu dari `amount`
- beberapa event memakai field quantity khusus seperti `job_statements.quantity`, `inventory_mutations.qty_mutation`, `inventory_mutations.qty`, dan `return_stocks.qty`
- service memakai relasi `destination_stock_id`, `original_stock_id`, `po_stock_id`, dan `stock_bucket` untuk menentukan source event
- event diurutkan ascending berdasarkan tanggal efektif, urutan tipe event, lalu `id`
- `Ledger Ending Stock` adalah akumulasi seluruh `qty` event
- `Current Stock` diambil dari agregat `scopeStocks` pada scope yang sama

## Catatan Penting
- Halaman ini hanya untuk bucket reguler; history sample memakai route dan service terpisah
- Halaman ini bersifat **read-only**
- Dokumentasi ini mengikuti implementasi aktif pada `InventoryStockHistoryController`, `InventoryStockHistoryService`, dan `history.blade.php`
