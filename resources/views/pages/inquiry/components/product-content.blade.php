<div class="row">
    <div class="col-12">
        <form id="product-form" action="" method="post" class="parsley-examples">
            @csrf
            <input type="text" name="id" id="id" hidden>
            <input type="text" name="inquiry_id" id="inquiry_id" value="{{ isset($inquiry->id) ? $inquiry->id : '' }}"
                hidden>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="">Main Category<span class="text-danger">*</span></label>
                            <br>
                            <select class="form-control select2 main_category" required name="main_category"
                                id="main_category">
                                @isset($main_category)
                                    {!! selectGenerate('Main Category', $main_category, 'id', ['code', 'main_category_name'], old('main_category')) !!}
                                @endisset
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="">Sub Category</label>
                            <br>
                            <select class="form-control select2 sub_category" name="sub_category" id="sub_category"
                                disabled>

                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="">Product Type</label>
                            <br>
                            <select class="form-control select2 product_type" name="product_type" id="product_type"
                                disabled>

                            </select>

                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="">Brand</label>
                            <br>
                            <select class="form-control select2 brand" name="brand" id="brand" disabled>

                            </select>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="">Specification</label>
                            <br>
                            <select class="form-control select2 specification" name="specification" id="specification"
                                disabled>

                            </select>

                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="">Packaging/Size</label>
                            <br>
                            <select class="form-control select2 packaging" name="packaging" id="packaging" disabled>

                            </select>

                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="form-group text-right my-3 px-2">
            @can('add inquiry_item')
            <button class="btn btn-primary waves-effect waves-light btn-submit-product" type="button">
                Add/Update Inquiry Product
            </button>
            @endcan
        </div>
    </div>
</div>


<div class="border-top border-bottom py-3">
    <div id="table-product"></div>
</div>
<br>
<div class="col-12 text-right">
    <a class="btn btn-success waves-effect waves-light"
        href="{{ route('inquiry.index', $request == 'success' ? ($request = 'success') : 'update') }}">
        <i class="mdi mdi-send mr-1"></i>
        <span>Submit Inquiry</span>
    </a>
</div>

@include('pages.inquiry.js.fetchRelationMainCategory')

<script src="{{ URL::to('/') }}/assets/libs/select2/select2.min.js"></script>
<script src="{{ URL::to('/') }}/assets/js/pages/form-advanced.init.js"></script>
<script src="{{ URL::to('/') }}/assets/libs/sweetalert2/sweetalert2.min.js"></script>

<div><button class="reloadProductList" hidden></button></div>
<script>
    $('.btn-submit-product').click(function() {

        $('#product-form').parsley().validate();
        if ($('#product-form').parsley().isValid()) {
            if ($('#id').val() != '') {
                var method = "PUT";
                var url = "/inquiry/inquiry-product/" + $('#id').val() + "/update";
            } else {
                var method = "POST";
                var url = "{{ route('inquiry.inquiryProduct.store') }}";
            }
            $.ajax({
                url: url,
                type: method,
                data: $('#product-form').serialize(),
                success: function(data) {
                    if (data == 'updated') {
                        $.toast({
                            heading: "Success!",
                            text: "Inquiry product data successfully updated.",
                            position: "top-right",
                            loaderBg: "#5ba035",
                            icon: "success",
                            hideAfter: 3e3,
                            stack: 1
                        })
                    } else {
                        $.toast({
                            heading: "Success!",
                            text: "Inquiry product data successfully added.",
                            position: "top-right",
                            loaderBg: "#5ba035",
                            icon: "success",
                            hideAfter: 3e3,
                            stack: 1
                        })
                    }
                    $('#product-form')[0].reset();
                    $('#product-form').parsley().reset();
                    $('#main_category').val('').trigger('change');

                    setTimeout(() => {
                        $('#sub_category').prop("disabled", true);
                        $('#product_type').prop("disabled", true);
                        $('#specification').prop("disabled", true);
                        $('#packaging').prop("disabled", true);
                        $('#brand').prop("disabled", true);
                    }, 1000);
                },
                error: function(data) {
                    $.toast({
                        heading: "Failed!",
                        text: "Change a few things up and try submitting again.",
                        position: "top-right",
                        loaderBg: "#bf441d",
                        icon: "error",
                        hideAfter: 3e3,
                        stack: 1
                    })
                }
            });
            $.ajax({
                method: "get",
                url: "{{ route('inquiry.inquiryProduct.data', $inquiry->id) }}",
                success: function(response) {
                    $('#table-product').html(response);
                }
            })
        }
    });
    $('.reloadProductList').click(function() {
        $.ajax({
            method: "get",
            url: "{{ route('inquiry.inquiryProduct.data', $inquiry->id) }}",
            success: function(response) {
                $('#table-product').html(response);
            }
        })
    });
</script>
