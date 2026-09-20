<div class="button-list">
    @can('edit warehouse')
    <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-warning btn-edit" data-toggle="modal"
        data-target="#editModal" data-url="{{ route('warehouse.edit', $id) }}">
        <i class="fas fa-pen-alt"></i></button>
    @endcan
</div>

<script>
    $(document).ready(function () {

        $('.btn-edit').click(function (e) {
            $.ajax({
                type: "get",
                url: $(this).data('url'),
                success: function (response) {
                    let warehouse = response.warehouse;

                    let currentText = $('#edit-status-description').text();

                    if (warehouse.status !== currentText) {
                        $('#edit-status').click();
                    }

                    $('#form-edit').attr('action', response.actionUrl);
                    $('#edit-warehouse-id').val(warehouse.id);
                    $('#edit-warehouse-name').val(warehouse.warehouse_name);
                    $('#edit-status-description').text(warehouse.status);
                    $('#edit-address').val(warehouse.address);
                }
            });
        });

    });

</script>
