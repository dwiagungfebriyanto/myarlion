<div class="button-list">

    @can('edit PO asset')
    <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-warning btn-edit" data-toggle="modal"
        data-target="#editModal" data-url="{{ route('accounting.asset.edit', $id) }}">
        <i class="fas fa-pen-alt"></i></button>
    @endcan

    @can('delete PO asset')
    <form action="{{ route('accounting.asset.destroy', $id) }}" method="post"
        style="display:inline">
        @csrf
        @method('delete')

        <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-danger sa-warning">
            <i class="fas fa-trash-alt"></i>
        </button>
    </form>
    @endcan
</div>

<script>
    $(document).ready(function () {
        $('.btn-edit').click(function (e) {
            let url = $(this).data('url');

            $.ajax({
                type: "get",
                url: url,
                success: function (response) {
                    const poAsset = response.poAsset;

                    $('#form-edit').attr('action', response.actionUrl)
                    $('[name="id_record"]').val(poAsset.id)
                    $('#edit-id').val(poAsset.unique_id.replace('ASS', ''))
                    $('#edit-supplier').html(response.supplierOptions)
                    $('#edit-note').html(poAsset.note)
                }
            });
        });

        $(".sa-warning").click(function () {
            let element = $(this);

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                type: "warning",
                showCancelButton: !0,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then(function (t) {
                if (t.value === true) {
                    $.ajax({
                        type: "POST",
                        url: element.parent().attr('action'),
                        data: element.parent().serialize(),
                        success: function (response) {
                            $('#po-asset-datatable').DataTable().ajax.reload();

                            $.toast({
                                heading  : "Success!",
                                text     : 'PO Asset data successfully deleted.',
                                position : "top-right",
                                loaderBg : "#5ba035",
                                icon     : "success",
                                hideAfter: 3e3,
                                stack    : 1
                            })
                        },
                        error: function (xhr, status, error) {
                            // Handle error response
                            let response = xhr.responseJSON;

                            $.toast({
                                heading  : "Something went wrong!",
                                text     : "Failed to delete data.",
                                position : "top-right",
                                loaderBg : "#bf441d",
                                icon     : "error",
                                hideAfter: 3e3,
                                stack    : 1
                            })
                        }
                    });
                }
            })
        })

    });

</script>
