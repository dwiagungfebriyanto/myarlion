<div class="col-sm-12 col-md-6">
    <div class="bg-white pl-3 pr-3 pb-1">
        <div class="timeline" dir="ltr">
            <article class="timeline-item alt">
                <div class="text-right">
                    <div class="time-show first">
                        <a href="#" class="btn btn-primary width-lg" style="font-weight: bold">
                            History <br>
                            PO {{ ucfirst($poStock->po_type) }} <i class="fas fa-arrow-right"></i> Inventory <br>
                            @php
                                $totalAmount = $toInventoryInHistories->sum('amount');
                                $totalWeight = $toInventoryInHistories->sum('weight');
                                $grandTotal = $totalAmount + $totalWeight;

                                echo "(total: $grandTotal)";
                            @endphp
                        </a>
                    </div>
                </div>
            </article>

            @if(count($toInventoryInHistories) > 0)
                @php
                    $leftPosition = true;
                @endphp
                @foreach($toInventoryInHistories as $history)
                    @php
                        $unitIsKg = $history->unit_id == 1;
                        $unitName = $unitIsKg ? 'kg' : 'pcs';
                        $unitValue = $unitIsKg ? 'weight' : 'amount';
                    @endphp

                    <article
                        class="timeline-item {{ $leftPosition ? 'alt' : '' }}">
                        <div class="timeline-desk">
                            <div class="panel">
                                <div class="timeline-box">
                                    <span
                                        class="arrow{{ $leftPosition ? '-alt' : '' }}"></span>

                                    <span class="timeline-icon bg-primary">
                                        <i class="mdi mdi-adjust"></i></span>

                                    <h4 class="text-primary">
                                        {{ Illuminate\Support\Carbon::parse($history->datetime)->diffForHumans() }}
                                    </h4>

                                    <p class="timeline-date text-muted">
                                        <small>{{ $history->datetime }}</small></p>
                                    <p>
                                        <ul style="font-weight: bold">
                                            <li>
                                                <a href="{{ route('inventory_in.history', $history) }}"
                                                    class="text-warning" style="text-decoration: underline">
                                                    {{ camelCaseToSpaces(class_basename($history)) }}
                                                </a>
                                            </li>

                                            <li>
                                                <span class="text-success">Warehouse:</span>
                                                {{ $history->warehouse->warehouse_name }}
                                            </li>

                                            <li>
                                                <span class="text-success">Product:</span>
                                                {{ $history->product->skuFormat() }}
                                            </li>

                                            <li>
                                                <span class="text-success">Quantity:</span>
                                                {{ $history->$unitValue ." $unitName" }}
                                            </li>

                                            <li>
                                                <span class="text-success">Status:</span>
                                                {{ $history->status }}
                                            </li>

                                            <li>
                                                <span class="text-success">Note:</span>
                                                {{ $history->notes }}
                                            </li>
                                            </li>

                                            <li>
                                                <span class="text-success">Stock:</span>
                                                {{ $history->inventoryStock->$unitValue ." $unitName" }}
                                            </li>
                                        </ul>
                                    </p>
                                </div>
                                {{-- /.timeline-box --}}
                            </div>
                            {{-- /.panel --}}
                        </div>
                        {{-- /.timeline-desk --}}
                    </article>

                    @php
                        $leftPosition = !$leftPosition;
                    @endphp
                @endforeach
            @else
                <article class="timeline-item alt">
                    <div class="timeline-desk">
                        <div class="panel">
                            <div class="timeline-box">
                                <span class="arrow-alt"></span>
                                <span class="timeline-icon bg-primary"><i class="mdi mdi-adjust"></i></span>
                                <p>No history.</p>
                            </div>
                        </div>
                    </div>
                </article>
            @endif
        </div>
        {{-- /.timeline --}}
    </div>
</div>
