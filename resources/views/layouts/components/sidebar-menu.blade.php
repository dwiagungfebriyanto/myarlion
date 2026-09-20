<li>
    <a href="{{ route('dashboard') }}">
        <i class="fe-airplay"></i>
        <!-- <span class="badge badge-danger float-right">3</span> -->
        <span> Dashboard </span>
    </a>
</li>


@can('view account')
<li>
    <a href="javascript: void(0);">
        <i class="fe-users"></i>
        <span> Account </span>
        <span class="menu-arrow"></span>
    </a>
    <ul class="nav-second-level nav" aria-expanded="false">
        <li>
            <a href="{{ route('account.index') }}">List</a>
        </li>

        @can('view permission')
        <li>
            <a href="{{ route('permission.index') }}">Permission</a>
        </li>
        @endcan
    </ul>
</li>
@endcan

@can('view product')
<li>
    <a href="javascript: void(0);">
        <i class="mdi mdi-cube"></i>
        <span> Product </span>
        <span class="menu-arrow"></span>
    </a>
    <ul class="nav-second-level nav" aria-expanded="false">
        <li>
            <a href="{{ route('product.list.index') }}">List</a>
        </li>
        <li>
            <a href="{{ route('product.return-stock.index') }}">Return Stock</a>
        </li>

        @can('view inventory')
        <li>
            <a href="javascript: void(0);" aria-expanded="false">Inventory
                <span class="menu-arrow"></span>
            </a>
            <ul class="nav-third-level nav" aria-expanded="false">
                <li>
                    <a href="{{ route('product.inventory-in.index') }}">Inventory In</a>
                </li>
                <li>
                    <a href="{{ route('product.inventory-out.index') }}">Inventory Out</a>
                </li>
                <li>
                    <a href="{{ route('product.inventory-lost.index') }}">Inventory Lost</a>
                </li>
                <li>
                    <a href="{{ route('product.inventory-stock.index') }}">Inventory Stock</a>
                </li>
                <li>
                    <a href="{{ route('product.inventory-mutation.index') }}">Inventory Mutation</a>
                </li>
            </ul>
        </li>
        @endcan

        <li>
            <a href="{{ route('product.main-category.index') }}">Main Category</a>
        </li>
        <li>
            <a href="{{ route('product.sub-category.index') }}">Sub Category</a>
        </li>
        <li>
            <a href="{{ route('product.brand.index') }}">Brand</a>
        </li>
        <li>
            <a href="{{ route('product.product-type.index') }}">Product Type</a>
        </li>
        <li>
            <a href="{{ route('product.specification.index') }}">Specification</a>
        </li>
        <li>
            <a href="{{ route('product.packaging.index') }}">Packaging</a>
        </li>
    </ul>
</li>


<li>
    <a href="javascript: void(0);">
        <i class="mdi mdi-cube-outline"></i>
        <span> Sample </span>
        <span class="menu-arrow"></span>
    </a>
    <ul class="nav-second-level nav" aria-expanded="false">
        <li>
            <a href="{{ route('product.inventory-stock.index', ['type' => 'sample']) }}">Inventory Sample</a>
        </li>
        <li>
            <a href="{{ route('free_sample.index') }}">Free Sample - non Job</a>
        </li>
    </ul>
</li>
@endcan



@can('view supplier')
<li>
    <a href="{{ route('supplier.index') }}">
        <i class="mdi mdi-account-supervisor"></i>
        <span> Supplier </span>
    </a>
</li>
@endcan

@can('view vendor')
<li>
    <a href="{{ route('vendors.index') }}">
        <i class="far fa-handshake"></i>
        <span> Vendor </span>
    </a>
</li>
@endcan

@can('view inquiry')
<li>
    <a href="{{ route('inquiry.index') }}">
        <i class="mdi mdi-file-document"></i>
        <span> Inquiry </span>
    </a>
</li>
@endcan

@can('view customer')
<li>
    <a href="{{ route('customer.index') }}">
        <i class=" mdi mdi-account-group"></i>
        <span> Customer </span>
    </a>
</li>
@endcan

@can('view job')
<li>
    <a href="javascript: void(0);">
        <i class="mdi mdi-notebook-multiple"></i>
        <span> Job </span>
        <span class="menu-arrow"></span>
    </a>
    <ul class="nav-second-level" aria-expanded="false">
        <li><a href="{{ route('job.list.index') }}">List</a></li>
        <li><a href="{{ route('job.edit_amount_approval.index') }}">Edit Amount Approval</a></li>
    </ul>
</li>
@endcan

{{-- <li>
    <a href="{{ route('shipment.index') }}">
        <i class="fas fa-shipping-fast"></i>
        <span> Shipment </span>
    </a>
</li> --}}

@if (auth()->user()->hasRole('accounting') || auth()->user()->can('view accounting'))
<li>
    <a href="javascript: void(0);">
        <i class="mdi mdi-finance"></i>
        <span> Accounting </span>
        <span class="menu-arrow"></span>
    </a>
    <ul class="nav-second-level" aria-expanded="false">
        <li><a href="{{ route('accounting.asset.index') }}">PO Asset</a></li>
        <li><a href="{{ route('purchase_orders.index') }}">PO Stock - Sample</a></li>
        <li><a href="{{ route('accounting.cost.index') }}">Cost</a></li>
        <li><a href="{{ route('other_income.index') }}">Other Income</a></li>
        <li><a href="{{ route('accounting.income_statement.index') }}">Income Statement</a></li>
        <li><a href="{{ route('sales-target.index') }}">Sales Target | Yearly</a></li>
        <li><a href="{{ route('sales_target_monthly.index') }}">Sales Target | Monthly</a></li>
    </ul>
</li>
@endif


@can('view warehouse')
<li>
    <a href="{{ route('warehouse.index') }}">
        <i class="mdi mdi-factory"></i>
        <span> Warehouse </span>
    </a>
</li>
@endcan

@role('administrator')
<li>
    <a href="{{ route('system.index') }}">
        <i class="mdi mdi-settings-outline "></i>
        <span> System </span>
    </a>
</li>
@endrole

<li>
    <a href="https://drive.google.com/drive/folders/1krEUsS_ocnFKc8DSkWq71cHbE5sFhHRb?usp=sharing" target="_blank" rel="noopener">
        <i class="mdi mdi-file-document-outline"></i>
        <span> Documentation </span>
    </a>
</li>


@push('scripts')
    <script>
        $(function () {
            $('#side-menu a').each(function (index, element) {
                if (window.location.href == $(this).attr('href')) {
                    $(this).parent('li').addClass('mm-active');
                } else {
                    $(this).parent('li').removeClass('mm-active');
                }
            });
            
            var currentUrl = window.location.href;

            if (currentUrl.includes('/product/inventory-stock') && currentUrl.includes('type=sample')) {
                $('li').removeClass('mm-active');
                $('ul').removeClass('mm-show');
                $('a[href*="inventory-stock"][href*="type=sample"]').parent('li').addClass('mm-active');
                $('a[href*="inventory-stock"][href*="type=sample"]').closest('ul').addClass('mm-show');
                $('a[href*="inventory-stock"][href*="type=sample"]').closest('li').parent('ul').addClass('mm-show');

                $('a[href*="inventory-stock"][href*="type=sample"]').closest('ul').parent('li').addClass('mm-active');
            }
        });
    </script>
@endpush
