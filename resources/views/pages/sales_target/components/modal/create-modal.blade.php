<div id="createModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('sales-target.store') }}" id="form-add" method="post">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Add {{ $pageTitle }}</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="year">Year<span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="year" id="year" placeholder="Year"
                                min="2010" step="1" value="{{ date('Y') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="marketing">Marketing<span class="text-danger">*</span></label>
                            <select name="marketing" id="marketing" class="form-control select2">
                                {!! selectGenerate('Marketing', $marketings, 'id', 'name') !!}
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="target">Target</label>
                            <input type="text" class="form-control autonumeric" name="target" id="target"
                                placeholder="target">
                        </div>
                    </div>
                </div>
                {{-- /.modal-body --}}

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                </div>

            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script src="https://code.jquery.com/jquery-3.6.4.slim.js"
    integrity="sha256-dWvV84T6BhzO4vG6gWhsWVKVoa4lVmLnpBOZh/CAHU4=" crossorigin="anonymous"></script>

<script>
    $(function () {
        $('#form-add').submit(function (e) {
            e.preventDefault();

            let target = $('#target').autoNumeric('get');
            $('#target').val(target);

            submitData();
        });

        function submitData() {
            $.ajax({
                type: 'POST',
                url: '{{ route("sales-target.store") }}',
                data: $('#form-add').serialize(),
                success: function (data) {
                    // Handle success response
                    $('#createModal').modal('hide');
                    $('#form-add')[0].reset();
                    $('.help-block').remove()
                    $('#salestarget-table').DataTable().ajax.reload();

                    toastSuccess('New sales target data successfully added.')
                },
                error: function (xhr, status, error) {
                    // Handle error response
                    let response = xhr.responseJSON;

                    if (!$.isEmptyObject(response)) {
                        $('.help-block').remove();

                        $.each(response.errors, function (indexInArray, valueOfElement) {
                            $(`#${indexInArray}`).parents('.validate-input').append(
                                `<span class="help-block"><mdall>${valueOfElement[0]}</mdall></span>`
                            )
                        });
                    }
                }
            });
        }
    });

</script>
