# Panduan Inventory Stock

## Ringkasan
Halaman **Inventory Stock** dipakai untuk melihat stok inventory per warehouse. Sistem saat ini memisahkan data menjadi dua tampilan:

- **Inventory Stock** untuk stok reguler
- **Inventory Sample** untuk stok sample

Keduanya memakai tampilan list yang sama, tetapi isi datanya dibedakan oleh `stock bucket`.
List ini menjadi tampilan operasional utama karena quantity aktif sudah digabung berdasarkan `SKU - PO - Warehouse`.

## Yang Perlu Diketahui User
Perilaku aktif pada halaman saat ini:
- ada filter `Warehouse`
- ada checkbox `Show All Data`
- ada kolom `Supplier`
- ada kolom `Stock Bucket`
- ada tombol `History`
- ada tombol `Return Stock`

Default tampilan:
- hanya menampilkan stock yang masih lebih dari `0`
- jika `Show All Data` dicentang, sistem juga menampilkan saldo `0` atau negatif bila ada anomali

## Cara Membaca Halaman
Kolom penting yang akan dilihat user:
- `PO`: membuka detail PO pada tab baru jika data PO tersedia
- `Supplier`: nama supplier dari PO asal stock
- `Stock Bucket`: penanda apakah row termasuk `Stock` atau `Sample`
- `History`: membuka halaman histori ledger sesuai bucket
- `Return Stock`: memulai proses return bila stock masih tersedia

Aturan tombol history:
- pada halaman reguler, tombol membuka **Stock History**
- pada halaman sample, tombol membuka **Sample History**

Aturan tombol return:
- hanya muncul jika quantity stock masih lebih dari `0`

## Filter dan Tampilan Data
### Filter Warehouse
User dapat memilih warehouse untuk mempersempit daftar stock yang tampil.

### Show All Data
Checkbox ini dipakai untuk audit atau pengecekan saldo tidak normal.

Jika tidak dicentang:
- hanya stock aktif dengan saldo `> 0` yang tampil

Jika dicentang:
- semua row untuk filter saat ini ikut tampil, termasuk saldo `0` atau negatif

## Tips Penggunaan
- Gunakan tampilan default untuk kebutuhan operasional harian
- Gunakan `Show All Data` saat perlu audit atau cek anomali saldo
- Gunakan kolom `Supplier` dan link `PO` untuk menelusuri asal stock
- Gunakan `History` jika perlu melihat urutan masuk/keluar stock secara detail
- Perhatikan `Stock Bucket` agar tidak tertukar antara stok reguler dan sample

## Catatan Penting
- Halaman ini adalah tampilan operasional utama untuk stok aktif yang sudah digabung sesuai key operasional sistem
- `Inventory Sample` adalah variasi dari halaman list yang sama
- History sample tersedia melalui tombol `History` pada mode sample
- Untuk detail teknis, lihat doc feature `Inventory Sample` dan `Inventory Sample History`
- Panduan ini mengikuti tampilan aktif pada `Inventory Stock` dan `Inventory Sample` saat ini
