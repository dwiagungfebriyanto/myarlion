# Job - Edit Amount Approval

## Overview
Modul **Edit Amount Approval** dipakai untuk meminta perubahan `amount` / PI Amount pada Job yang sudah ada, dengan alur approval terpisah sebelum nilai `jobs.amount` benar-benar diubah. Form edit Job biasa tidak pernah menjadi jalur perubahan langsung untuk field ini.

Tujuan utama modul:
- menahan perubahan amount langsung dari user biasa,
- menyimpan histori request perubahan amount,
- memastikan hanya ada **satu request berstatus `waiting` per job** pada satu waktu,
- memberi halaman review untuk approve atau reject request.

## Alur Singkat
### 1. Submit request
1. User membuka halaman edit Job.
2. Field `amount` pada form edit Job hanya tampil sebagai nilai referensi dan selalu `readonly`.
3. User klik **Request Edit Amount**.
4. Frontend submit ke `POST /api/jobs/{job}/request-edit-amount`.
5. `JobEditAmountApprovalController@store()`:
   - validasi `request_amount` dan `note`,
   - lock row job dengan transaction,
   - cek apakah job masih punya request `waiting`,
   - jika masih ada, kembalikan `422`,
   - jika tidak ada, simpan request baru ke `job_edit_amount_approvals`.
6. Endpoint `PUT/PATCH job/list/{job}` (`job.list.update`) tidak mengubah `amount`, walaupun payload `amount` ikut terkirim dari client.

### 2. Review request
1. Reviewer membuka halaman **Edit Amount Approval**.
2. Datatable menampilkan daftar request beserta status.
3. Reviewer mengubah status ke `approved` atau `rejected`.
4. `JobEditAmountApprovalController@changeStatus()` menyimpan status dan reviewer.
5. Jika status `approved`, `jobs.amount` diupdate memakai `request_amount`.

## Route Utama
| Method | URI | Route Name | Fungsi |
| :--- | :--- | :--- | :--- |
| GET | `edit-amount-approval` | `job.edit_amount_approval.index` | Halaman list approval |
| PUT | `edit-amount-approval/{editRequest}/change-status` | `edit_amount_approval.change_status` | Approve/reject request dan menerapkan perubahan `jobs.amount` saat status `approved` |
| POST | `api/jobs/{job}/request-edit-amount` | `api.edit_amount_approval.store` | Membuat request edit amount |
| PUT/PATCH | `job/list/{job}` | `job.list.update` | Update data utama Job selain `amount`; payload `amount` diabaikan |

## Tabel & Relasi
### `job_edit_amount_approvals`
Kolom utama yang dipakai modul:
- `job_id`
- `old_amount`
- `request_amount`
- `note`
- `status` dengan nilai `waiting`, `approved`, `rejected`
- `requester_id`
- `reviewer_id`
- `created_at`

Relasi:
- `job_edit_amount_approvals.job_id -> jobs.id`
- `job_edit_amount_approvals.requester_id -> users.id`
- `job_edit_amount_approvals.reviewer_id -> users.id`

Aturan data:
- satu job boleh punya banyak histori request,
- job tidak boleh punya lebih dari satu request `waiting` aktif,
- unique index lama pada `job_id` harus sudah dilepas agar histori request bisa tersimpan.

## Perilaku UI
### Halaman edit Job
- field `amount` selalu `readonly`,
- tombol **Request Edit Amount** hanya tampil jika user punya permission yang sesuai dan tidak ada request `waiting`,
- pesan status request tampil hanya saat request aktif masih `waiting`.

Kontrak perilaku:
- `job.list.update` tidak mengubah `amount`,
- `api.edit_amount_approval.store` hanya membuat request,
- `edit_amount_approval.change_status` menjadi satu-satunya jalur penerapan perubahan nilai final `jobs.amount`.

### Halaman approval
- datatable mendukung search untuk:
  - `jobs.code`,
  - `customers.name`,
  - `users.name` pada kolom **Marketing**,
- kolom `Requested by` dan `Reviewed by` tetap tampil, tetapi tidak masuk scope global search.

## File Kunci
- `app/Http/Controllers/JobEditAmountApprovalController.php`
- `app/DataTables/JobEditAmountApprovalDataTable.php`
- `app/Models/JobEditAmountApproval.php`
- `app/Models/Job.php`
- `resources/views/pages/job/edit/tabs/edit-job.blade.php`
- `resources/views/pages/job/edit/components/modal-request-edit-amount.blade.php`
- `resources/views/pages/job_edit_amount_approval/index.blade.php`

## Test Checklist
1. User bisa membuat request pertama untuk satu job.
2. User tidak bisa membuat request kedua jika request sebelumnya masih `waiting`.
3. User bisa membuat request baru setelah request sebelumnya `approved`.
4. User bisa membuat request baru setelah request sebelumnya `rejected`.
5. Approve request mengubah `jobs.amount` ke `request_amount`.
6. Update Job biasa tidak mengubah `jobs.amount` walaupun payload `amount` dikirim.
7. Halaman edit Job selalu menampilkan field `amount` sebagai `readonly`.
8. Datatable approval bisa search berdasarkan Job ID, nama customer, dan nama marketing.
