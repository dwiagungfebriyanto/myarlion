<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <h4 class="header-title" style="font-size: 12px">SALES COMMISSION</h4>

            <div class="row">
                <div class="col-md">
                    @if($job->statusIsOpen() && auth()->user()->can('job statement add sales commission'))
                        <button type="button" class="btn btn-success waves-effect waves-light btn-add mr-2 my-2"
                            data-toggle="modal" data-target="#addSalesCommission">
                            <i class="fas fa-plus-square mr-1"></i> Add Commission</button>

                        @include('pages.job_statement.components.modals.add_commission_modal')
                    @endif
                </div>
            </div>

            <table class="table">
                <thead>
                    <th>No.</th>
                    <th>Release Date</th>
                    <th>Employee</th>
                    <th>Percentage</th>
                    <th>Nominal</th>
                    <th>Action</th>
                </thead>

                <tbody>
                    @foreach($commissions as $commission)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $commission->release_date }}</td>
                            <td>{{ $commission->employee->name }}</td>
                            <td>{{ "$commission->percentage%" }}</td>
                            <td>{{ currencyFormat($commission->nominal) }}</td>
                            <td>
                                @if($job->statusIsOpen())
                                    @include('pages.job_statement.components.action_button.commission_action_button')
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    <tr>
                        <td colspan="6" class="text-right">
                            <span class="total-label">TOTAL COMMISSION:
                                {{ currencyFormat($totalCommission) }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        {{-- /.table-responsive --}}
    </div>
    {{-- /.col-md-12 --}}
</div>
{{-- /.row --}}

<hr class="mb-4">

@include('pages.job_statement.components.modals.edit_commission_modal')


@push('scripts')
<script>
    $(function () {
        $('.btn-edit-commission').click(function (e) {
            let data = $(this).data();

            $('#editCommissionForm').attr('action', data.url);
            $('#edit-release-date').val(data.date);
            $('#editCommissionForm #edit-marketing').val(data.marketing).trigger('change');
            $('#edit-percentage').autoNumeric('set', data.percentage);
            $('#editCommissionForm #edit-nominal').autoNumeric('set', data.nominal);
            $('#editCommissionForm #edit-note').val(data.note);
        });
    });
</script>
@endpush
