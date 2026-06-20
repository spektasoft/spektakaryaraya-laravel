<?php

return [
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
