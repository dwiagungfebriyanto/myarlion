<!-- sample modal content -->
<div id="createModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" id="form-add" method="post">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Add {{ $pageTitle }}</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="id">ID<span class="text-danger">*</span></label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">ASS</span>
                                </div>

                                <input type="text" id="id" class="form-control" aria-label="Username"
                                    aria-describedby="basic-addon1" data-parsley-maxlength="7"
                                    placeholder="" name="id"
                                    value="">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="supplier">Supplier<span class="text-danger">*</span></label>
                            <br>
                            <select id="supplier" class="form-control select2" name="supplier">
                                <option disabled selected>-- Select Supplier --</option>

                                @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ '[' .$supplier->mainCategory->main_category_name ."] $supplier->code - $supplier->supplier_name" }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="note">Notes<span class="text-danger">*</span></label>
                            <textarea id="note" class="form-control" rows="3"
                                name="note"></textarea>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Add</button>
                </div>

            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script src="https://code.jquery.com/jquery-3.6.4.slim.js"
    integrity="sha256-dWvV84T6BhzO4vG6gWhsWVKVoa4lVmLnpBOZh/CAHU4=" crossorigin="anonymous"></script>

<script>
    $(function () {
        $('#createModal').on('shown.bs.modal', function() {
            $.ajax({
                type: "get",
                url: "{{ route('asset.get_id_suggestion') }}",
                success: function (response) {
                    $('#id').attr('placeholder', response);
                }
            });
        });

        $('#form-add').submit(function (e) {
            e.preventDefault();

            let idValue = $('#id').val();
            let setValue = (isNaN(idValue))
                ? idValue
                : 'ASS' + idValue;

            if(isNaN(idValue)) { // jika inputan tidak hanya angka
                $('.help-block').remove()

                $('#id').parent().after(`<span class="help-block">
                                                <mdall>The ID field can only contain numbers.</mdall>
                                            </span>`)
            } else {
                submitData();
            }
        });

        function submitData(setValue) {
            $.ajax({
                type: 'POST',
                url: "{{ route('accounting.asset.store') }}",
                data: {
                    '_token'  : $('[name="_token"]').val(),
                    'id'      : 'ASS' + $('#id').val(),
                    'supplier': $('#supplier').val(),
                    'note'    : $('#note').val(),
                },
                success: function (data) {
                    // Handle success response
                    $('#createModal').modal('hide');
                    $('#form-add')[0].reset();
                    $('select').val(null).trigger('change');
                    $('.help-block').remove()
                    $('#po-asset-datatable').DataTable().ajax.reload();

                    $.toast({
                        heading: "Success!",
                        text: 'New PO Asset data successfully added.',
                        position: "top-right",
                        loaderBg: "#5ba035",
                        icon: "success",
                        hideAfter: 3e3,
                        stack: 1
                    })
                },
                error: function (xhr, status, error) {
                    // Handle error response
                    $('.help-block').remove()

                    let response = xhr.responseJSON;

                    if (!$.isEmptyObject(response)) {

                        $.each(response.errors, function (index, value) {
                            let errorMessage = `<div class="col-12">
                                                    <span class="help-block">
                                                        <mdall>${value}</mdall>
                                                    </span>
                                                </div>`;

                            $(`#form-add`).find(`[name="${index}"]`).parent().after(errorMessage);
                        });
                    }

                    $.toast({
                        heading: "Something went wrong!",
                        text: "Change a few things up and try submitting again.",
                        position: "top-right",
                        loaderBg: "#bf441d",
                        icon: "error",
                        hideAfter: 3e3,
                        stack: 1
                    })
                }
            });
        }
    });
</script>
