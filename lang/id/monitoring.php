<?php

return [
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
