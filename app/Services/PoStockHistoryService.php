<?php

namespace App\Services;

use App\Models\InventoryIn;
use App\Models\JobPoStock;
use App\Models\PoStock;
use App\Models\PoStockProduct;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PoStockHistoryService
{
    public function build(PoStock $poStock): array
    {
        $poStock->loadMissing([
            'supplier',
            'poStockDetail.product',
            'poStockDetail.unit',
        ]);

        $itemContexts = $this->buildItemContexts($poStock->poStockDetail);
        $events = collect()
            ->merge($this->buildInventoryEvents($poStock, $itemContexts))
            ->merge($this->buildJobEvents($poStock, $itemContexts));

        return [
            'item_count' => $itemContexts->count(),
            'product_histories' => $this->buildProductHistories($itemContexts, $events),
        ];
    }

    private function buildInventoryEvents(PoStock $poStock, Collection $itemContexts): Collection
    {
        return InventoryIn::with(['product', 'unit', 'inventoryStock'])
            ->where('po_stock_id', $poStock->id)
            ->get()
            ->map(function (InventoryIn $inventoryIn) use ($itemContexts) {
                $itemContext = $this->resolveItemContext(
                    $itemContexts,
                    (int) $inventoryIn->product_id,
                    (int) $inventoryIn->unit_id
                );

                $sku = $itemContext['sku'] ?? ($inventoryIn->product?->sku ?? (string) $inventoryIn->product_id);
                $unitName = $itemContext['unit_name'] ?? ($inventoryIn->unit?->unit_name ?? '-');
                $inventoryStock = $inventoryIn->inventoryStock;
                $referenceUrl = match ($inventoryStock?->getStockBucket()) {
                    'sample' => route('product.inventory_stock.sample_history', $inventoryStock),
                    'stock' => route('product.inventory_stock.history', $inventoryStock),
                    default => null,
                };

                return $this->makeEvent([
                    'id' => $inventoryIn->id,
                    'type' => 'po_to_inventory',
                    'effective_at' => $inventoryIn->getRawOriginal('datetime'),
                    'description' => 'PO masuk ke Inventory',
                    'qty' => $this->quantityFromColumns($inventoryIn->amount, $inventoryIn->weight, (int) $inventoryIn->unit_id),
                    'value' => (float) $inventoryIn->getRawOriginal('purchase_cost'),
                    'unit_value' => (float) $inventoryIn->getRawOriginal('purchase_cost_per_unit'),
                    'reference' => 'Inventory In #' . $inventoryIn->id,
                    'reference_url' => $referenceUrl,
                    'sort_order' => 10,
                    'item_key' => $itemContext['key'] ?? $this->itemKey((int) $inventoryIn->product_id, (int) $inventoryIn->unit_id),
                    'sku' => $sku,
                    'unit_name' => $unitName,
                    'row_variant' => 'inventory',
                ]);
            });
    }

    private function buildJobEvents(PoStock $poStock, Collection $itemContexts): Collection
    {
        return JobPoStock::with(['job', 'product.unit'])
            ->where('po_stock_id', $poStock->id)
            ->get()
            ->map(function (JobPoStock $jobPoStock) use ($itemContexts) {
                $itemContext = $this->resolveItemContextByProduct($itemContexts, (int) $jobPoStock->product_id);
                $sku = $itemContext['sku'] ?? ($jobPoStock->product?->sku ?? (string) $jobPoStock->product_id);
                $unitName = $itemContext['unit_name'] ?? ($jobPoStock->product?->unit?->unit_name ?? '-');
                $jobCode = $jobPoStock->job?->code ?? '-';

                return $this->makeEvent([
                    'id' => $jobPoStock->id,
                    'type' => 'po_to_job',
                    'effective_at' => $jobPoStock->getRawOriginal('created_at'),
                    'description' => 'PO dipakai untuk Job ' . $jobCode,
                    'qty' => (float) $jobPoStock->qty,
                    'value' => (float) $jobPoStock->amount,
                    'unit_value' => $this->resolveUnitValue((float) $jobPoStock->amount, (float) $jobPoStock->qty),
                    'reference' => 'Job ' . $jobCode,
                    'reference_url' => $jobPoStock->job
                        ? route('job_statement.index', $jobPoStock->job->id)
                        : null,
                    'sort_order' => 20,
                    'item_key' => $itemContext['key'] ?? $this->itemKey((int) $jobPoStock->product_id, (int) ($jobPoStock->product?->unit_id ?? 0)),
                    'sku' => $sku,
                    'unit_name' => $unitName,
                    'row_variant' => 'job',
                ]);
            });
    }

    private function buildItemContexts(Collection $poDetails): Collection
    {
        return $poDetails
            ->groupBy(fn (PoStockProduct $detail) => $this->itemKey((int) $detail->product_id, (int) $detail->unit_id))
            ->map(function (Collection $details, string $key) {
                /** @var PoStockProduct $detail */
                $detail = $details->first();

                return [
                    'key' => $key,
                    'product_id' => (int) $detail->product_id,
                    'unit_id' => (int) $detail->unit_id,
                    'sku' => $detail->product?->sku ?? (string) $detail->product_id,
                    'tab_product_display' => $detail->product?->skuFormat() ?? ($detail->product?->sku ?? (string) $detail->product_id),
                    'unit_name' => $detail->unit?->unit_name ?? '-',
                    'total_qty' => (float) $details->sum('qty'),
                    'remaining_qty' => (float) $details->sum('remaining_qty'),
                ];
            })
            ->values()
            ->keyBy('key');
    }

    private function resolveItemContext(Collection $itemContexts, int $productId, int $unitId): ?array
    {
        $context = $itemContexts->get($this->itemKey($productId, $unitId));

        if ($context) {
            return $context;
        }

        return $this->resolveItemContextByProduct($itemContexts, $productId);
    }

    private function resolveItemContextByProduct(Collection $itemContexts, int $productId): ?array
    {
        return $itemContexts->first(fn (array $context) => $context['product_id'] === $productId);
    }

    private function sortEvents(Collection $events): Collection
    {
        $sorted = $events->all();

        usort($sorted, function (array $left, array $right) {
            $leftDate = $left['effective_at']->getTimestamp();
            $rightDate = $right['effective_at']->getTimestamp();

            if ($leftDate !== $rightDate) {
                return $leftDate <=> $rightDate;
            }

            if ($left['sort_order'] !== $right['sort_order']) {
                return $left['sort_order'] <=> $right['sort_order'];
            }

            return $left['id'] <=> $right['id'];
        });

        return collect($sorted);
    }

    private function makeEvent(array $attributes): array
    {
        $effectiveAt = $attributes['effective_at'] instanceof Carbon
            ? $attributes['effective_at']->copy()
            : Carbon::parse($attributes['effective_at']);

        return [
            'id' => (int) $attributes['id'],
            'type' => $attributes['type'],
            'effective_at' => $effectiveAt,
            'date_display' => $this->formatDate($effectiveAt),
            'description' => $attributes['description'],
            'qty' => (float) $attributes['qty'],
            'value' => (float) $attributes['value'],
            'unit_value' => array_key_exists('unit_value', $attributes)
                ? (float) $attributes['unit_value']
                : null,
            'reference' => $attributes['reference'],
            'reference_url' => $attributes['reference_url'] ?? null,
            'remark' => '-',
            'sort_order' => (int) $attributes['sort_order'],
            'item_key' => $attributes['item_key'],
            'sku' => $attributes['sku'],
            'unit_name' => $attributes['unit_name'],
            'row_variant' => $attributes['row_variant'],
        ];
    }

    private function itemKey(int $productId, int $unitId): string
    {
        return $productId . ':' . $unitId;
    }

    private function quantityFromColumns($amount, $weight, int $unitId): float
    {
        return (float) (($unitId === 1) ? $weight : $amount);
    }

    private function buildProductHistories(Collection $itemContexts, Collection $events): Collection
    {
        $eventsByItemKey = $events
            ->groupBy('item_key')
            ->map(function (Collection $itemEvents, string $itemKey) use ($itemContexts) {
                $itemContext = $itemContexts->get($itemKey);
                $startingQty = (float) ($itemContext['total_qty'] ?? 0);

                return $this->decorateEvents($this->sortEvents($itemEvents), $startingQty);
            });

        return $itemContexts
            ->values()
            ->map(function (array $itemContext, int $index) use ($eventsByItemKey) {
                return [
                    'key' => $itemContext['key'],
                    'sku' => $itemContext['sku'],
                    'tab_product_display' => $itemContext['tab_product_display'],
                    'unit_name' => $itemContext['unit_name'],
                    'product_display' => $itemContext['sku'],
                    'total_qty_display' => $this->formatQuantity($itemContext['total_qty']),
                    'remaining_qty_display' => $this->formatQuantity($itemContext['remaining_qty']),
                    'events' => $eventsByItemKey->get($itemContext['key'], collect())->values(),
                    'tab_id' => 'po-history-tab-' . $index,
                ];
            });
    }

    private function decorateEvents(Collection $events, float $startingQty): Collection
    {
        $runningStock = $startingQty;

        return $events->map(function (array $event) use (&$runningStock) {
            $runningStock -= $event['qty'];
            $event['running_stock'] = $runningStock;
            $displayQty = $event['row_variant'] === 'job'
                ? -abs($event['qty'])
                : abs($event['qty']);

            $event['qty_display'] = $this->formatQuantity($displayQty, true);
            $event['running_stock_display'] = $this->formatQuantity($runningStock);
            $event['value_display'] = currencyFormat($event['value']);
            $event['remark'] = $this->formatUnitValue(
                $event['unit_value'] ?? $this->resolveUnitValue($event['value'], $event['qty'])
            );

            return $event;
        });
    }

    private function formatQuantity(float $quantity, bool $withSign = false): string
    {
        $formatted = number_format(abs($quantity), 2, ',', '.');

        if (!$withSign) {
            return $formatted;
        }

        $sign = $quantity >= 0 ? '+' : '-';

        return $sign . $formatted;
    }

    private function resolveUnitValue(float $value, float $quantity): float
    {
        if (abs($quantity) < 0.0001) {
            return 0.0;
        }

        return abs($value / $quantity);
    }

    private function formatUnitValue(float $unitValue): string
    {
        return currencyFormat(abs($unitValue));
    }

    private function formatDate(Carbon $date): string
    {
        if ($date->format('H:i:s') === '00:00:00') {
            return $date->format('d-M-y');
        }

        return $date->format('d-M-y H:i');
    }
}
