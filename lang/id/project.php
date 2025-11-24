<?php

declare(strict_types=1);

return [
    'resource' => [
        'model_label' => 'Proyek|Proyek',
        'name' => 'Nama',
        'description' => 'Deskripsi',
        'start_date' => 'Tanggal Mulai',
        'url' => 'URL',
        'partners' => 'Mitra',
        'logo' => 'Logo',
        'logo_id' => 'Logo',
        'status_label' => 'Status',
        'status' => [
            'draft' => 'Konsep',
            'publish' => 'Terbit',
            'archive' => 'Arsip',
        ],
    ],
    'action' => [
        'edit' => 'Ubah',
        'view' => 'Lihat',
        'visit' => 'Kunjungi',
    ],
    'export_completed' => 'Ekspor proyek Anda telah selesai dan :successful_rows baris berhasil diekspor.',
    'export_failed' => ':failed_rows baris gagal diekspor.',
    'import_completed' => ':successful_rows baris berhasil diimpor.',
    'import_failed' => ':failed_rows baris gagal diimpor.',
];
