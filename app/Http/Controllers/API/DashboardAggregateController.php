<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\DashboardAggregateRequest;
use App\Services\Dashboard\DashboardAggregator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;

class DashboardAggregateController extends Controller
{
    public function __invoke(DashboardAggregateRequest $request, DashboardAggregator $aggregator): JsonResponse
    {
        $token = $request->header('X-Dashboard-Token') ?? $request->input('token');
        $expected = config('dashboard.aggregate_token');

        if (!$expected || !$token || !hash_equals($expected, $token)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $year = (int) ($request->input('year', date('Y')));
        $monthParam = $request->input('month'); // bisa berupa "01", "1", "12", dll atau null
        
        // Format month menjadi YYYY-MM
        if ($monthParam !== null) {
            $monthNum = (int) $monthParam;
            if ($monthNum >= 1 && $monthNum <= 12) {
                $month = $year . '-' . sprintf('%02d', $monthNum);
            } else {
                // Invalid month, default ke bulan sekarang
                $month = $year . '-' . date('m');
            }
        } else {
            // Month null, gunakan bulan sekarang dalam tahun yang diminta
            $month = $year . '-' . date('m');
        }
        
        $marketing = $request->input('marketing', 'all');
        $websiteId = $request->input('website');
        $modulesRaw = $request->input('modules');
        $forceRefresh = (bool) $request->boolean('force_refresh', false);

        $modules = $modulesRaw
            ? array_values(array_intersect(
                array_filter(array_map('trim', explode(',', $modulesRaw))),
                DashboardAggregator::SUPPORTED_MODULES
            ))
            : DashboardAggregator::SUPPORTED_MODULES;

        $cacheKey = "dash_full:" . md5(json_encode([$year,$month,$marketing,$websiteId,$modules]));
        $cacheEnabled = config('dashboard.cache_enabled');
        $ttl = (int) config('dashboard.cache_ttl');

        if ($cacheEnabled && !$forceRefresh && Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            return response()->json($cached);
        }

        $started = microtime(true);
        $data = $aggregator->aggregate($year, $month, $marketing, $websiteId, $modules);
        $execMs = (int) ((microtime(true) - $started) * 1000);

        $payload = [
            'success' => true,
            'message' => 'Dashboard snapshot generated',
            'generated_at' => now()->toIso8601String(),
            'filters' => [
                'year' => $year,
                'month' => $month,
                'marketing' => $marketing,
                'website_id' => $websiteId,
                'modules' => $modules,
            ],
            'modules' => $data,
            'meta' => [
                'module_count' => count($data),
                'execution_ms' => $execMs,
                'cache' => false,
            ],
        ];

        if ($cacheEnabled) {
            Cache::put($cacheKey, $payload, $ttl);
        }

        return response()->json($payload);
    }
}
