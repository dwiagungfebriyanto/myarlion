<?php

namespace App\Services;

use App\Models\FreeSample;
use App\Models\InventoryIn;
use App\Models\InventoryLost;
use App\Models\InventoryMutation;
use App\Models\InventoryOut;
use App\Models\InventoryStock;
use App\Models\JobStatement;
use App\Models\ReturnStock;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class InventoryStockHistoryService
{
    public function build(InventoryStock $anchor): array
    {
        $poStockId = $anchor->po_stock_id ?: optional($anchor->getPoStock())->id;
        $productId = (int) $anchor->product_id;
        $warehouseId = (int) $anchor->warehouse_id;
        $unitId = (int) $anchor->unit_id;

        $scopeStocks = InventoryStock::with(['product', 'warehouse', 'unit'])
            ->where('stock_bucket', 'stock')
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->where('unit_id', $unitId)
            ->when(
                is_null($poStockId),
                fn ($query) => $query->whereNull('po_stock_id'),
                fn ($query) => $query->where('po_stock_id', $poStockId)
            )
            ->get();

        $scopeStockIds = $scopeStocks->pluck('id');
        $events = collect()
            ->merge($this->buildInventoryInEvents($scopeStockIds, $unitId))
            ->merge($this->buildTransferOutEvents($scopeStockIds, $unitId))
            ->merge($this->buildTransferInEvents($scopeStockIds, $unitId))
            ->merge($this->buildMutationOutEvents($scopeStockIds))
            ->merge($this->buildMutationInEvents($scopeStockIds))
            ->merge($this->buildJobStockEvents($scopeStockIds))
            ->merge($this->buildFreeSampleEvents($scopeStockIds))
            ->merge($this->buildInventoryLostEvents($scopeStockIds, $unitId))
            ->merge($this->buildReturnStockEvents($scopeStockIds));

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

    private function buildInventoryInEvents(Collection $scopeStockIds, int $unitId): Collection
    {
        if ($scopeStockIds->isEmpty()) {
            return collect();
        }

        return InventoryIn::with(['poStock'])
            ->whereIn('destination_stock_id', $scopeStockIds)
            ->get()
            ->map(function (InventoryIn $inventoryIn) use ($unitId) {
                $poLabel = $inventoryIn->poStock?->unique_id ?? '-';

                return $this->makeEvent([
                    'id' => $inventoryIn->id,
                    'type' => 'inventory_in',
                    'effective_at' => $inventoryIn->getRawOriginal('datetime'),
                    'description' => 'Stock In dari PO ' . $poLabel,
                    'qty' => $this->quantityFromColumns($inventoryIn->amount, $inventoryIn->weight, $unitId),
                    'value' => (float) $inventoryIn->getRawOriginal('purchase_cost'),
                    'unit_value' => (float) $inventoryIn->getRawOriginal('purchase_cost_per_unit'),
                    'reference' => 'PO ' . $poLabel,
                    'reference_url' => $inventoryIn->poStock
                        ? route('po_stock.detail', $inventoryIn->poStock->id)
                        : null,
                    'direction' => 'in',
                    'sort_order' => 10,
                ]);
            });
    }

    private function buildTransferOutEvents(Collection $scopeStockIds, int $unitId): Collection
    {
        if ($scopeStockIds->isEmpty()) {
            return collect();
        }

        return InventoryOut::with(['warehouse'])
            ->whereIn('original_stock_id', $scopeStockIds)
            ->get()
            ->map(function (InventoryOut $inventoryOut) use ($unitId) {
                $warehouseName = $inventoryOut->warehouse?->warehouse_name ?? 'Warehouse tujuan';

                return $this->makeEvent([
                    'id' => $inventoryOut->id,
                    'type' => 'transfer_out',
                    'effective_at' => $inventoryOut->getRawOriginal('datetime'),
                    'description' => 'Transfer Stock ke WH ' . $warehouseName,
                    'qty' => -$this->quantityFromColumns($inventoryOut->amount, $inventoryOut->weight, $unitId),
                    'value' => (float) $inventoryOut->getRawOriginal('purchase_cost'),
                    'unit_value' => (float) $inventoryOut->getRawOriginal('purchase_cost_per_unit'),
                    'reference' => $warehouseName,
                    'direction' => 'out',
                    'sort_order' => 20,
                ]);
            });
    }

    private function buildTransferInEvents(Collection $scopeStockIds, int $unitId): Collection
    {
        if ($scopeStockIds->isEmpty()) {
            return collect();
        }

        return InventoryOut::with(['originalStock.warehouse'])
            ->whereIn('destination_stock_id', $scopeStockIds)
            ->get()
            ->map(function (InventoryOut $inventoryOut) use ($unitId) {
                $originWarehouse = $inventoryOut->originalStock?->warehouse?->warehouse_name ?? 'Warehouse asal';

                return $this->makeEvent([
                    'id' => $inventoryOut->id,
                    'type' => 'transfer_in',
                    'effective_at' => $inventoryOut->getRawOriginal('datetime'),
                    'description' => 'Transfer Stock dari WH ' . $originWarehouse,
                    'qty' => $this->quantityFromColumns($inventoryOut->amount, $inventoryOut->weight, $unitId),
                    'value' => (float) $inventoryOut->getRawOriginal('purchase_cost'),
                    'unit_value' => (float) $inventoryOut->getRawOriginal('purchase_cost_per_unit'),
                    'reference' => $originWarehouse,
                    'direction' => 'in',
                    'sort_order' => 30,
                ]);
            });
    }

    private function buildMutationOutEvents(Collection $scopeStockIds): Collection
    {
        if ($scopeStockIds->isEmpty()) {
            return collect();
        }

        return InventoryMutation::with(['product'])
            ->whereIn('original_stock_id', $scopeStockIds)
            ->get()
            ->map(function (InventoryMutation $mutation) {
                $sku = $mutation->product?->sku ?? $mutation->new_product_id;

                return $this->makeEvent([
                    'id' => $mutation->id,
                    'type' => 'mutation_out',
                    'effective_at' => $mutation->getRawOriginal('created_at'),
                    'description' => 'Mutation ke SKU ' . $sku,
                    'qty' => -(float) $mutation->qty_mutation,
                    'value' => (float) $mutation->getRawOriginal('source_purchase_cost'),
                    'unit_value' => (float) $mutation->getRawOriginal('source_purchase_cost_per_unit'),
                    'reference' => 'SKU ' . $sku,
                    'direction' => 'out',
                    'sort_order' => 40,
                ]);
            });
    }

    private function buildMutationInEvents(Collection $scopeStockIds): Collection
    {
        if ($scopeStockIds->isEmpty()) {
            return collect();
        }

        return InventoryMutation::with(['originalSku'])
            ->whereIn('destination_stock_id', $scopeStockIds)
            ->get()
            ->map(function (InventoryMutation $mutation) {
                $sku = $mutation->originalSku?->sku ?? $mutation->original_product_id;

                return $this->makeEvent([
                    'id' => $mutation->id,
                    'type' => 'mutation_in',
                    'effective_at' => $mutation->getRawOriginal('created_at'),
                    'description' => 'Mutation dari SKU ' . $sku,
                    'qty' => (float) $mutation->qty,
                    'value' => (float) $mutation->getRawOriginal('price'),
                    'unit_value' => $this->resolveUnitValue(
                        (float) $mutation->getRawOriginal('price'),
                        (float) $mutation->qty
                    ),
                    'reference' => 'SKU ' . $sku,
                    'direction' => 'in',
                    'sort_order' => 50,
                ]);
            });
    }

    private function buildJobStockEvents(Collection $scopeStockIds): Collection
    {
        if ($scopeStockIds->isEmpty()) {
            return collect();
        }

        return JobStatement::with(['job'])
            ->where('is_sample', false)
            ->whereIn('inventory_stock_id', $scopeStockIds)
            ->get()
            ->map(function (JobStatement $jobStatement) {
                $jobCode = $jobStatement->job?->code ?? '-';

                return $this->makeEvent([
                    'id' => $jobStatement->id,
                    'type' => 'job_stock',
                    'effective_at' => $jobStatement->getRawOriginal('created_at'),
                    'description' => 'Stock dipakai untuk Job ' . $jobCode,
                    'qty' => -(float) $jobStatement->quantity,
                    'value' => (float) $jobStatement->getRawOriginal('stock_cost'),
                    'unit_value' => (float) $jobStatement->getRawOriginal('stock_cost_per_unit'),
                    'reference' => 'Job ' . $jobCode,
                    'reference_url' => $jobStatement->job
                        ? route('job.list.edit', $jobStatement->job->id)
                        : null,
                    'direction' => 'out',
                    'sort_order' => 60,
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
                return $this->makeEvent([
                    'id' => $inventoryLost->id,
                    'type' => 'inventory_lost',
                    'effective_at' => $inventoryLost->getRawOriginal('datetime'),
                    'description' => 'Stock hilang',
                    'qty' => -$this->quantityFromColumns($inventoryLost->amount, $inventoryLost->weight, $unitId),
                    'value' => (float) $inventoryLost->getRawOriginal('purchase_cost'),
                    'unit_value' => (float) $inventoryLost->getRawOriginal('purchase_cost_per_unit'),
                    'reference' => 'Inventory Lost',
                    'direction' => 'out',
                    'sort_order' => 80,
                ]);
            });
    }

    private function buildFreeSampleEvents(Collection $scopeStockIds): Collection
    {
        if ($scopeStockIds->isEmpty()) {
            return collect();
        }

        return FreeSample::with(['Customer', 'inquiry', 'inventoryStock'])
            ->whereIn('inventory_stock_id', $scopeStockIds)
            ->get()
            ->map(function (FreeSample $freeSample) {
                $customerName = $freeSample->recipientName();

                return $this->makeEvent([
                    'id' => $freeSample->id,
                    'type' => 'free_sample',
                    'effective_at' => $freeSample->getRawOriginal('date'),
                    'description' => 'Free Sample ke Customer ' . $customerName,
                    'qty' => -(float) $freeSample->quantity,
                    'value' => (float) $freeSample->purchase_cost,
                    'unit_value' => (float) $freeSample->getRawOriginal('purchase_cost_per_unit'),
                    'reference' => $customerName,
                    'direction' => 'out',
                    'sort_order' => 70,
                ]);
            });
    }

    private function buildReturnStockEvents(Collection $scopeStockIds): Collection
    {
        if ($scopeStockIds->isEmpty()) {
            return collect();
        }

        return ReturnStock::with(['warehouse'])
            ->whereIn('inventory_stock_id', $scopeStockIds)
            ->get()
            ->map(function (ReturnStock $returnStock) {
                $warehouseName = $returnStock->warehouse?->warehouse_name ?? 'Return Stock';

                return $this->makeEvent([
                    'id' => $returnStock->id,
                    'type' => 'return_stock',
                    'effective_at' => $returnStock->getRawOriginal('created_at'),
                    'description' => 'Return Stock dari inventory',
                    'qty' => -(float) $returnStock->qty,
                    'value' => (float) $returnStock->getRawOriginal('stock_cost'),
                    'unit_value' => (float) $returnStock->getRawOriginal('stock_cost_per_unit'),
                    'reference' => $warehouseName,
                    'direction' => 'out',
                    'sort_order' => 90,
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
