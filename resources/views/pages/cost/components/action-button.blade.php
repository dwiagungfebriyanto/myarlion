<div class="button-list">
    <button type="button" class="btn btn-icon btn-sm waves-effect waves-light btn-info btn-detail" data-toggle="modal"
        data-target="#detailModal" data-url="{{ route('accounting.cost.show', $record_id) }}">
        <i class="fas fa-eye"></i></button>

    @can('edit cost')
        <a href="{{ route('accounting.cost.edit', $record_id) }}"
            class="btn btn-icon btn-sm waves-effect waves-light btn-warning">
            <i class="fas fa-pen-alt"></i>
        </a>
    @endcan

    @can('delete cost')
        <form action="{{ route('accounting.cost.destroy', $record_id) }}" method="post"
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
        $('.btn-detail').click(function (e) {
            let url = $(this).data('url');

            $.ajax({
                type: "get",
                url: url,
                dataType: "json",
                success: function (response) {
                    const responseData = response.data;
                    let cost = response.data.cost;

                    $('#receipt1-detail').html(setReceiptOutput(cost.receipt_file_1));
                    $('#receipt2-detail').html(setReceiptOutput(cost.receipt_file_2));
                    $('#detail-date').text(cost.date);
                    $('#detail-bank-account').text(responseData.bankAccountLabel);
                    $('#detail-category').text(
                        `${responseData.outcomeGroup.name} - ${cost.outcome_type.name}`);
                    // $('#detail-code').text(cost.code_type + ': #' + cost.code);
                    $('#detail-code').text(cost.code_type);
                    $('#detail-amount').text(cost.amount);
                    $('#detail-recipient-type').text(cost.recipient_type);
                    $('#detail-recipient').text(responseData.recipientLabel);
                    $('#detail-note').text(cost.note);

                    if (responseData.poStockLabel) {
                        $('#detail-po-stock').text(responseData.poStockLabel);
                        $('#po-stock-detail').css('display', '');
                    } else {
                        $('#po-stock-detail').css('display', 'none');
                    }
                }
            });
        });

        function setReceiptOutput(params) {
            let output = '';

            if (params !== null) {
                if (params.includes('.pdf')) {
                    output = `<iframe src="/${params}" frameborder="2" width="100%" height="400"></iframe>`;
                } else {
                    output = `<img src="/${params}" class="card-img-top img-fluid" alt="Receipt file"
                                style="border: 2px black solid; border-radius: 2px; max-height: 400px" height="400">`;
                }
            }

            return output;
        }

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
                        type: 'POST',
                        url: element.parent().attr('action'),
                        data: element.parent().serialize(),
                        success: function (data) {
                            // Handle success response
                            $('#cost-datatable').DataTable().ajax.reload();

                            $.toast({
                                heading: "Success!",
                                text: 'Cost data successfully deleted.',
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
                                text: 'Cost data failed to delete.',
                            })
                        }
                    });
                }
            })
        })

    });

</script>
