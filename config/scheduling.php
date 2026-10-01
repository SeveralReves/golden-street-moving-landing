<?php

return [
    // How many moves can run at the same time (one per available crew/truck).
    'max_concurrent_moves' => (int) env('SCHEDULING_MAX_CONCURRENT_MOVES', 2),

    // Duration used when the quote has no estimate to derive one from.
    'default_duration_hours' => 4,

    // Calendar grid bounds, as HH:MM:SS.
    'day_start' => '06:00:00',
    'day_end' => '21:00:00',
];
