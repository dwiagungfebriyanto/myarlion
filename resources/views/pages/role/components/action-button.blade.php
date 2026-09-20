<div class="button-list">

    <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-info btn-detail" data-toggle="modal"
        data-target="#detailModal" data-url="{{ route('role.show', $id) }}">
        <i class="fas fa-eye "></i></button>

    @can('edit role')
    <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-warning btn-edit" data-toggle="modal"
        data-target="#editModal" data-url="{{ route('role.edit', $id) }}">
        <i class="fas fa-pen-alt"></i></button>
    @endcan

    @can('delete role')
    <form action="{{ route('role.destroy', $id) }}" method="post" style="display:inline">
        @csrf
        @method('delete')

        <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-danger btn-delete">
            <i class="fas fa-trash-alt"></i>
        </button>
    </form>
    @endcan
</div>

<script>
    $(document).ready(function () {
        $('.btn-detail').click(function (e) {
            $.ajax({
                type: 'get',
                url: $(this).data('url'),
                success: function (response) {
                    // Handle success response
                    $('#detail-role-name').text(response.role.name);
                    $('#detail-permissions').html(response.rolePermissions);
                },
                error: function (xhr, status, error) {
                    // Handle error response
                    errorToast({
                        heading: 'Something went wrong!',
                        text: 'Failed to get data!',
                    })
                }
            });
        });

        $('.btn-edit').click(function (e) {
            $.ajax({
                type: "get",
                url: $(this).data('url'),
                success: function (response) {
                    let indexUrl = $('#form-edit').data('url');

                    $('#form-edit').attr('action', indexUrl + '/' + response.role.id)
                    $('#edit-role').val(response.role.name)
                    $('#edit-permission').html(response.permissionOptions)
                }
            });
        });

        $(".btn-delete").click(function () {
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
                        type: 'POST',
                        url: element.parent().attr('action'),
                        data: element.parent().serialize(),
                        success: function (data) {
                            // Handle success response
                            $('#role-datatable').DataTable().ajax.reload();

                            $.toast({
                                heading: "Success!",
                                text: 'Role data successfully deleted.',
                                position: "top-right",
                                loaderBg: "#5ba035",
                                icon: "success",
                                hideAfter: 3e3,
                                stack: 1
                            })
                        },
                        error: function (xhr, status, error) {
                            // Handle error response
                            errorToast({
                                heading: "Something went wrong!",
                                text: 'Permission data failed to delete.',
                            })
                        }
                    });
                }
            })
        })

        function errorToast(params) {
            $.toast({
                heading: params.heading,
                text: params.text,
                position: "top-right",
                loaderBg: "#bf441d",
                icon: "error",
                hideAfter: 3e3,
                stack: 1
            })
        }

    });

</script>
