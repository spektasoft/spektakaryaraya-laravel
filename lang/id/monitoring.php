<?php

return [
    'resource' => [
        'label' => 'Situs Dipantau',
        'plural_label' => 'Situs Dipantau',
        'navigation_group' => 'Administrasi',
        'sections' => [
            'general' => 'Konfigurasi Umum',
            'baselines' => 'Baseline Terkunci (Diambil dari Payload Stabil)',
        ],
        'fields' => [
            'name' => 'Nama Situs',
            'url' => 'URL Target',
            'is_active' => 'Status Pemantauan Aktif',
            'project' => 'Proyek Terkait',
            'md5_hash' => 'Checksum MD5 Baseline',
            'links_count' => 'Jumlah Tautan Baseline',
            'scripts_count' => 'Jumlah Skrip Baseline',
        ],
        'columns' => [
            'uptime' => 'Uptime',
            'integrity' => 'Integritas',
            'latency' => 'Latensi',
            'last_checked' => 'Terakhir Diperiksa',
        ],
        'actions' => [
            'check_now' => 'Periksa Sekarang',
            'recalibrate' => 'Rekalibrasi',
            'notifications' => [
                'check_success' => 'Operasi diagnostik berhasil dijalankan!',
                'recalibrate_success' => 'Baseline berhasil diperbarui.',
                'request_failed' => 'Permintaan gagal: Kode HTTP :code',
            ],
        ],
        'status' => [
            'active' => 'Aktif',
            'disabled' => 'Nonaktif',
        ],
    ],
    'logs' => [
        'type' => 'Tipe',
        'status' => 'Status',
        'http_status' => 'Status HTTP',
        'latency' => 'Latensi',
        'logged_at' => 'Dicatat Pada',
    ],
    'uptime' => [
        'alert_title' => 'Peringatan Website Down',
        'alert_body' => 'Website :name mengembalikan kode :code.',
        'connection_failed' => 'Koneksi ke :name gagal. Galat: :error',
        'log_error' => 'Status HTTP Tidak Berhasil: :code',
    ],
    'integrity' => [
        'alert_title' => 'Peringatan Keamanan',
        'alert_body' => 'Pemindaian integritas :name gagal. Pelanggaran: :violations',
        'log_error' => 'Kegagalan Pemeriksaan Integritas: :error',
        'violations' => [
            'checksum' => 'Ketidakcocokan checksum',
            'links' => 'Jumlah tautan melonjak (:current vs ekspektasi :expected)',
            'scripts' => 'Jumlah skrip melonjak (:current vs ekspektasi :expected)',
        ],
    ],
    'common' => [
        'system_alert' => 'Peringatan Sistem Monitoring',
        'security_alert' => 'Peringatan Sistem Keamanan',
    ],
];
