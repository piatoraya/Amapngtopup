<?php
/* ShennBoku Cron Secret Key
 * ------------------------------------------------------------------
 * Secret ini dipakai untuk dua hal:
 *   1. library/cron/_guard.php  -> memblokir akses web tanpa ?key=
 *   2. admin/server/auto-status-and-refund.php -> menyisipkan ?key= pada link "CHECK"
 *
 * GANTI nilai di bawah dengan string acak milik Anda sendiri (min. 32 karakter),
 * lalu tambahkan ?key=<nilai> pada SEMUA url cron di cPanel/external cron.
 */
if(!defined('SHENN_CRON_KEY')) define('SHENN_CRON_KEY', 'e6f1c9d2b7a84f0e9c3d5a1b8f72e4c6a0d9b3f5c8e1a7d2b4f6093c5e8a1d7b');
