@can('add inventory stock')
    @if($inventoryStock->getStockBucket() !== 'sample' && $inventoryStock->product)
        <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-success btn-return"
            data-toggle="modal"
            data-target="#returnModal"
            title="return stock"
            data-id="{{ $inventoryStock->id }}"
            data-warehouse-id="{{ $inventoryStock->warehouse_id }}"
            data-warehouse-name="{{ $inventoryStock->warehouse?->warehouse_name }}"
            data-product="{{ optional($inventoryStock->product)->skuFormat() }}"
            data-stock="{{ optional($inventoryStock)->getStock() }}"
            data-unit="{{ $inventoryStock->product?->unit->unit_name }}"
            data-unit-id="{{ $inventoryStock->product?->unit_id }}"
            data-price="{{ $inventoryStock->purchase_cost_per_unit }}">
            <i class="fas fa-recycle"></i>
        </button>
    @endif
@endcan
