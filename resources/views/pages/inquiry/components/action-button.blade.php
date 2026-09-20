<div class="d-flex my-2 justify-content-start">

    @can('edit inquiry')
        <a href="{{ route('inquiry.edit', $id) }}" class="btn btn-warning btn-sm waves-effect waves-light mr-2"><i
                class="fas fa-pen-alt"></i></a>
    @endcan

    @if ($status != 'sales')
        @can('delete inquiry')
            <form action="{{ route('inquiry.destroy', $id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-danger btn-sm waves-effect waves-light sa-warning"><i
                        class="fas fa-trash-alt"></i></button>
            </form>
        @endcan
    @endif


</div>

<script>
    $(document).ready(function() {

        $('.btn-edit').off('click').on('click', function(e) {
            let data = $(this).data();

            $.ajax({
                method: "get",
                url: data.url,
                success: function(response) {
                    $('#editModal').find('.modal-dialog').html(response);
                }
            })
        });

        // $('.btn-detail').off('click').on('click', function(e) {
        //     $.ajax({
        //         type: "get",
        //         url: $(this).data('url'),
        //         dataType: "json",
        //         success: function(response) {
        //             console.log(response);

        //             let note = (response.inquiry.note !== null) ? response.inquiry.note :
        //                 '-';
        //             $('#note').text(note);

        //             let country = response.country.country_code + ' - ' + response.country
        //                 .country_name;
        //             $('#country').text(country);

        //             $('#city').text(response.inquiry.city);

        //             let channel = response.channel.channel_name;
        //             $('#channel').text(channel);

        //             let user = response.user.name;
        //             $('#user').text(user);

        //             $('#name').text(response.inquiry.name);
        //             $('#phone').text(response.inquiry.phone);
        //             $('#email').text(response.inquiry.email);
        //             $('#date').text(response.date);

        //             if ($('#product').find('ul').children().length > 0) {
        //                 $('#product').find('ul').empty();
        //                 $.each(response.products, function(index, value) {
        //                     $('#product').find('ul').append("<li>" + value
        //                         .product_type
        //                         .product_type_name + "</li>");
        //                 });
        //             } else {
        //                 $.each(response.products, function(index, value) {
        //                     $('#product').find('ul').append("<li>" + value
        //                         .product_type.product_type_name + "</li>");
        //                 });
        //             }
        //         }
        //     });
        // });



        $(".sa-warning").click(function() {
            let element = $(this);

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                type: "warning",
                showCancelButton: !0,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then(function(t) {
                if (t.value === true) {
                    element.parent().submit()
                }
            })
        })

    });
</script>
