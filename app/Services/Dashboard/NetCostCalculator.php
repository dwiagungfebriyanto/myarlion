<?php

namespace App\Services\Dashboard;

use App\Models\OtherIncome;
use App\Models\OutcomeCheque;
use App\Models\OutcomeType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class NetCostCalculator
{
    public function monthlyByYear(int $year): Collection
    {
        $monthExpression = $this->monthExpression('date');

        $costTotals = OutcomeCheque::query()
            ->selectRaw("$monthExpression as month, SUM(amount) as total")
            ->where('code', 'CF')
            ->whereYear('date', $year)
            ->groupByRaw($monthExpression)
            ->pluck('total', 'month');

        $otherIncomeTotals = OtherIncome::query()
            ->selectRaw("$monthExpression as month, SUM(amount) as total")
            ->where('code', 'CF')
            ->whereYear('date', $year)
            ->groupByRaw($monthExpression)
            ->pluck('total', 'month');

        return collect(range(1, 12))->mapWithKeys(function (int $month) use ($costTotals, $otherIncomeTotals) {
            return [$month => (float) ($costTotals->get($month, 0) - $otherIncomeTotals->get($month, 0))];
        });
    }

    public function costCategoryByGroup(string $month): Collection
    {
        $costTotals = OutcomeCheque::query()
            ->selectRaw('outcome_type_id, SUM(amount) as total')
            ->where('code', 'CF')
            ->where('date', 'like', "$month%")
            ->groupBy('outcome_type_id')
            ->pluck('total', 'outcome_type_id');

        $otherIncomeTotals = OtherIncome::query()
            ->selectRaw('outcome_type_id, SUM(amount) as total')
            ->where('code', 'CF')
            ->whereNotNull('outcome_type_id')
            ->where('date', 'like', "$month%")
            ->groupBy('outcome_type_id')
            ->pluck('total', 'outcome_type_id');

        $typeIds = $costTotals->keys()
            ->merge($otherIncomeTotals->keys())
            ->unique()
            ->values();

        if ($typeIds->isEmpty()) {
            return collect();
        }

        $groupedTotals = OutcomeType::query()
            ->select('outcome_types.id', 'outcome_groups.name as group_name')
            ->join('outcome_groups', 'outcome_groups.id', '=', 'outcome_types.outcome_group_id')
            ->whereIn('outcome_types.id', $typeIds)
            ->orderBy('outcome_groups.name')
            ->get()
            ->reduce(function (Collection $carry, OutcomeType $type) use ($costTotals, $otherIncomeTotals) {
                $groupName = $type->group_name;
                $netAmount = (float) ($costTotals->get($type->id, 0) - $otherIncomeTotals->get($type->id, 0));

                $carry->put($groupName, (float) $carry->get($groupName, 0) + $netAmount);

                return $carry;
            }, collect());

        return $groupedTotals
            ->map(fn (float $amount) => max(0, $amount))
            ->sortKeys();
    }

    public function costCategoryChartData(string $month): array
    {
        return $this->costCategoryByGroup($month)
            ->map(fn (float $amount, string $groupName) => [$groupName, $amount])
            ->values()
            ->all();
    }

    public function monthExpression(string $column): string
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return "CAST(strftime('%m', $column) AS INTEGER)";
        }

        return "MONTH($column)";
    }
}
