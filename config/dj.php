<?php

return [
    // Slot date range used for generating weekend dates (inclusive)
    // Format: YYYY-MM-DD
    'slot_range' => [
        'start' => env('DJ_SLOT_START', '2026-01-15'),
        'end' => env('DJ_SLOT_END', '2026-02-28'),
    ],
];
