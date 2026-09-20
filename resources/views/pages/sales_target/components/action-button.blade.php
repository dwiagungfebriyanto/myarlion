<div class="button-list">
    @can('edit sales target')
        <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-warning btn-edit"
            data-toggle="modal" data-target="#editModal"
            data-url="{{ route('sales-target.edit', $id) }}">
            <i class="fas fa-pen-alt"></i></button>
    @endcan

    @can('delete sales target')
        <form action="{{ route('sales-target.destroy', $id) }}" method="post"
            style="display:inline">
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
        $('.btn-edit').click(function (e) {
            $.ajax({
                type: "get",
                url: $(this).data('url'),
                success: function (response) {
                    let indexUrl = '{{ route("sales-target.index") }}';

                    $('#form-edit').attr('action', indexUrl + '/' + response.id)
                    $('#edit-year').val(response.year)
                    $('#edit-marketing option[value="' + response.user_id + '"]').prop(
                        'selected', true);
                    $('#edit-marketing').trigger('change');
                    $('#edit-target').val(response.target)
                }
            });
        });

        $(".btn-delete").click(function () {
            saDelete($(this))
        })

    });

</script>
