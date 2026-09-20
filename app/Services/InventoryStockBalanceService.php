<?php

namespace App\Services;

use App\Models\InventoryStock;
use App\Models\PoStockProduct;
use RuntimeException;

class InventoryStockBalanceService
{
    public function lockStock(int $stockId): InventoryStock
    {
        return InventoryStock::query()
            ->lockForUpdate()
            ->findOrFail($stockId);
    }

    public function unitField(int $unitId): string
    {
        return $unitId === 1 ? 'weight' : 'amount';
    }

    public function quantityFromValues(int $unitId, $amount, $weight): float
    {
        return $unitId === 1
            ? (float) ($weight ?? 0)
            : (float) ($amount ?? 0);
    }

    public function quantityFromStock(InventoryStock $stock): float
    {
        return $this->quantityFromValues(
            (int) $stock->unit_id,
            $stock->getRawOriginal('amount'),
            $stock->getRawOriginal('weight')
        );
    }

    public function bucketFromInventoryType(?string $inventoryType): string
    {
        return $inventoryType === 'sample' ? 'sample' : 'stock';
    }

    public function bucketForStock(InventoryStock $stock): string
    {
        return $stock->getStockBucket();
    }

    public function resolvePoStockIdFromStock(InventoryStock $stock): ?int
    {
        if (! is_null($stock->po_stock_id)) {
            return (int) $stock->po_stock_id;
        }

        $poStock = $stock->getPoStock();

        return $poStock ? (int) $poStock->id : null;
    }

    public function resolveRootInventoryInIdFromStock(InventoryStock $stock): ?int
    {
        return $stock->getRootInventoryInId();
    }

    public function findOrCreateActiveStock(
        int $productId,
        ?int $poStockId,
        int $warehouseId,
        int $unitId,
        string $bucket,
        array $legacyAttributes = []
    ): InventoryStock {
        $query = InventoryStock::query()
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->where('unit_id', $unitId)
            ->where('stock_bucket', $bucket)
            ->lockForUpdate();

        if (is_null($poStockId)) {
            $query->whereNull('po_stock_id');
        } else {
            $query->where('po_stock_id', $poStockId);
        }

        $existing = $query->first();
        if ($existing) {
            return $existing;
        }

        $stock = new InventoryStock();
        $stock->inventory_id = $legacyAttributes['inventory_id'] ?? 0;
        $stock->inventory_type = $legacyAttributes['inventory_type'] ?? ($bucket === 'sample' ? 'sample' : 'in');
        $stock->product_id = $productId;
        $stock->po_stock_id = $poStockId;
        $stock->stock_bucket = $bucket;
        $stock->warehouse_id = $warehouseId;
        $stock->unit_id = $unitId;
        $stock->amount = $unitId === 1 ? 0 : 0;
        $stock->weight = $unitId === 1 ? 0 : 0;
        $stock->purchase_cost = $legacyAttributes['reference_purchase_cost'] ?? 0;
        $stock->purchase_cost_per_unit = $legacyAttributes['reference_purchase_cost_per_unit'] ?? 0;
        $stock->save();

        return $stock->fresh();
    }

    public function addToActiveStock(
        int $productId,
        ?int $poStockId,
        int $warehouseId,
        int $unitId,
        string $bucket,
        float $quantity,
        float $cost,
        array $legacyAttributes = []
    ): InventoryStock {
        $stock = $this->findOrCreateActiveStock(
            $productId,
            $poStockId,
            $warehouseId,
            $unitId,
            $bucket,
            $legacyAttributes
        );

        return $this->applyDelta($stock, $quantity, $cost);
    }

    public function subtractFromStock(InventoryStock $stock, float $quantity, float $cost): InventoryStock
    {
        return $this->applyDelta($stock, -$quantity, -$cost);
    }

    public function restoreToStock(InventoryStock $stock, float $quantity, float $cost): InventoryStock
    {
        return $this->applyDelta($stock, $quantity, $cost);
    }

    public function transferStock(
        InventoryStock $sourceStock,
        int $destinationWarehouseId,
        float $quantity,
        array $legacyDestinationAttributes = []
    ): array {
        $bucket = $this->bucketForStock($sourceStock);
        $poStockId = $this->resolvePoStockIdFromStock($sourceStock);
        $unitCost = (float) $sourceStock->getRawOriginal('purchase_cost_per_unit');
        $movedCost = $unitCost * $quantity;

        if ((int) $sourceStock->warehouse_id === $destinationWarehouseId) {
            return [
                'source' => $sourceStock,
                'destination' => $sourceStock,
                'bucket' => $bucket,
                'po_stock_id' => $poStockId,
                'moved_cost' => $movedCost,
                'unit_cost' => $unitCost,
            ];
        }

        $this->subtractFromStock($sourceStock, $quantity, $movedCost);

        $destinationStock = $this->addToActiveStock(
            (int) $sourceStock->product_id,
            $poStockId,
            $destinationWarehouseId,
            (int) $sourceStock->unit_id,
            $bucket,
            $quantity,
            $movedCost,
            array_merge($legacyDestinationAttributes, [
                'reference_purchase_cost' => (float) $sourceStock->getRawOriginal('purchase_cost'),
                'reference_purchase_cost_per_unit' => $unitCost,
            ])
        );

        return [
            'source' => $sourceStock,
            'destination' => $destinationStock,
            'bucket' => $bucket,
            'po_stock_id' => $poStockId,
            'moved_cost' => $movedCost,
            'unit_cost' => $unitCost,
        ];
    }

    public function applyDelta(InventoryStock $stock, float $quantityDelta, float $costDelta): InventoryStock
    {
        $currentQuantity = $this->quantityFromStock($stock);
        $currentCost = (float) $stock->getRawOriginal('purchase_cost');
        $currentUnitCost = (float) $stock->getRawOriginal('purchase_cost_per_unit');
        $poStockId = is_null($stock->po_stock_id) ? null : (int) $stock->po_stock_id;
        $poReferenceCost = $this->resolvePoReferenceCost((int) $stock->product_id, $poStockId);
        $hasPoLineage = ! is_null($poStockId);

        $newQuantity = $currentQuantity + $quantityDelta;

        if ($newQuantity < -0.0001) {
            throw new RuntimeException('Inventory stock quantity cannot be negative.');
        }

        if (abs($newQuantity) < 0.0001) {
            $newQuantity = 0.0;
        }

        $unitField = $this->unitField((int) $stock->unit_id);
        $otherField = $unitField === 'weight' ? 'amount' : 'weight';

        $stock->{$unitField} = $newQuantity;
        $stock->{$otherField} = 0;

        if ($hasPoLineage) {
            $stock->po_stock_id = $poStockId;
            $stock->purchase_cost = $poReferenceCost['total'] ?? $currentCost;
            $stock->purchase_cost_per_unit = $poReferenceCost['unit'] ?? $currentUnitCost;
        } else {
            $newCost = $currentCost + $costDelta;

            if ($newCost < -0.0001) {
                throw new RuntimeException('Inventory stock cost cannot be negative.');
            }

            if ($newQuantity === 0.0 || abs($newCost) < 0.0001) {
                $newCost = 0.0;
            }

            $stock->purchase_cost = $newCost;
            $stock->purchase_cost_per_unit = $newQuantity != 0.0
                ? $newCost / $newQuantity
                : 0.0;
        }

        $stock->save();

        return $stock->fresh();
    }

    private function resolvePoReferenceCost(int $productId, ?int $poStockId): ?array
    {
        if (is_null($poStockId)) {
            return null;
        }

        $poDetail = PoStockProduct::query()
            ->where('po_stock_id', $poStockId)
            ->where('product_id', $productId)
            ->first();

        if (! $poDetail) {
            return null;
        }

        $unitCost = (float) $poDetail->purchase_cost;

        return [
            'total' => round($unitCost * (float) $poDetail->qty, 2),
            'unit' => $unitCost,
        ];
    }
}
