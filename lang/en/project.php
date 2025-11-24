<?php

declare(strict_types=1);

return [
    'resource' => [
        'model_label' => 'Project|Projects',
        'name' => 'Name',
        'description' => 'Description',
        'start_date' => 'Start Date',
        'url' => 'URL',
        'partners' => 'Partners',
        'logo' => 'Logo',
        'logo_id' => 'Logo',
        'status_label' => 'Status',
        'status' => [
            'draft' => 'Draft',
            'publish' => 'Publish',
            'archive' => 'Archive',
        ],
    ],
    'action' => [
        'edit' => 'Edit',
        'view' => 'View',
        'visit' => 'Visit',
    ],
    'export_completed' => 'Your project export has completed and :successful_rows row(s) exported.',
    'export_failed' => ':failed_rows row(s) failed to export.',
    'import_completed' => ':successful_rows row(s) imported.',
    'import_failed' => ':failed_rows row(s) failed to import.',
];
