<div class="button-list">
    <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-info btn-detail" data-toggle="modal"
        data-target="#detailModal"
        onclick="getDetailData('{{ route('product.inventory-out.show', $inventoryOut) }}')">
        <i class="fas fa-eye"></i></button>

    @can('edit inventory out')
        <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-warning btn-edit"
            data-toggle="modal" data-target="#editModal"
            onclick="getEditData('{{ route('product.inventory-out.edit', $inventoryOut) }}')">
            <i class="fas fa-pen-alt"></i></button>
    @endcan

    @can('delete inventory out')
        <form action="{{ route('product.inventory-out.destroy', $inventoryOut->id) }}"
            id="form-delete" method="post" style="display:inline">
            @csrf
            @method('delete')
            <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-danger sa-warning"
                onclick="saDelete(this)">
                <i class="fas fa-trash-alt"></i>
            </button>
        </form>
    @endcan
</div>
