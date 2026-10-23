# Menjalankan ShennBoku Secara Lokal (XAMPP / Laragon)

Panduan ini menjelaskan cara menjalankan panel di komputer sendiri, tanpa
menyentuh server produksi. Semua langkah di bawah sudah diperhitungkan oleh
kode yang ada di repository ini.

---

## 0. Kenapa tidak bisa "tinggal jalan"?

Ada **3 halangan**, dan semuanya sudah disiapkan solusinya:

| # | Halangan | Solusi |
|---|---|---|
| 1 | Tidak ada file `.sql` bawaan / skrip instalasi. Aplikasi butuh database berisi **28 tabel** untuk hidup. | Anda sudah punya export produksi — dipakai langsung (langkah 3). |
| 2 | `.htaccess` produksi memakai **handler LiteSpeed** milik hosting mereka dan **memaksa HTTPS**. Di lokal, file `.php` justru terdownload sebagai teks, dan redirect-nya loop tak berujung. | Gunakan `.htaccess.local` (langkah 4). |
| 3 | Cookie login diberi flag `Secure`. Browser membuangnya saat HTTP → login sukses tapi selalu kembali ke halaman login. | Sudah diperbaiki di kode: `shenn_is_https()` di `connect.php` menyesuaikan otomatis. |

---

## 1. Pasang PHP 7.4 (WAJIB, jangan PHP 8.1+)

Gunakan **PHP 7.4** agar sama persis dengan produksi (`.htaccess` produksi
memaksa handler `alt-php74`).

> **Penting — jangan pakai PHP 8.1 ke atas.**
> Mulai PHP 8.1, `mysqli` secara bawaan melempar *exception* saat query gagal
> (`MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT`). Seluruh kode di sini
> bergantung pada `$call->query()` yang **mengembalikan `false`** — misalnya
> `if($call->query(...) == true)`, `$q = $call->query(...)` lalu `->num_rows`.
> Di PHP 8.1+, query yang gagal akan berhenti eksekusi (fatal) bukan sekadar
> gagal diam-diam. Bisa diperbaiki nanti dengan `mysqli_report(MYSQLI_REPORT_OFF)`,
> tapi untuk sekarang hindari dulu.

**PHP 8.0** masih relatif aman karena perubahan mysqli itu baru di 8.1, tapi
tetap tidak disarankan.

**Ekstensi yang dibutuhkan** (semua sudah aktif di XAMPP/Laragon bawaan):

| Ekstensi | Untuk | Cek |
|---|---|---|
| `mysqli` | koneksi database | wajib |
| `curl` | semua panggilan API (17 pemakaian) | wajib |
| `openssl` | HTTPS + `random_bytes` | wajib |
| `mbstring` | manipulasi string | wajib |
| `json`, `session` | bawaan PHP | sudah |

Tidak diperlukan: GD, fileinfo, iconv, zip, bcmath, intl.
(`getimagesize()` milik ekstensi *standard*, bukan GD.)

Cek dari terminal:
```bash
php -v
php -m | findstr /i "mysqli curl openssl mbstring"    # Windows
php -m | grep -Ei "mysqli|curl|openssl|mbstring"      # Linux/macOS
```

---

## 2. Letakkan project & buat VirtualHost

### Kenapa harus VirtualHost?

`connect.php` menentukan lokasi file aplikasi lewat fungsi `_DIR_()` yang
memakai `$_SERVER['DOCUMENT_ROOT']`. Kalau project ditaruh begitu saja di
`C:\xampp\htdocs\shennboku`, maka `DOCUMENT_ROOT` = `C:\xampp\htdocs`
(bukan folder project), sehingga pencarian `library/mainfunction.php` bisa
meleset.

> **Sudah saya perbaiki.** `_DIR_()` kini memverifikasi apakah
> `DOCUMENT_ROOT/connect.php` benar-benar ada; kalau tidak, otomatis jatuh ke
> folder tempat `connect.php` berada. Jadi project yang ditaruh di sub-folder
> tetap jalan. VirtualHost tetap disarankan karena lebih bersih.

### XAMPP (Windows)

1. Salin seluruh folder project ke `C:\xampp\htdocs\shennboku`
2. Buka `C:\xampp\apache\conf\extra\httpd-vhosts.conf`, tambahkan:
   ```apache
   <VirtualHost *:80>
       ServerName shennboku.local
       DocumentRoot "C:/xampp/htdocs/shennboku"
       <Directory "C:/xampp/htdocs/shennboku">
           Options -Indexes +FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```
   (`AllowOverride All` **wajib** supaya `.htaccess` terbaca.)
3. Buka `C:\Windows\System32\drivers\etc\hosts` dengan Notepad (Run as admin), tambahkan:
   ```
   127.0.0.1   shennboku.local
   ```
4. Pastikan `mod_rewrite` aktif di `httpd.conf` (`LoadModule rewrite_module ...` — hapus tanda `#` di depannya).
5. Restart Apache.

### Laragon (Windows) — jauh lebih mudah

1. Salin folder project ke `C:\laragon\www\shennboku`
2. Buka Laragon → **Menu → Apache → httpd-vhosts.conf** (biasanya Laragon sudah membuatnya otomatis untuk folder di `www`)
3. Klik **Reload**. Laragon otomatis membuat domain `http://shennboku.test` dengan `DocumentRoot` tepat ke folder project dan `AllowOverride All`.

### Linux/macOS

```bash
# taruh project, lalu buat vhost (Apache 2.4)
sudo nano /etc/apache2/sites-available/shennboku.conf
```
```apache
<VirtualHost *:80>
    ServerName shennboku.local
    DocumentRoot "/var/www/shennboku"
    <Directory "/var/www/shennboku">
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```
```bash
sudo a2ensite shennboku.conf
sudo a2enmod rewrite
echo "127.0.0.1 shennboku.local" | sudo tee -a /etc/hosts
sudo systemctl restart apache2
```

---

## 3. Import database produksi

1. Buka **phpMyAdmin** → tab **Import** → pilih file `.sql` hasil export produksi.
2. Buat database baru dulu, misalnya `shennboku` (sesuaikan dengan `library/config.local.php`).
3. Pastikan kolom **Format** = *SQL* dan **Format of inserts** = *INSERT*.
4. Jalankan.

Catatan:
- Export phpMyAdmin bawaan struktur+data sudah cukup.
- Baris di tabel `conf` yang menyimpan path lama (`/home/ampangpe/public_html`)
  **tidak masalah** — `_DIR_()` akan menimpanya otomatis pada request pertama.

**Sekali saja (disarankan)** — tambah index untuk modul anti-brute-force:
```sql
ALTER TABLE `logs` ADD INDEX `idx_rl` (`type`,`date`,`ip`);
```

---

## 4. Aktifkan `.htaccess` versi lokal

```bash
# di folder project
cp .htaccess .htaccess.produksi.asli   # cadangan
cp .htaccess.local .htaccess           # aktifkan yang lokal
```

`.htaccess.local` adalah salinan persis `.htaccess` produksi dengan **4 perubahan saja**:

| Perubahan | Alasan |
|---|---|
| Blok `SetHandler application/x-httpd-alt-php74___lsphp` dihapus | Nama handler itu milik LiteSpeed hosting mereka. Di Apache lokal tidak dikenal → file `.php` jadi terdownload, bukan dieksekusi. |
| `php_value session.auto_start 1` dimatikan | `connect.php` memanggil `session_start()` sendiri, dan `auto_start` membuat `session.cookie_secure` mustahil diatur ulang dari kode. |
| Force HTTPS dimatikan | Di `http://localhost` akan loop redirect tak berujung. |
| `session.cookie_secure` → `0` | Kalau `1`, browser membuang `PHPSESSID` saat HTTP → selalu kembali ke login. |

Proteksi lain tetap 100% sama: URL rewrite, blokir `connect.php`/`_key.php`/`.log`/`.sql`, dan seluruh aturan WAF.

> **Jangan pernah** mengunggah `.htaccess.local` atau `.htaccess.produksi.asli` ke produksi.
> Keduanya sudah masuk `.gitignore`.

---

## 5. Isi `library/config.local.php`

Sudah saya buatkan dengan default XAMPP/Laragon:

```php
return [
    'db_host' => '127.0.0.1',
    'db_user' => 'root',
    'db_pass' => '',
    'db_name' => 'shennboku',
    'wapi_key' => '',
    'lann_api_key' => '',
];
```

Sesuaikan `db_name` dengan nama database yang Anda buat di langkah 3.
Kosongkan `wapi_key` dan `lann_api_key` — aplikasi tetap jalan, hanya fitur
WhatsApp yang tidak aktif.

---

## 6. Verifikasi

Buka `http://shennboku.local` (atau `http://localhost/shennboku` bila tanpa vhost).

| URL | Yang dicek |
|---|---|
| `http://shennboku.local/` | halaman utama tampil, bukan `server-error.php` |
| `http://shennboku.local/auth/login` | halaman login |
| `http://shennboku.local/api/profile` | harus balas JSON `"API Key not registered."` → berarti API hidup |
| `http://shennboku.local/connect.php` | harus **403** (diblokir `.htaccess`) |
| `http://shennboku.local/library/config.local.php` | harus **403** |

Login pakai akun admin yang sudah ada di dump produksi.

Kalau muncul halaman error: lihat `shenn.log` di folder project (koneksi DB gagal
akan ditulis di sana), dan `library/error_log` / `account/error_log`.

---

## 7. Fitur yang tidak aktif lokal

Semua remote API — kalau key kosong, fitur gagal tapi aplikasi tetap hidup
(karena `error_reporting(0)`):

| Layanan | Untuk | Akibat lokal |
|---|---|---|
| WapiSender | OTP & notifikasi WhatsApp | Kode OTP tidak terkirim |
| Atlantic Group | QRIS / Virtual Account | Deposit otomatis tidak jalan |
| Firebase FCM | Notifikasi browser | Notifikasi tidak terkirim |
| SMTP mailer | Email | Email tidak terkirim |
| reCAPTCHA | Proteksi form | Cukup isi key di admin panel → `conf` |
| SimCardDetector | Deteksi operator | Deteksi operator kosong |

Untuk testing alur transaksi, buat langsung data via admin panel, atau set
saldo user lewat SQL:
```sql
UPDATE `users` SET `balance` = 1000000 WHERE `username` = 'USERNAME_ANDA';
```

---

## 8. Cron (opsional untuk lokal)

File cron ada di `library/cron/`. Dari baris perintah **tidak butuh key** —
`_guard.php` melewati proteksi saat `PHP_SAPI === 'cli'`:

```bash
php library/cron/status-ppob.php
php library/cron/status-deposit--.php
```

Kalau diakses lewat browser tetap butuh `?key=<SHENN_CRON_KEY>` (lihat
`library/cron/_key.php`).

---

## 9. Troubleshooting

| Gejala | Penyebab | Solusi |
|---|---|---|
| File `.php` terdownload / tampil sebagai teks | `.htaccess` produksi masih aktif (handler LiteSpeed) | Pakai `.htaccess.local` |
| Browser bilang "too many redirects" | Force HTTPS masih aktif | Pakai `.htaccess.local` |
| Login sukses tapi langsung logout | `cookie_secure=1` saat HTTP | Pakai `.htaccess.local`; kode sudah otomatis menyesuaikan |
| Halaman merah muda / `server-error.php` | Koneksi database gagal | Cek `library/config.local.php` dan `shenn.log` |
| `Fatal error: ... mainfunction.php` | `DOCUMENT_ROOT` meleset | Pakai VirtualHost (langkah 2) |
| 403 pada semua halaman | Baris `RewriteCond %{HTTP_USER_AGENT} ... python` di WAF memblokir | Pakai `curl` (aman), atau nonaktifkan blok UA di `.htaccess` lokal |
| CSS/JS 404 | Folder `app/` atau `library/assets/` belum ikut tersalin | Pastikan seluruh folder ikut disalin |
| Gambar QRIS tidak tampil | `mod_rewrite` belum aktif | Aktifkan `rewrite_module` lalu restart Apache |

---

## 10. Saat pindah ke produksi — checklist

Jangan terlewat:

- [ ] Kembalikan `.htaccess` produksi (bukan `.htaccess.local`)
- [ ] Isi `library/config.local.php` dengan kredensial **asli hasil rotasi**
- [ ] Rotasi password database asli + key WapiSender (yang lama masih ada sebagai fallback)
- [ ] Ganti `SHENN_CRON_KEY` di `library/cron/_key.php`, lalu tambahkan `?key=` ke semua URL cron eksternal
- [ ] Hapus file log yang berserakan (`error_log`, `shenn.log`) — sudah masuk `.gitignore`
- [ ] Jalankan `ALTER TABLE logs ADD INDEX idx_rl (type,date,ip);`
- [ ] Jalankan `php -l` untuk semua file `.php` sebelum upload
- [ ] Pastikan `library/config.local.php` dan `.htaccess.local` **tidak ikut** terunggah
