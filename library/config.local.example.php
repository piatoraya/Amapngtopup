<?php
/**
 * Contoh konfigurasi lokal untuk menyimpan kredensial sensitif.
 *
 * CARA PAKAI:
 *   1. Salin file ini menjadi library/config.local.php
 *   2. Isi nilainya dengan kredensial ASLI yang baru (hasil rotasi)
 *   3. Jangan commit file tersebut ke repository
 *
 * connect.php akan otomatis membaca file ini (jika ada) sebelum nilai default,
 * dan environment variable (SHENN_*) memiliki prioritas tertinggi.
 *
 * Catatan: file PHP ini tidak mengeluarkan output saat diakses langsung,
 * namun tetap diblokir lewat .htaccess untuk pertahanan berlapis.
 */
return [
    // Database
    'db_host' => 'altamura-db.id.domainesia.com',
    'db_user' => 'ampangpe_wc-17',
    'db_pass' => 'GANTI_DENGAN_PASSWORD_BARU_YANG_KUAT',
    'db_name' => 'ampangpe_wc-17',

    // WapiSender (connect.php)
    'wapi_key' => 'GANTI_API_KEY_WAPISENDER',

    // Key untuk library/class.wapisender.php (lann sender)
    'lann_api_key' => 'GANTI_LANN_API_KEY',
];
