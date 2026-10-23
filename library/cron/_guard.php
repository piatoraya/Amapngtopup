<?php
/* ShennBoku Cron Guard
 * ------------------------------------------------------------------
 * Wajib di-include di baris paling atas setiap file cron yang dapat
 * dipanggil lewat web (status-*, refund-*, delete_akun, get/*, benefit/*, dll).
 *
 * Aturan:
 *   - Dijalankan dari CLI (cron server / php-cli)  -> selalu diizinkan.
 *   - Diakses via web (browser/wget/curl)          -> wajib ?key=<SHENN_CRON_KEY>.
 *
 * Contoh: https://domain.com/library/cron/status-ppob?key=xxxxxxxx
 * File webhook (callback-atlantic.php) TIDAK memakai guard ini.
 */
require __DIR__.'/_key.php';

if(PHP_SAPI !== 'cli') {
    $shenn_cron_key = isset($_GET['key']) ? (string) $_GET['key'] : '';
    if($shenn_cron_key === '' || !hash_equals(SHENN_CRON_KEY, $shenn_cron_key)) {
        http_response_code(403);
        exit('Forbidden');
    }
}
