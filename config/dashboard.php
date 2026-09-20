<?php
return [
    // Token untuk endpoint agregat dashboard (set di .env => DASHBOARD_AGG_TOKEN="your-secret-token")
    'aggregate_token' => env('DASHBOARD_AGG_TOKEN', null),

    // TTL cache (detik) untuk snapshot agregat. Bisa di override di .env DASHBOARD_AGG_CACHE_TTL
    'cache_ttl' => (int) env('DASHBOARD_AGG_CACHE_TTL', 600),

    // Aktif/nonaktifkan caching agregat
    'cache_enabled' => env('DASHBOARD_AGG_CACHE_ENABLED', true),
];
