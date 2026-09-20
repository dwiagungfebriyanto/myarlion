# Account/User Feature

## Overview Fitur
Fitur **Account/User** dipakai untuk mengelola akun pengguna internal aplikasi. Entry point utamanya ada di `app/Http/Controllers/Auth/UserController.php` dan route resource `account`.

Ruang lingkup modul saat ini mencakup:
- daftar user berbasis DataTables,
- create user baru,
- edit data user,
- delete user,
- upload atau update signature user,
- edit permission per user di luar permission bawaan role.

Dependensi utama implementasi:
- `App\Models\User` sebagai model utama akun,
- `App\Models\Role` untuk role utama user,
- `App\Models\Permission` untuk permission granular,
- package Spatie `HasRoles` untuk role dan permission,
- Spatie Activity Log untuk pencatatan perubahan tertentu,
- `UsersDataTable` untuk list user.

Perilaku implementasi saat ini:
- route modul `account` hanya dibungkus middleware `auth`,
- pembatasan tombol aksi banyak dilakukan di level UI memakai `@can`, `@hasrole`, dan pengecekan user aktif,
- setelah create atau update data user, alur selalu diarahkan ke halaman upload signature,
- upload signature tidak mengembalikan redirect dari controller; redirect ditangani script frontend,
- permission user tambahan disimpan terpisah dari role permission melalui endpoint AJAX.

## Alur End-to-End
### 1) Menampilkan daftar user
1. User membuka `GET /account` (`account.index`).
2. Request masuk ke `UserController@index(UsersDataTable $dataTable)`.
3. `UsersDataTable` mengambil data `users` yang di-join ke `roles`.
4. View `resources/views/pages/account/index.blade.php` menampilkan:
   - tombol **Add New** jika user punya permission `add account`,
   - DataTable daftar akun,
   - modal preview signature,
   - modal edit permission.
5. Kolom aksi dan signature dirender dari partial Blade:
   - `resources/views/pages/account/components/action-button.blade.php`,
   - `resources/views/pages/account/components/show-button.blade.php`.

### 2) Create user
1. User membuka `GET /account/create` (`account.create`).
2. `UserController@create()` mengambil semua data role.
3. View `resources/views/pages/account/create.blade.php` menampilkan form field:
   - `name`,
   - `position`,
   - `username`,
   - `role`,
   - `email`,
   - `password`,
   - `password_confirmation`.
4. Submit form dikirim ke `POST /account` (`account.store`).
5. `UserController@store()` melakukan validasi, membuat record baru di tabel `users`, lalu:
   - menyimpan `role_id`,
   - hash password dengan `Hash::make`,
   - assign role Spatie berdasarkan nama role yang dipilih.
6. Jika berhasil, session flash `success` diisi dan user diarahkan ke `GET /account/create/{id}/signature` (`account.createSign`).
7. View `resources/views/pages/account/create-signature.blade.php` membuka form Dropzone untuk upload signature.
8. User dapat:
   - upload satu file signature lalu klik **Submit All**,
   - atau langsung klik **Submit All** tanpa upload file untuk menyelesaikan create tanpa signature.
9. Frontend melakukan redirect ke list account dengan query:
   - `?success` jika submit dengan signature,
   - `?successNotSign` jika submit tanpa signature.

### 3) Upload signature saat create
1. Form upload signature dikirim ke `PUT /account/store/{id}/signature` (`account.uploadSign`).
2. `UserController@uploadSign()` memvalidasi file image.
3. File disimpan ke folder publik `public/images/signature_photo`.
4. Nama file dibentuk dengan format `{username}-{timestamp}.{extension}`.
5. Kolom `users.signature` diupdate dengan nama file tersebut.

Catatan implementasi:
- controller method ini tidak mengembalikan response body atau redirect,
- redirect ke halaman list dilakukan dari JavaScript pada view signature.

### 4) Edit user
1. User membuka `GET /account/{account}/edit` (`account.edit`).
2. Tombol edit pada list hanya tampil bila:
   - user login punya role `administrator`, atau
   - user login punya permission `edit account` dan sedang mengedit akun miliknya sendiri.
3. `UserController@edit($id)` mengambil data user dan seluruh role.
4. View `resources/views/pages/account/edit.blade.php` menampilkan form edit field utama dan preview signature saat ini.
5. Submit form dikirim ke `PUT /account/{account}` (`account.update`).
6. `UserController@update()`:
   - memvalidasi perubahan data,
   - memperbarui field utama user,
   - mengganti password hanya jika field password diisi,
   - melakukan `syncRoles($request->role)`,
   - mencatat activity `password_changed` jika password diperbarui.
7. Setelah update berhasil, user diarahkan ke `GET /account/edit/{id}/signature` (`account.editSign`) untuk upload atau update signature.
8. Frontend di halaman signature mengarahkan kembali ke list account dengan query:
   - `?updated` jika upload signature,
   - `?updatedNotSign` jika tidak upload signature.

### 5) Edit permission per user
1. Fitur ini hanya ditampilkan untuk user dengan role `administrator`.
2. Dari list account, admin klik tombol **Edit Permission**.
3. Frontend memanggil `GET /account/permission/{user}/edit` (`account.edit_permission`) via AJAX.
4. `UserController@editPermission(User $user)`:
   - mengambil seluruh permission,
   - menandai permission yang sudah dimiliki user,
   - menyusun HTML checkbox,
   - mengembalikan JSON berisi URL update, data user, dan HTML opsi permission.
5. Admin submit modal permission ke `PUT /account/permission/{user}/update` (`account.update_permission`) via AJAX.
6. `UserController@updatePermission()`:
   - memvalidasi setiap `permissions.*`,
   - menyimpan snapshot permission lama,
   - mencabut seluruh direct permission user saat ini,
   - memberikan direct permission baru berdasarkan request,
   - mencatat activity `user_permission_changed`.

Catatan:
- fitur ini mengelola **direct permission** user, bukan role permission,
- user tetap mewarisi permission dari role walaupun direct permission diubah.

### 6) Delete user
1. Tombol delete hanya tampil untuk user dengan role `administrator`.
2. Frontend menampilkan konfirmasi SweetAlert.
3. Jika dikonfirmasi, form dikirim ke `DELETE /account/{account}` (`account.destroy`).
4. `UserController@destroy($id)` menghapus record user lalu redirect ke list account dengan flash message sukses.

## Daftar Route & Endpoint
### A. Route inti modul account (`routes/web.php`)
| Method | URI | Route Name | Controller@Method | Fungsi |
| :--- | :--- | :--- | :--- | :--- |
| GET | `account` | `account.index` | `UserController@index` | Menampilkan daftar user |
| GET | `account/create` | `account.create` | `UserController@create` | Menampilkan form create user |
| POST | `account` | `account.store` | `UserController@store` | Menyimpan user baru |
| GET | `account/{account}/edit` | `account.edit` | `UserController@edit` | Menampilkan form edit user |
| PUT/PATCH | `account/{account}` | `account.update` | `UserController@update` | Memperbarui data user |
| DELETE | `account/{account}` | `account.destroy` | `UserController@destroy` | Menghapus user |
| GET | `account/create/{id}/signature` | `account.createSign` | `UserController@createSign` | Halaman upload signature setelah create |
| GET | `account/edit/{id}/signature` | `account.editSign` | `UserController@editSign` | Halaman upload signature setelah update |
| PUT | `account/store/{id}/signature` | `account.uploadSign` | `UserController@uploadSign` | Upload atau update file signature |
| GET | `account/permission/{user}/edit` | `account.edit_permission` | `UserController@editPermission` | Mengambil data modal permission user |
| PUT | `account/permission/{user}/update` | `account.update_permission` | `UserController@updatePermission` | Menyimpan direct permission user |

### B. Middleware route
Semua route modul ini berada dalam group:

```php
Route::middleware(['auth'])->group(function () {
    Route::resource('account', UserController::class);
    Route::get('account/create/{id}/signature', [UserController::class, 'createSign'])->name('account.createSign');
    Route::get('account/edit/{id}/signature', [UserController::class, 'editSign'])->name('account.editSign');
    Route::put('account/store/{id}/signature', [UserController::class, 'uploadSign'])->name('account.uploadSign');
    Route::get('account/permission/{user}/edit', [UserController::class, 'editPermission'])->name('account.edit_permission');
    Route::put('account/permission/{user}/update', [UserController::class, 'updatePermission'])->name('account.update_permission');
});
```

Artinya:
- akses minimal membutuhkan user login,
- tidak ada middleware `role` atau `permission` khusus di level route untuk create, edit, delete, maupun edit permission,
- pembatasan aksi tambahan diimplementasikan terutama di layer tampilan.

## Validasi Request
### 1) Validasi create user (`store`)
| Field | Rules |
| :--- | :--- |
| `name` | `required|string|max:255` |
| `position` | `required|string|max:255` |
| `username` | `required|string|max:255|unique:users` |
| `email` | `required|string|email|max:255|unique:users` |
| `role` | `required` |
| `password` | `required|confirmed|min:8 + letters + mixedCase + numbers` |

### 2) Validasi update user (`update`)
| Field | Rules |
| :--- | :--- |
| `name` | `required|string|max:255` |
| `position` | `required|string|max:255` |
| `username` | `required|string|max:255|unique:users,username,{id}` |
| `email` | `required|string|email|max:255|unique:users,email,{id}` |
| `role` | `required` |
| `password` | `nullable|confirmed|min:8 + letters + mixedCase + numbers` |

### 3) Validasi upload signature (`uploadSign`)
| Field | Rules |
| :--- | :--- |
| `file` | `required|image|mimes:jpeg,png,jpg,gif,svg|max:2048` |

### 4) Validasi update permission (`updatePermission`)
| Field | Rules |
| :--- | :--- |
| `permissions.*` | `exists:App\Models\Permission,id|nullable` |

## Struktur Data & Relasi
### 1) Tabel `users`
Tabel utama untuk akun internal.

Kolom yang dipakai modul:
- `id`
- `name`
- `position`
- `role_id`
- `username`
- `email`
- `password`
- `signature`
- `remember_token`
- `created_at`
- `updated_at`

Catatan implementasi:
- `role_id` disimpan langsung di tabel `users`,
- pada saat yang sama, role juga dikelola melalui package Spatie dengan `assignRole()` dan `syncRoles()`,
- ini berarti modul memakai dua representasi role: `users.role_id` dan tabel pivot Spatie.

### 2) Tabel `roles`
Dipakai untuk dropdown role dan join list user.

Kolom yang dipakai:
- `id`
- `name`
- `guard_name`

Seed role awal:
- `administrator`
- `accounting`
- `marketing`
- `quality_control`

### 3) Tabel `permissions`
Dipakai untuk direct permission per user dan permission global per modul.

Contoh permission yang relevan untuk modul account:
- `view account`
- `add account`
- `edit account`
- `delete account`

Permission ini dibuat oleh `database/seeders/PermissionSeeder.php`.

### 4) Tabel pivot Spatie
Relasi package Spatie yang relevan:
- `model_has_roles`
- `model_has_permissions`
- `role_has_permissions`

Peran tabel pivot pada modul ini:
- `model_has_roles` menyimpan role aktif user untuk `assignRole()` atau `syncRoles()`,
- `model_has_permissions` menyimpan direct permission tambahan hasil edit permission user,
- `role_has_permissions` menyimpan permission bawaan dari tiap role.

## Komponen Utama
### Controller
- `app/Http/Controllers/Auth/UserController.php`

### Model
- `app/Models/User.php`
- `app/Models/Role.php`
- `app/Models/Permission.php`

### DataTable
- `app/DataTables/UsersDataTable.php`

### Views
- `resources/views/pages/account/index.blade.php`
- `resources/views/pages/account/create.blade.php`
- `resources/views/pages/account/edit.blade.php`
- `resources/views/pages/account/create-signature.blade.php`
- `resources/views/pages/account/edit-signature.blade.php`
- `resources/views/pages/account/components/action-button.blade.php`
- `resources/views/pages/account/components/permission-modal.blade.php`
- `resources/views/pages/account/components/show-button.blade.php`
- `resources/views/pages/account/components/show-signature.blade.php`

## DataTable List User
`UsersDataTable` menampilkan kolom:
- `id`
- `name`
- `username`
- `email`
- `position`
- `role_name`
- `signature`
- `action`

Sumber query:
- `users` join `roles` pada `users.role_id = roles.id`

Custom rendering:
- kolom `signature` memakai tombol untuk preview modal signature,
- kolom `action` memuat tombol edit, edit permission, dan delete sesuai hak akses tampilan.

## Logging & Audit
Audit trail yang ada saat ini:
- model `User` memakai trait `LogsActivity` dan `logFillable()` dengan log name `user`,
- update password di `UserController@update()` mencatat event `password_changed`,
- update direct permission di `UserController@updatePermission()` mencatat event `user_permission_changed` beserta snapshot permission lama dan baru.

## Catatan Implementasi Penting
1. Kontrol akses backend untuk modul account belum sepenuhnya dipagari middleware `permission` atau `role`.
2. Tombol aksi pada list account memang disembunyikan di UI, tetapi endpoint backend tetap berada di bawah middleware `auth` saja.
3. Flow create dan update menganggap upload signature sebagai langkah lanjutan yang opsional.
4. File signature disimpan langsung ke folder publik `public/images/signature_photo`.
5. Method `uploadSign()` saat ini tidak menghapus file signature lama ketika user mengunggah file baru.
6. Method `destroy()` menghapus record user, tetapi tidak memiliki penanganan file signature lama di filesystem.
7. Permission modal bekerja lewat HTML checkbox yang dibangun di controller dan dikembalikan sebagai JSON response.

## Referensi Kode
- controller utama: `app/Http/Controllers/Auth/UserController.php`
- route modul: `routes/web.php`
- query list user: `app/DataTables/UsersDataTable.php`
- model user: `app/Models/User.php`
- seeder permission: `database/seeders/PermissionSeeder.php`
- seeder role: `database/seeders/RoleSeeder.php`
