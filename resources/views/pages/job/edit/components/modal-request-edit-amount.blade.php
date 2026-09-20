<div id="requestEditAmountModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="requestEditAmountLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestEditAmountLabel">Request Edit PI Amount</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="requestEditAmountForm" class="form-parsley"
                    action="{{ route('api.edit_amount_approval.store', $job) }}" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="current_amount">Current Amount</label>
                        <input type="text" class="form-control" id="current_amount"
                            value="{{ currencyFormat($job->amount) }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="request_amount">Request Amount <span class="text-danger">*</span></label>
                        <input type="text" class="form-control autoNumeric" id="request_amount" name="request_amount"
                            required>

                        <span class="text-danger errorMessage"></span>
                    </div>

                    <div class="form-group">
                        <label for="note">Note <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="note" name="note" required></textarea>
                        
                        <span class="text-danger errorMessage"></span>
                    </div>

                    <input type="hidden" name="requester_id" value="{{ auth()->id() }}">
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" form="requestEditAmountForm" class="btn btn-primary">Request</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(function () {
            $('#requestEditAmountForm').submit(function (e) {
                e.preventDefault();
                const requestForm = $(this);
                
                setAutoNumericRawValue();

                $.ajax({
                    type: "post",
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            toastSuccess(response.message);

                            requestForm.find('.errorMessage').html('');
                            $('button[form="requestEditAmountForm"]').attr('disabled', true);

                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            toastDanger(response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        // Handle error response
                        let response = xhr.responseJSON || {};

                        requestForm.find('.errorMessage').html('');

                        if (response.errors) {
                            $.each(response.errors, function (key, value) {
                                requestForm.find(`[name='${key}']`).siblings('span.text-danger')
                                    .html(`<i class="fa fa-times-circle"></i> ${value[0]}`);
                            });
                        }

                        toastDanger(response.message);
                    }
                });
            });
        });

    </script>
@endpush
