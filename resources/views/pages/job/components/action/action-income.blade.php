<div class="d-flex my-2 justify-content-center">
    @if(App\Models\Job::find($job_id)->status_payment === 'open')
        @can('edit income job')
            <button class="btn btn-warning btn-sm waves-effect waves-light mr-2 btn-edit-income" type="button"
                data-toggle="modal" data-target="#editIncomeModal" data-date="{{ $date }}"
                data-url="{{ route('job_income.update', $id) }}"
                data-bank-account-id="{{ $bank_account_id }}" data-payment="{{ $payment }}"
                data-nominal="{{ $nominal }}" data-to-idr="{{ $to_idr }}">
                <i class="fas fa-pen-alt"></i></button>
        @endcan

        @can('delete income job')
            <form action="{{ route('job_income.destroy', $id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-danger btn-sm waves-effect waves-light"
                    onclick="saDelete($(this))">
                    <i class="fas fa-trash-alt"></i></button>
            </form>
        @endcan
    @endif
</div>

<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/sweet-alerts.init.js') }}"></script>

<script>
    $(document).ready(function () {
        $('.btn-edit-income').click(function (e) {
            let data = $(this).data();

            $('#form-income-edit').attr('action', data.url);
            $('#edit-datetime').val(data.date.slice(0, -3));
            $(`#edit-bank-account option[value="${data.bankAccountId}"]`).prop('selected', true);
            $('#edit-nominal-income').autoNumeric('set', data.payment);
            $('#edit-convertion').autoNumeric('set', data.nominal);

            $('#edit-bank-account').trigger('change');
        });

    });

</script>
