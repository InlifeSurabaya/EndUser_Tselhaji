# 🧩 Project Rules — Laravel + Livewire

Dokumen ini berisi **aturan pengembangan proyek** untuk menjaga konsistensi struktur, penamaan, dan alur kerja Git di seluruh tim.

---

## ⚙️ 1. Struktur dan Penamaan File

### 📂 Livewire Components
Semua komponen Livewire harus berada di dalam folder:


app/Http/Livewire/


### 📄 Penamaan File & Class
Gunakan format **PascalCase** tanpa spasi atau tanda hubung.

#### ✅ Contoh Benar:
- `CreateBook.php`
- `EditUserProfile.php`
- `ListTransactions.php`

#### ❌ Contoh Salah:
- `create_book.php`
- `createbook.php`
- `Create_book.php`

## 🧠 2. Pembuatan Controller / Component Baru

Ketika membuat komponen baru dengan Livewire, wajib menggunakan perintah:

```bash
php artisan make:livewire book.create-book
````

> ⚠️ Pastikan nama mengikuti format **PascalCase** seperti contoh di atas.

---

## 🧩 3. Penulisan Method dalam Komponen

### 📘 Aturan:

Setiap method publik (`public function`) wajib diberi komentar **docblock** sebelum deklarasi method.

Gunakan format berikut:

```php
/**
 * The attributes that are mass assignable.
 *
 * @var list<string>
 */
public function saveBook()
{
    // ...
}
```

### ✅ Contoh Lengkap:

```php
class CreateBook extends Component
{
    public $title;
    public $author;

    /**
     * Method ini untuk menyimpan buku.
     *
     * @var list<string>
     */
    public function saveBook()
    {
        // logic untuk menyimpan buku
    }
}
```

---

## 🌿 4. Aturan Branching (Git Workflow)

### 🔹 Branch Utama:

* `main` → Branch produksi (deployment)
* `dev` → Branch pengembangan utama

### 🔹 Membuat Branch Baru:

Setiap fitur, perbaikan bug, atau chore harus dibuat **dari branch `dev`**.

Gunakan format penamaan berikut:

```
nama/tipe/namafitur
```

#### Tipe Branch:

| Tipe  | Keterangan                                                    |
| ----- | ------------------------------------------------------------- |
| feat  | Menambahkan fitur baru                                        |
| fix   | Memperbaiki bug                                               |
| chore | Perubahan kecil/non-fungsional (misal: refactor, dokumentasi) |

#### ✅ Contoh:

```bash
# Dari branch dev
git checkout dev

# Buat branch baru
git checkout -b raihan/feat/create-book
```

---

## 🧾 5. Commit Message Convention

Gunakan format **conventional commits** untuk memudahkan tracking perubahan.

```
<type>(<scope>): <short summary>
```

#### Contoh:

```
feat(book): add Livewire component for book creation
fix(auth): resolve session logout issue
chore(ui): improve form layout styling
```

---

## 🧰 6. Deployment dan Merge Rules

1. Semua merge ke `dev` harus melalui **Pull Request (PR)**.
2. PR harus direview minimal oleh **1 developer lain**.
3. Merge ke `main` hanya boleh dilakukan oleh maintainer.
4. Jangan merge langsung ke `main` tanpa testing di `dev`.

---

## ✨ 7. Standar Kode

* Gunakan **PSR-12** untuk format kode PHP.
* Setiap file PHP wajib diawali dengan deklarasi `<?php`.
* Tidak boleh ada spasi atau baris kosong berlebih.
* Gunakan komentar hanya bila perlu untuk menjelaskan logika kompleks.

---

## 📦 8. Tambahan

* Setiap modul wajib punya file Livewire dan Blade view dengan nama yang sama.
  Contoh:

  ```
  app/Http/Livewire/CreateBook.php
  resources/views/livewire/create-book.blade.php
  ```
* Hindari logika bisnis berat di Blade — simpan di komponen Livewire.
* Selalu jalankan `php artisan optimize:clear` setelah menambah file baru.

---

## 🧽 9. Format Kode dan Pint Check
### 📌 Aturan Wajib Sebelum Commit:

Sebelum melakukan commit, **wajib menjalankan perintah berikut:**

```bash
./vendor/bin/pint
```


> `php pint` digunakan untuk memastikan semua file mengikuti standar format kode Laravel (PSR-12).
> Commit **tidak boleh dilakukan** sebelum menjalankan perintah ini.

### ✅ Workflow Contoh:

```bash
# Setelah selesai coding
./vendor/bin/pint

# Pastikan tidak ada error atau file yang belum diformat
git add app/Http/Livewire/CreateBook.php
git commit -m "feat(book): add Livewire component for book creation"

```
📘 **Catatan Akhir**

> Tujuan dari peraturan ini adalah menjaga proyek tetap bersih, konsisten, dan mudah dikelola oleh seluruh tim.
> Selalu pastikan:
> * Menjalankan `./vendor/bin/pint` sebelum commit
> * Membuat PR ke `dev` saja
> * Tidak pernah merge langsung ke `main` atau `master`


