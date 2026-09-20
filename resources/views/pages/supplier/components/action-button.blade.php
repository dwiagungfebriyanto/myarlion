<div class="d-flex my-2 justify-content-start">
    <button type="button" class="btn btn-info btn-sm waves-effect waves-light mr-2 btn-detail" data-id=""
        data-toggle="modal" data-target="#viewModal"
        data-url="{{ route('supplier.show', $id) }}">
        <i class="fas fa-eye"></i></button>

    @can('edit supplier')
        <button class="btn btn-warning btn-sm waves-effect waves-light mr-2 btn-edit" type="button" data-toggle="modal"
            data-id="{{ $id }}" data-jenis="edit" data-target="#editModal"
            data-url="{{ route('supplier.edit', $id) }}">
            <i class="fas fa-pen-alt"></i></button>
    @endcan

    {{-- <form action="{{ route('supplier.destroy', $id) }}"
    method="POST">
    @csrf
    @method('DELETE')
    <button type="button" class="btn btn-danger btn-sm waves-effect waves-light sa-warning"><i
            class="fas fa-trash-alt"></i></button>
    </form> --}}
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
                    let main_category = response.main_category.id + ' - ' + response
                        .main_category
                        .main_category_name;

                    let address = (response.supplier.address !== null) ?
                        response.supplier.address :
                        '-';
                    let telp = (response.supplier.telp !== null) ?
                        response.supplier.telp :
                        '-';
                    let fax = (response.supplier.fax !== null) ?
                        response.supplier.fax :
                        '-';
                    let email = (response.supplier.email !== null) ?
                        response.supplier.email :
                        '-';
                    let contact = (response.supplier.contact !== null) ?
                        response.supplier.contact :
                        '-';
                    let note = (response.supplier.note !== null) ?
                        response.supplier.note :
                        '-';

                    let pkp = (response.supplier.pkp !== null) ? response.supplier.pkp :
                        '-';

                    let no_rekening = (response.supplier.no_rekening !== null) ?
                        response.supplier.no_rekening :
                        '-';

                    $('.main_category').text(main_category);
                    $('.name').text(response.supplier.supplier_name);
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

        // $(".sa-warning").click(function () {
        //     let element = $(this);

        //     Swal.fire({
        //         title: "Are you sure?",
        //         text: "You won't be able to revert this!",
        //         type: "warning",
        //         showCancelButton: !0,
        //         confirmButtonColor: "#3085d6",
        //         cancelButtonColor: "#d33",
        //         confirmButtonText: "Yes, delete it!"
        //     }).then(function (t) {
        //         if (t.value === true) {
        //             element.parent().submit()
        //         }
        //     })
        // })

    });

</script>
