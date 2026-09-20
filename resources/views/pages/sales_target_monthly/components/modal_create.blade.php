<button type="button" class="btn btn-success waves-effect waves-light btn-rounded" data-toggle="modal"
    data-target="#createModal">
    <i class="mdi mdi-plus mr-1"></i> Add New
</button>


<div id="createModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">Add {{ $pageTitle }}</h4>
            </div>

            <div class="modal-body">
                <form id="formAdd" action="{{ route('sales_target_monthly.store') }}"
                    method="post" data-modal="#createModal"
                    data-datatable="#salestargetmonthly-table">
                    @csrf

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="year">Year<span class="text-danger">*</span></label>
                            <input type="month" id="month" class="form-control" placeholder="Month" name="month"
                                value="{{ date('Y-m') }}" required>

                            <span class="text-danger"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="marketing">Marketing<span class="text-danger">*</span></label>
                            <select id="marketing" class="form-control select2" name="marketing" required>
                                {!! selectGenerate('Marketing', $marketings, 'id', 'name') !!}
                            </select>

                            <span class="text-danger"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="target">Sales Target<span class="text-danger">*</span></label>
                            <input type="text" id="target" class="form-control autonumeric" placeholder="Sales target"
                                name="target" required>

                            <span class="text-danger"></span>
                        </div>
                    </div>
                </form>
            </div>
            {{-- /.modal-body --}}

            <div class="modal-footer">
                <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary waves-effect waves-light" form="formAdd">Submit</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->


@push('scripts')
    <script>
        $(function () {
            $('#formAdd').submit(function (e) {
                e.preventDefault();
                submitModalRequest(this);
            });
        });

    </script>
@endpush
