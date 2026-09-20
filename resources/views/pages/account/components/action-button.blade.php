<div class="button-list">

    @if((auth()->user()->getRoleNames()[0] === 'administrator') || (auth()->user()->can('edit account') && auth()->user()->id === $id))
    <a href="{{ route('account.edit', $id) }}"
        class="btn btn-warning btn-sm waves-effect waves-light btn-action" title="Edit">
        <i class="fas fa-pen-alt"></i></a>
    @endif

    @hasrole('administrator')
        <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-blue btn-action btn-permission"
            title="Edit Permission" data-toggle="modal" data-target="#permissionModal"
            data-url="{{ route('account.edit_permission', $id) }}">
            <i class="fas fa-check-double"></i></button>

        <form action="{{ route('account.destroy', $id) }}" method="POST" style="display:inline">
            @csrf
            @method('DELETE')
            <button type="button" class="btn btn-danger btn-sm waves-effect waves-light btn-action sa-warning">
                <i class="fas fa-trash-alt" title="Delete"></i></button>
        </form>
    @endhasrole
</div>

<script>
    $(document).ready(function () {
        $('.btn-permission').click(function (e) {
            $.ajax({
                type: "get",
                url: $(this).data('url'),
                success: function (response) {
                    $('#form-edit-permission').attr('action', response.updatePermissionUrl)
                    $('#form-edit-permission #username').val(response.user.username)
                    $('#edit-permission').html(response.permissionOptions)
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
                    element.parent().submit()
                }
            })
        })

    });

</script>
