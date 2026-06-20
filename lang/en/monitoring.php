<?php

return [
    'resource' => [
        'label' => 'Monitored Site',
        'plural_label' => 'Monitored Sites',
        'navigation_group' => 'Administration',
        'sections' => [
            'general' => 'General Configuration',
            'baselines' => 'Locked Baselines (Captured from Stable Payload)',
        ],
        'fields' => [
            'name' => 'Site Name',
            'url' => 'Target URL',
            'is_active' => 'Active Monitoring Status',
            'project' => 'Associated Project',
            'md5_hash' => 'Baseline MD5 Checksum',
            'links_count' => 'Baseline Link Count',
            'scripts_count' => 'Baseline Script Count',
        ],
        'columns' => [
            'uptime' => 'Uptime',
            'integrity' => 'Integrity',
            'latency' => 'Latency',
            'last_checked' => 'Last Checked',
        ],
        'actions' => [
            'check_now' => 'Check Now',
            'recalibrate' => 'Recalibrate',
            'notifications' => [
                'check_success' => 'Diagnostic operations run successfully!',
                'recalibrate_success' => 'Baseline baselines successfully updated.',
                'request_failed' => 'Request failed: HTTP Code :code',
            ],
        ],
        'status' => [
            'active' => 'Active',
            'disabled' => 'Disabled',
        ],
    ],
    'logs' => [
        'type' => 'Type',
        'status' => 'Status',
        'http_status' => 'HTTP Status',
        'latency' => 'Latency',
        'logged_at' => 'Logged At',
    ],
    'uptime' => [
        'alert_title' => 'Website Down Alert',
        'alert_body' => 'Website :name returned code :code.',
        'connection_failed' => 'Connection to :name failed. Error: :error',
        'log_error' => 'Unsuccessful HTTP Status: :code',
    ],
    'integrity' => [
        'alert_title' => 'Security Alert',
        'alert_body' => ':name integrity scan failed. Violations: :violations',
        'log_error' => 'Integrity Check Failure: :error',
        'violations' => [
            'checksum' => 'Checksum mismatch',
            'links' => 'Links count spiked (:current vs expected :expected)',
            'scripts' => 'Scripts count spiked (:current vs expected :expected)',
        ],
    ],
    'common' => [
        'system_alert' => 'Monitoring System Alert',
        'security_alert' => 'Security System Alert',
    ],
];
