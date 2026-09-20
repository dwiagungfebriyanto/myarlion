<div class="d-flex my-2 justify-content-start">
    <button type="button" class="btn btn-info btn-sm waves-effect waves-light mr-2 btn-detail" data-id=""
        data-toggle="modal" data-target="#viewModal"
        data-url="{{ route('vendors.show', $id) }}"><i class="fas fa-eye"></i></button>

    @can('edit vendor')
        <button class="btn btn-warning btn-sm waves-effect waves-light mr-2 btn-edit" type="button" data-toggle="modal"
            data-id="{{ $id }}" data-jenis="edit" data-target="#editModal"
            data-url="{{ route('vendors.edit', $id) }}"><i class="fas fa-pen-alt"></i></button>
    @endcan

    @can('delete vendor')
        <form action="{{ route('vendors.destroy', $id) }}" method="POST">
            @csrf
            @method('delete')
            <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" onclick="saDelete(this)">
                <i class="fas fa-trash-alt"></i></button>
        </form>
    @endcan
</div>

<script>
    $(document).ready(function () {
        $('.btn-edit').off('click').on('click', function (e) {
            let data = $(this).data();

            $.ajax({
                method: "get",
                url: data.url,
                success: function (response) {
                    $('#editModal').find('.modal-dialog').html(response);
                }
            })
        });

        $('.btn-detail').click(function (e) {
            $.ajax({
                type: "get",
                url: $(this).data('url'),
                dataType: "json",
                success: function (response) {

                    let address = (response.vendor.address !== null) ?
                        response.vendor.address :
                        '-';
                    let telp = (response.vendor.telp !== null) ?
                        response.vendor.telp :
                        '-';
                    let fax = (response.vendor.fax !== null) ?
                        response.vendor.fax :
                        '-';
                    let email = (response.vendor.email !== null) ?
                        response.vendor.email :
                        '-';
                    let contact = (response.vendor.contact !== null) ?
                        response.vendor.contact :
                        '-';
                    let note = (response.vendor.note !== null) ?
                        response.vendor.note :
                        '-';

                    let pkp = (response.vendor.pkp !== null) ? response.vendor.pkp :
                        '-';

                    let no_rekening = (response.vendor.no_rekening !== null) ?
                        response.vendor.no_rekening :
                        '-';
                    $('.name').text(response.vendor.vendor_name);
                    $('.address').text(address);
                    $('.telp').text(telp);
                    $('.fax').text(fax);
                    $('.email').text(email);
                    $('.contact').text(contact);
                    $('.pkp').text(pkp);
                    $('.no_rekening').text(no_rekening);
                    $('.note').text(note);
                }
            });
        });

    });

</script>
