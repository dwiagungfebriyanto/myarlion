<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const BACKUP_TABLE = 'inventory_cost_integrity_backups_20260615';

    private const COST_EPSILON = 0.01;

    public function up(): void
    {
        if (Schema::hasTable(self::BACKUP_TABLE)) {
            throw new RuntimeException(
                self::BACKUP_TABLE.' already exists. Resolve the previous correction or rollback before retrying.'
            );
        }

        Schema::create(self::BACKUP_TABLE, function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 32);
            $table->unsignedBigInteger('entity_id');
            $table->json('original_values');
            $table->json('corrected_values');
            $table->timestamp('created_at');

            $table->unique(['entity_type', 'entity_id'], 'inventory_cost_backup_entity_unique');
        });

        try {
            DB::transaction(function () {
                $now = now()->format('Y-m-d H:i:s');
                [$activeStocks, $targets, $canonicalCosts] = $this->loadStockTargets();

                if (count($targets) < 125) {
                    throw new RuntimeException(
                        'Expected at least 125 inventory stock corrections, found '.count($targets).'.'
                    );
                }

                if (! isset($targets[383]) || abs($targets[383]['canonical_cost'] - 30279.0) > self::COST_EPSILON) {
                    throw new RuntimeException('Inventory stock 383 no longer resolves to the verified unit cost 30279.');
                }

                $affectedProductIds = array_values(array_unique(array_column($targets, 'product_id')));

                $freeSamples = DB::table('free_samples')
                    ->whereIn('inventory_stock_id', array_keys($canonicalCosts))
                    ->lockForUpdate()
                    ->get()
                    ->filter(function ($row) use ($canonicalCosts) {
                        $canonicalCost = $canonicalCosts[$row->inventory_stock_id];
                        $correctTotal = round($canonicalCost * (float) $row->quantity, 2);

                        return abs((float) $row->purchase_cost_per_unit - round($canonicalCost, 2)) > self::COST_EPSILON
                            || abs((float) $row->purchase_cost - $correctTotal) > self::COST_EPSILON;
                    })
                    ->values();

                if ($freeSamples->count() < 3) {
                    throw new RuntimeException(
                        'Expected at least 3 free sample corrections, found '.$freeSamples->count().'.'
                    );
                }

                $jobStatements = DB::table('job_statements')
                    ->whereIn('inventory_stock_id', array_keys($canonicalCosts))
                    ->lockForUpdate()
                    ->get()
                    ->filter(function ($row) use ($canonicalCosts) {
                        $canonicalCost = $canonicalCosts[$row->inventory_stock_id];
                        $correctTotal = $canonicalCost * (float) $row->quantity;

                        return abs((float) $row->stock_cost_per_unit - $canonicalCost) > self::COST_EPSILON
                            || abs((float) $row->stock_cost - $correctTotal) > self::COST_EPSILON;
                    })
                    ->values();

                if ($jobStatements->count() < 23) {
                    throw new RuntimeException(
                        'Expected at least 23 job statement corrections, found '.$jobStatements->count().'.'
                    );
                }

                foreach ($targets as $stockId => $target) {
                    $stock = $target['stock'];
                    $correctedCost = $target['canonical_cost'] * $target['quantity'];
                    $corrected = [
                        'purchase_cost' => $correctedCost,
                        'purchase_cost_per_unit' => $target['canonical_cost'],
                        'updated_at' => $now,
                    ];

                    $this->backup('inventory_stock', $stockId, [
                        'purchase_cost' => $stock->purchase_cost,
                        'purchase_cost_per_unit' => $stock->purchase_cost_per_unit,
                        'updated_at' => $stock->updated_at,
                    ], $corrected, $now);

                    DB::table('inventory_stocks')->where('id', $stockId)->update($corrected);
                }

                foreach ($freeSamples as $freeSample) {
                    $canonicalCost = $canonicalCosts[$freeSample->inventory_stock_id];
                    $corrected = [
                        'purchase_cost' => round($canonicalCost * (float) $freeSample->quantity, 2),
                        'purchase_cost_per_unit' => round($canonicalCost, 2),
                        'updated_at' => $now,
                    ];

                    $this->backup('free_sample', (int) $freeSample->id, [
                        'purchase_cost' => $freeSample->purchase_cost,
                        'purchase_cost_per_unit' => $freeSample->purchase_cost_per_unit,
                        'updated_at' => $freeSample->updated_at,
                    ], $corrected, $now);

                    DB::table('free_samples')->where('id', $freeSample->id)->update($corrected);
                }

                foreach ($jobStatements as $jobStatement) {
                    $canonicalCost = $canonicalCosts[$jobStatement->inventory_stock_id];
                    $corrected = [
                        'stock_cost' => $canonicalCost * (float) $jobStatement->quantity,
                        'stock_cost_per_unit' => $canonicalCost,
                        'updated_at' => $now,
                    ];

                    $this->backup('job_statement', (int) $jobStatement->id, [
                        'stock_cost' => $jobStatement->stock_cost,
                        'stock_cost_per_unit' => $jobStatement->stock_cost_per_unit,
                        'updated_at' => $jobStatement->updated_at,
                    ], $corrected, $now);

                    DB::table('job_statements')->where('id', $jobStatement->id)->update($corrected);
                }

                $products = DB::table('products')
                    ->whereIn('id', $affectedProductIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                if ($products->count() < 87) {
                    throw new RuntimeException(
                        'Expected at least 87 existing affected products, found '.$products->count().'.'
                    );
                }

                $productIds = $products->keys()->map(fn ($id) => (int) $id)->values()->all();
                $activeStocksByProduct = $activeStocks->groupBy('product_id');

                foreach ($productIds as $productId) {
                    $product = $products->get($productId);
                    $highestCost = $activeStocksByProduct->get($productId, collect())
                        ->max(function ($stock) use ($targets) {
                            return $targets[$stock->id]['canonical_cost']
                                ?? (float) $stock->purchase_cost_per_unit;
                        }) ?? 0.0;
                    $corrected = [
                        'harga_tertinggi' => $highestCost,
                        'updated_at' => $now,
                    ];

                    $this->backup('product', (int) $productId, [
                        'harga_tertinggi' => $product->harga_tertinggi,
                        'updated_at' => $product->updated_at,
                    ], $corrected, $now);

                    DB::table('products')->where('id', $productId)->update($corrected);
                }

                $this->validateCorrection(
                    $targets,
                    $canonicalCosts,
                    $freeSamples,
                    $jobStatements,
                    $productIds
                );

                $this->assertPoDetailsAreUnique();
                $poReferenceTargets = $this->loadPoReferenceTargets();
                $this->applyPoReferenceCostCorrection($poReferenceTargets, $now);
                $this->validatePoReferenceCostCorrection($poReferenceTargets);
            }, 3);
        } catch (Throwable $exception) {
            Schema::dropIfExists(self::BACKUP_TABLE);

            throw $exception;
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable(self::BACKUP_TABLE)) {
            return;
        }

        $backups = DB::table(self::BACKUP_TABLE)->orderByDesc('id')->get();

        foreach ($backups as $backup) {
            $table = $this->tableForEntity($backup->entity_type);
            $current = DB::table($table)->where('id', $backup->entity_id)->first();

            if (! $current) {
                throw new RuntimeException(
                    "Cannot rollback inventory cost correction: {$backup->entity_type} {$backup->entity_id} is missing."
                );
            }

            $correctedValues = json_decode($backup->corrected_values, true, 512, JSON_THROW_ON_ERROR);

            if (! $this->valuesMatch($current, $correctedValues)) {
                throw new RuntimeException(
                    "Cannot rollback inventory cost correction: {$backup->entity_type} {$backup->entity_id} changed after migration."
                );
            }
        }

        DB::transaction(function () use ($backups) {
            foreach ($backups as $backup) {
                DB::table($this->tableForEntity($backup->entity_type))
                    ->where('id', $backup->entity_id)
                    ->update(json_decode($backup->original_values, true, 512, JSON_THROW_ON_ERROR));
            }
        }, 3);

        Schema::drop(self::BACKUP_TABLE);
    }

    private function loadStockTargets(): array
    {
        $allStocks = DB::table('inventory_stocks')
            ->lockForUpdate()
            ->get();
        $activeStocks = $allStocks->filter(
            fn ($stock) => (float) ($stock->amount ?? 0) > 0 || (float) ($stock->weight ?? 0) > 0
        )->values();
        $inventoryIns = DB::table('inventory_ins')
            ->select('destination_stock_id', 'product_id', 'po_stock_id', 'purchase_cost_per_unit')
            ->lockForUpdate()
            ->get();

        $directSources = $inventoryIns->whereNotNull('destination_stock_id')->groupBy('destination_stock_id');
        $poSources = $inventoryIns
            ->whereNotNull('po_stock_id')
            ->groupBy(fn ($row) => $row->product_id.':'.$row->po_stock_id);
        $targets = [];
        $canonicalCostsByStock = [];

        foreach ($allStocks as $stock) {
            $sources = collect($directSources->get($stock->id, collect()));

            if (! is_null($stock->po_stock_id)) {
                $sources = $sources->concat(
                    $poSources->get($stock->product_id.':'.$stock->po_stock_id, collect())
                );
            }

            $canonicalCosts = $sources
                ->map(fn ($source) => (float) $source->purchase_cost_per_unit)
                ->unique(fn ($cost) => number_format($cost, 6, '.', ''))
                ->values();

            if ($canonicalCosts->count() > 1) {
                throw new RuntimeException(
                    "Inventory stock {$stock->id} has ambiguous inventory-in unit costs."
                );
            }

            if ($canonicalCosts->isEmpty()) {
                continue;
            }

            $canonicalCost = (float) $canonicalCosts->first();
            $canonicalCostsByStock[(int) $stock->id] = $canonicalCost;

            $isActive = (float) ($stock->amount ?? 0) > 0 || (float) ($stock->weight ?? 0) > 0;

            if (! $isActive
                || abs((float) $stock->purchase_cost_per_unit - $canonicalCost) <= self::COST_EPSILON) {
                continue;
            }

            $quantity = (int) $stock->unit_id === 1
                ? (float) ($stock->weight ?? 0)
                : (float) ($stock->amount ?? 0);

            if ($quantity <= 0) {
                throw new RuntimeException("Inventory stock {$stock->id} has no positive active quantity.");
            }

            $targets[(int) $stock->id] = [
                'stock' => $stock,
                'product_id' => (int) $stock->product_id,
                'quantity' => $quantity,
                'canonical_cost' => $canonicalCost,
            ];
        }

        return [$activeStocks, $targets, $canonicalCostsByStock];
    }

    private function assertPoDetailsAreUnique(): void
    {
        $duplicate = DB::table('po_stock_product')
            ->select('po_stock_id', 'product_id')
            ->groupBy('po_stock_id', 'product_id')
            ->havingRaw('COUNT(*) > 1')
            ->first();

        if ($duplicate) {
            throw new RuntimeException(
                "PO {$duplicate->po_stock_id} has duplicate detail rows for product {$duplicate->product_id}."
            );
        }
    }

    private function loadPoReferenceTargets()
    {
        return DB::table('inventory_stocks as stock')
            ->join('po_stock_product as detail', function ($join) {
                $join->on('detail.po_stock_id', '=', 'stock.po_stock_id')
                    ->on('detail.product_id', '=', 'stock.product_id');
            })
            ->select([
                'stock.id',
                'stock.purchase_cost',
                'stock.purchase_cost_per_unit',
                'stock.updated_at',
                'detail.qty as po_quantity',
                'detail.purchase_cost as po_unit_cost',
            ])
            ->lockForUpdate()
            ->get();
    }

    private function applyPoReferenceCostCorrection($targets, string $now): void
    {
        foreach ($targets as $target) {
            $corrected = $this->poReferenceCorrectedValues($target, $now);

            if ($this->costValuesMatch($target, $corrected)) {
                continue;
            }

            $existingStockBackup = DB::table(self::BACKUP_TABLE)
                ->where('entity_type', 'inventory_stock')
                ->where('entity_id', $target->id)
                ->first();

            if ($existingStockBackup) {
                DB::table(self::BACKUP_TABLE)
                    ->where('id', $existingStockBackup->id)
                    ->update([
                        'corrected_values' => json_encode($corrected, JSON_THROW_ON_ERROR),
                    ]);
            } else {
                $this->backup('inventory_stock_reference', (int) $target->id, [
                    'purchase_cost' => $target->purchase_cost,
                    'purchase_cost_per_unit' => $target->purchase_cost_per_unit,
                    'updated_at' => $target->updated_at,
                ], $corrected, $now);
            }

            DB::table('inventory_stocks')->where('id', $target->id)->update($corrected);
        }
    }

    private function poReferenceCorrectedValues(object $target, string $updatedAt): array
    {
        $unitCost = (float) $target->po_unit_cost;

        return [
            'purchase_cost' => round($unitCost * (float) $target->po_quantity, 2),
            'purchase_cost_per_unit' => $unitCost,
            'updated_at' => $updatedAt,
        ];
    }

    private function validatePoReferenceCostCorrection($targets): void
    {
        foreach ($targets as $target) {
            $stock = DB::table('inventory_stocks')->where('id', $target->id)->first();
            $corrected = $this->poReferenceCorrectedValues($target, $stock?->updated_at ?? '');

            if (! $stock || ! $this->costValuesMatch($stock, $corrected)) {
                throw new RuntimeException("Inventory stock {$target->id} failed PO reference cost validation.");
            }
        }
    }

    private function costValuesMatch(object $current, array $expected): bool
    {
        return abs((float) $current->purchase_cost - (float) $expected['purchase_cost']) <= self::COST_EPSILON
            && abs((float) $current->purchase_cost_per_unit - (float) $expected['purchase_cost_per_unit']) <= self::COST_EPSILON;
    }

    private function validateCorrection(
        array $targets,
        array $canonicalCosts,
        $freeSamples,
        $jobStatements,
        array $productIds
    ): void {
        foreach ($targets as $stockId => $target) {
            $stock = DB::table('inventory_stocks')->where('id', $stockId)->first();
            $expectedTotal = $target['canonical_cost'] * $target['quantity'];

            if (! $stock
                || abs((float) $stock->purchase_cost_per_unit - $target['canonical_cost']) > self::COST_EPSILON
                || abs((float) $stock->purchase_cost - $expectedTotal) > self::COST_EPSILON) {
                throw new RuntimeException("Inventory stock {$stockId} failed post-correction validation.");
            }
        }

        foreach ($freeSamples as $source) {
            $row = DB::table('free_samples')->where('id', $source->id)->first();
            $canonicalCost = $canonicalCosts[$source->inventory_stock_id];

            if (! $row
                || abs((float) $row->purchase_cost_per_unit - round($canonicalCost, 2)) > self::COST_EPSILON
                || abs((float) $row->purchase_cost - round($canonicalCost * (float) $row->quantity, 2)) > self::COST_EPSILON) {
                throw new RuntimeException("Free sample {$source->id} failed post-correction validation.");
            }
        }

        foreach ($jobStatements as $source) {
            $row = DB::table('job_statements')->where('id', $source->id)->first();
            $canonicalCost = $canonicalCosts[$source->inventory_stock_id];

            if (! $row
                || abs((float) $row->stock_cost_per_unit - $canonicalCost) > self::COST_EPSILON
                || abs((float) $row->stock_cost - ($canonicalCost * (float) $row->quantity)) > self::COST_EPSILON) {
                throw new RuntimeException("Job statement {$source->id} failed post-correction validation.");
            }
        }

        foreach ($productIds as $productId) {
            $product = DB::table('products')->where('id', $productId)->first();
            $highestCost = (float) DB::table('inventory_stocks')
                ->where('product_id', $productId)
                ->where(function ($query) {
                    $query->where('amount', '>', 0)->orWhere('weight', '>', 0);
                })
                ->max('purchase_cost_per_unit');

            if (! $product || abs((float) $product->harga_tertinggi - $highestCost) > self::COST_EPSILON) {
                throw new RuntimeException("Product {$productId} failed highest-price validation.");
            }
        }
    }

    private function backup(
        string $entityType,
        int $entityId,
        array $originalValues,
        array $correctedValues,
        string $createdAt
    ): void {
        DB::table(self::BACKUP_TABLE)->insert([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'original_values' => json_encode($originalValues, JSON_THROW_ON_ERROR),
            'corrected_values' => json_encode($correctedValues, JSON_THROW_ON_ERROR),
            'created_at' => $createdAt,
        ]);
    }

    private function tableForEntity(string $entityType): string
    {
        return match ($entityType) {
            'inventory_stock' => 'inventory_stocks',
            'inventory_stock_reference' => 'inventory_stocks',
            'free_sample' => 'free_samples',
            'job_statement' => 'job_statements',
            'product' => 'products',
            default => throw new RuntimeException("Unknown inventory cost backup entity type: {$entityType}."),
        };
    }

    private function valuesMatch(object $current, array $expected): bool
    {
        foreach ($expected as $field => $expectedValue) {
            $currentValue = $current->{$field};

            if (is_numeric($expectedValue) && is_numeric($currentValue)) {
                if (abs((float) $currentValue - (float) $expectedValue) > 0.0001) {
                    return false;
                }

                continue;
            }

            if ($currentValue !== $expectedValue) {
                return false;
            }
        }

        return true;
    }
};
