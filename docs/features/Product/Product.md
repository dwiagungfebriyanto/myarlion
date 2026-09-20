# Fitur Product

## Ringkasan
Modul **Product** adalah master data SKU yang dipakai lintas modul seperti inventory, PO, dan job. Controller utamanya adalah `ProductController`.

Fitur ini berfokus pada:
- menampilkan daftar product dalam datatable,
- menambah product baru,
- mengubah data product,
- melihat detail product,
- menyediakan endpoint relasi kategori dan opsi product untuk modul lain.

Create dan update product tidak menghitung ulang stok fisik secara langsung. Nilai seperti `qty`, `harga_rata_rata`, dan `harga_tertinggi` hanya ditampilkan dari data yang sudah tersimpan pada model product.

## Entry Point dan Route
Route resource aktif:
- `product.list.index`
- `product.list.create`
- `product.list.store`
- `product.list.show`
- `product.list.edit`
- `product.list.update`
- `product.list.destroy`

Endpoint pendukung:
- `product.fetchMainCategory`
- `options.product`

Entry point UI utama:
- halaman list: `pages.product.list.index`
- tombol `Add New` memuat modal create via AJAX
- tombol detail memuat JSON ke modal view
- tombol edit memuat modal edit via AJAX

## Data dan Relasi Utama
Field product yang dipakai aktif pada fitur ini:
- `main_category_id`
- `sub_category_id`
- `product_type_id`
- `brand_id`
- `specification_id`
- `packaging_id`
- `supplier_id`
- `unit_id`
- `sku`
- `qty`
- `harga_rata_rata`
- `harga_tertinggi`
- `note`

Relasi yang dipakai oleh form, datatable, dan detail modal:
- `main_category`
- `sub_category`
- `product_type`
- `brand`
- `specification`
- `packaging`
- `supplier`
- `unit`

## Alur Halaman
### List Product
Halaman list memakai `ProductDataTable` dan menampilkan kolom:
- `SKU`
- `Product`
- `Supplier`
- `Brand`
- `Stock`
- `Harga Rata - Rata`
- `Harga Tertinggi`
- `Updated`
- `Action`

Action button aktif saat ini:
- detail
- edit

Catatan:
- method `destroy()` tersedia di controller,
- tetapi tombol delete tidak dirender pada action button aktif saat ini.

### Create Product
Modal create dimuat dari `product.list.create` melalui AJAX.

Field utama:
- `main_category`
- `sub_category`
- `product_type`
- `brand`
- `specification`
- `packaging`
- `supplier`
- `unit`
- `note`

Perilaku form:
- `sub_category`, `product_type`, `brand`, `specification`, `packaging`, dan `supplier` disabled sebelum `main_category` dipilih
- setelah `main_category` berubah, form memanggil `product.fetchMainCategory`
- response endpoint dipakai untuk mengisi ulang dropdown relasi dependen

### Edit Product
Modal edit dimuat dari `product.list.edit` melalui AJAX.

Perilakunya sama dengan create, tetapi:
- dropdown dependen diisi ulang berdasarkan `main_category` product aktif,
- option lama dipilih ulang sesuai nilai product.

### Detail Product
Detail modal mengambil data dari `product.list.show` dalam bentuk JSON.

Data yang ditampilkan:
- nama relasi kategori/product
- supplier
- stock + unit
- `harga_rata_rata`
- `harga_tertinggi`
- `note`

## Generator SKU
SKU dibentuk saat create dan update melalui helper private `buildSku(...)` di `ProductController`.

Struktur SKU saat ini:
- `main_category_id`
- `sub_category.code`
- `brand.code`
- `product_type.code`
- `specification.code`
- `packaging.code`
- `supplier.code`

Catatan penting:
- segmen pertama memakai **ID** main category dari request, bukan `main_categories.code`
- jika code pada master relasi berubah, SKU akan ikut berubah saat product di-update
- halaman list memang menampilkan panel informasi SKU dengan struktur dan contoh SKU yang sama

## Endpoint Pendukung
### `fetchMainCategory`
Endpoint ini menerima `main_category` lalu mengembalikan JSON:
- `sub_category`
- `specification`
- `packaging`
- `supplier`
- `product_type`
- `brand`

Fungsi utamanya:
- mengisi dropdown dependen pada modal create
- mengisi dan memilih ulang dropdown dependen pada modal edit

### `getProductOptions`
Endpoint ini menghasilkan `<option>` HTML, bukan JSON.

Perilaku aktif:
- menerima `selected` tunggal atau array
- dapat difilter melalui `request()->filter`
- mengirim atribut:
  - `data-id`
  - `data-sku`
  - `data-unit-id`
  - `data-unit-name`

Endpoint ini dipakai sebagai sumber opsi SKU product untuk modul lain.

## Catatan Penting
- Controller masih memakai `Request` langsung; belum ada FormRequest khusus untuk create/update Product
- `show()` membangun response detail dengan query per relasi, bukan eager loading tunggal
- `skuFormat()` pada model menampilkan format:
  - `sku | product type | specification | packaging`
- `qty`, `harga_rata_rata`, dan `harga_tertinggi` tampil di list dan detail, tetapi tidak diinput manual pada form create/edit
- Modul Product berperan sebagai master data dan sumber opsi product untuk fitur lain, termasuk endpoint `options.product`
