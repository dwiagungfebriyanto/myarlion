<?php

namespace App\Services;

use App\Models\FreeSample;
use App\Models\InventoryIn;
use App\Models\InventoryLost;
use App\Models\InventoryOut;
use App\Models\InventoryStock;
use App\Models\JobStatement;
use App\Models\ReturnStock;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class InventorySampleHistoryService
{
    public function build(InventoryStock $anchor): array
    {
        $poStock = $anchor->getPoStock();

        $supplierId = (int) $poStock->supplier_id;
        $productId  = (int) $anchor->product_id;
        $warehouseId = (int) $anchor->warehouse_id;
        $unitId = (int) $anchor->unit_id;

        $scopeStocks = InventoryStock::with(['product', 'warehouse', 'unit'])
            ->where('stock_bucket', 'sample')
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->get()
            ->filter(fn (InventoryStock $stock) => $this->matchesSupplier($stock, $supplierId))
            ->values();

        $scopeStockIds = $scopeStocks->pluck('id');
        $events = collect()
            ->merge($this->buildSampleInEvents($supplierId, $productId, $warehouseId, $unitId))
            ->merge($this->buildTransferOutEvents($scopeStockIds, $productId, $warehouseId, $unitId))
            ->merge($this->buildTransferInEvents($supplierId, $productId, $warehouseId, $unitId))
            ->merge($this->buildJobSampleEvents($scopeStockIds, $unitId))
            ->merge($this->buildFreeSampleEvents($scopeStockIds, $unitId))
            ->merge($this->buildInventoryLostEvents($scopeStockIds, $unitId))
            ->merge($this->buildReturnSampleEvents($scopeStockIds, $unitId));

        $events = $this->sortEvents($events);

        $runningStock = 0.0;
        $events = $events->map(function (array $event) use (&$runningStock, $unitId) {
            $runningStock += $event['qty'];
            $event['running_stock'] = $runningStock;
            $event['qty_display'] = $this->formatQuantity($event['qty'], $unitId, true);
            $event['running_stock_display'] = $this->formatQuantity($runningStock, $unitId);
            $event['value_display'] = currencyFormat($event['value']);
            $event['remark'] = $this->formatUnitValue(
                $event['unit_value'] ?? $this->resolveUnitValue($event['value'], $event['qty'])
            );

            return $event;
        })->values();

        $currentStock = (float) $scopeStocks->sum(fn (InventoryStock $stock) => $this->stockQuantity($stock));

        return [
            'events' => $events,
            'current_stock' => $currentStock,
            'current_stock_display' => $this->formatQuantity($currentStock, $unitId),
            'ledger_ending_stock' => $runningStock,
            'ledger_ending_stock_display' => $this->formatQuantity($runningStock, $unitId),
            'has_balance_mismatch' => abs($currentStock - $runningStock) > 0.0001,
            'scope_stock_ids' => $scopeStockIds,
        ];
    }

    private function buildSampleInEvents(int $supplierId, int $productId, int $warehouseId, int $unitId): Collection
    {
        return InventoryIn::with(['poStock'])
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->whereHas('poStock', function ($query) use ($supplierId) {
                $query->where('po_type', 'sample')
                    ->where('supplier_id', $supplierId);
            })
            ->whereHas('inventoryStock', function ($query) {
                $query->where('stock_bucket', 'sample');
            })
            ->get()
            ->map(function (InventoryIn $inventoryIn) use ($unitId) {
                return $this->makeEvent([
                    'id' => $inventoryIn->id,
                    'type' => 'sample_in',
                    'effective_at' => $inventoryIn->getRawOriginal('datetime'),
                    'description' => 'Sample In dari PO ' . $inventoryIn->poStock->unique_id,
                    'qty' => $this->quantityFromColumns($inventoryIn->amount, $inventoryIn->weight, $unitId),
                    'value' => (float) $inventoryIn->getRawOriginal('purchase_cost'),
                    'unit_value' => (float) $inventoryIn->getRawOriginal('purchase_cost_per_unit'),
                    'reference' => 'PO ' . $inventoryIn->poStock->unique_id,
                    'reference_url' => route('po_stock.detail', $inventoryIn->poStock->id),
                    'direction' => 'in',
                    'sort_order' => 10,
                ]);
            });
    }

    private function buildTransferOutEvents(Collection $scopeStockIds, int $productId, int $warehouseId, int $unitId): Collection
    {
        if ($scopeStockIds->isEmpty()) {
            return collect();
        }

        return InventoryOut::with(['warehouse', 'originalStock'])
            ->where('product_id', $productId)
            ->where('original_warehouse_id', $warehouseId)
            ->whereIn('original_stock_id', $scopeStockIds)
            ->get()
            ->map(function (InventoryOut $inventoryOut) use ($unitId) {
                $qty = $this->quantityFromColumns($inventoryOut->amount, $inventoryOut->weight, $unitId);

                return $this->makeEvent([
                    'id' => $inventoryOut->id,
                    'type' => 'transfer_out',
                    'effective_at' => $inventoryOut->getRawOriginal('datetime'),
                    'description' => 'Transfer Sample ke WH ' . $inventoryOut->warehouse->warehouse_name,
                    'qty' => -$qty,
                    'value' => (float) $inventoryOut->getRawOriginal('purchase_cost'),
                    'unit_value' => (float) $inventoryOut->getRawOriginal('purchase_cost_per_unit'),
                    'reference' => $inventoryOut->warehouse->warehouse_name,
                    'direction' => 'out',
                    'sort_order' => 20,
                ]);
            });
    }

    private function buildTransferInEvents(int $supplierId, int $productId, int $warehouseId, int $unitId): Collection
    {
        return InventoryOut::with(['originalStock', 'warehouse'])
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->get()
            ->filter(function (InventoryOut $inventoryOut) use ($supplierId) {
                $originalStock = $inventoryOut->originalStock;

                return $originalStock
                    && $originalStock->getStockBucket() === 'sample'
                    && $this->matchesSupplier($originalStock, $supplierId);
            })
            ->map(function (InventoryOut $inventoryOut) use ($unitId) {
                $qty = $this->quantityFromColumns($inventoryOut->amount, $inventoryOut->weight, $unitId);
                $originWarehouse = $inventoryOut->originalWarehouse();
                $originLabel = $originWarehouse?->warehouse_name ?? 'Warehouse asal';

                return $this->makeEvent([
                    'id' => $inventoryOut->id,
                    'type' => 'transfer_in',
                    'effective_at' => $inventoryOut->getRawOriginal('datetime'),
                    'description' => 'Transfer Sample dari WH ' . $originLabel,
                    'qty' => $qty,
                    'value' => (float) $inventoryOut->getRawOriginal('purchase_cost'),
                    'unit_value' => (float) $inventoryOut->getRawOriginal('purchase_cost_per_unit'),
                    'reference' => $originLabel,
                    'direction' => 'in',
                    'sort_order' => 30,
                ]);
            });
    }

    private function buildJobSampleEvents(Collection $scopeStockIds, int $unitId): Collection
    {
        if ($scopeStockIds->isEmpty()) {
            return collect();
        }

        return JobStatement::with(['job.customer', 'job.marketing'])
            ->where('is_sample', true)
            ->whereIn('inventory_stock_id', $scopeStockIds)
            ->get()
            ->map(function (JobStatement $jobStatement) use ($unitId) {
                return $this->makeEvent([
                    'id' => $jobStatement->id,
                    'type' => 'job_sample',
                    'effective_at' => $jobStatement->created_at,
                    'description' => 'Sample dipakai untuk Job ' . $jobStatement->job->code,
                    'qty' => -(float) $jobStatement->quantity,
                    'value' => (float) $jobStatement->stock_cost,
                    'unit_value' => (float) $jobStatement->getRawOriginal('stock_cost_per_unit'),
                    'reference' => 'Job ' . $jobStatement->job->code,
                    'reference_url' => route('job.list.edit', $jobStatement->job->id),
                    'direction' => 'out',
                    'sort_order' => 40,
                ]);
            });
    }

    private function buildFreeSampleEvents(Collection $scopeStockIds, int $unitId): Collection
    {
        if ($scopeStockIds->isEmpty()) {
            return collect();
        }

        return FreeSample::with(['Customer', 'inquiry', 'inventoryStock'])
            ->whereIn('inventory_stock_id', $scopeStockIds)
            ->get()
            ->map(function (FreeSample $freeSample) use ($unitId) {
                $recipientName = $freeSample->recipientName();

                return $this->makeEvent([
                    'id' => $freeSample->id,
                    'type' => 'free_sample',
                    'effective_at' => $freeSample->getRawOriginal('date'),
                    'description' => 'Free Sample ke Customer ' . $recipientName,
                    'qty' => -(float) $freeSample->quantity,
                    'value' => (float) $freeSample->purchase_cost,
                    'unit_value' => (float) $freeSample->getRawOriginal('purchase_cost_per_unit'),
                    'reference' => $recipientName,
                    'direction' => 'out',
                    'sort_order' => 50,
                ]);
            });
    }

    private function buildInventoryLostEvents(Collection $scopeStockIds, int $unitId): Collection
    {
        if ($scopeStockIds->isEmpty()) {
            return collect();
        }

        return InventoryLost::with(['inventoryStock'])
            ->whereIn('inventory_stock_id', $scopeStockIds)
            ->get()
            ->map(function (InventoryLost $inventoryLost) use ($unitId) {
                $qty = $this->quantityFromColumns($inventoryLost->amount, $inventoryLost->weight, $unitId);

                return $this->makeEvent([
                    'id' => $inventoryLost->id,
                    'type' => 'inventory_lost',
                    'effective_at' => $inventoryLost->getRawOriginal('datetime'),
                    'description' => 'Sample hilang',
                    'qty' => -$qty,
                    'value' => (float) $inventoryLost->purchase_cost,
                    'unit_value' => (float) $inventoryLost->getRawOriginal('purchase_cost_per_unit'),
                    'reference' => 'Inventory Lost',
                    'direction' => 'out',
                    'sort_order' => 60,
                ]);
            });
    }

    private function buildReturnSampleEvents(Collection $scopeStockIds, int $unitId): Collection
    {
        if ($scopeStockIds->isEmpty()) {
            return collect();
        }

        return ReturnStock::with(['warehouse'])
            ->whereIn('inventory_stock_id', $scopeStockIds)
            ->get()
            ->map(function (ReturnStock $returnStock) use ($unitId) {
                return $this->makeEvent([
                    'id' => $returnStock->id,
                    'type' => 'return_sample',
                    'effective_at' => $returnStock->created_at,
                    'description' => 'Return Sample dari inventory',
                    'qty' => -(float) $returnStock->qty,
                    'value' => (float) $returnStock->stock_cost,
                    'unit_value' => (float) $returnStock->getRawOriginal('stock_cost_per_unit'),
                    'reference' => $returnStock->warehouse?->warehouse_name ?? 'Return Stock',
                    'direction' => 'out',
                    'sort_order' => 70,
                ]);
            });
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
            'direction' => $attributes['direction'],
            'sort_order' => (int) $attributes['sort_order'],
        ];
    }

    private function matchesSupplier(InventoryStock $stock, int $supplierId): bool
    {
        return (int) optional($stock->getPoStock())->supplier_id === $supplierId;
    }

    private function stockQuantity(InventoryStock $stock): float
    {
        return $this->quantityFromColumns($stock->amount, $stock->weight, (int) $stock->unit_id);
    }

    private function quantityFromColumns($amount, $weight, int $unitId): float
    {
        return (float) (($unitId === 1) ? $weight : $amount);
    }

    private function formatQuantity(float $quantity, int $unitId, bool $withSign = false): string
    {
        $decimals = 2;
        $formatted = number_format(abs($quantity), $decimals, ',', '.');

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
