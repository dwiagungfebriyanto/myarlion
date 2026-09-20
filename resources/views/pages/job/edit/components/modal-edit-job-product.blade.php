<div class="modal-content">
    <form id="edit-form" method="post" class="parsley-examples">
        @csrf
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title" id="myModalLabel">Edit Job Product</h4>
        </div>

        <div class="modal-body">
            <div class="form-group">
                <div class="col-12 validate-input">
                    <label for="main_category-edit">Main Category</label>
                    <br>
                    <select class="form-control select2" id="main_category-edit">
                        {!! selectGenerate('Main Category', $mainCategories, 'id', ['code', 'main_category_name'],
                        $jobProduct->main_category_id) !!}
                    </select>
                </div>
            </div>

            <div class="form-group">
                <div class="col-12 validate-input">
                    <label for="supplier-edit">Supplier</label>
                    <br>
                    <select class="form-control select2" id="supplier-edit"
                        data-selected-supplier="{{ $jobProduct->supplier_id }}">
                        <option selected disabled>-- Select supplier --</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <div class="col-12 validate-input-edit">
                    <label>Product<span class="text-danger">*</span></label>
                    <br>
                    <select class="form-control select2" name="job_has_product" id="job_has_product-edit"
                        data-selected-product="{{ $jobProduct->product_id }}" disabled required>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <div class="col-12 validate-input-edit">
                            <label>Quantity<span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input class="form-control autoNumeric" type="text" id="qty-edit" name="qty"
                                    placeholder="0" value="{{ $jobProduct->quantity }}" data-a-sign="" data-a-sep="."
                                    data-a-dec="," required>
                                <div class="input-group-append">
                                    <span class="input-group-text" id="qty-unit-edit">-</span>
                                </div>
                            </div>
                            <input type="number" id="stock-validation-edit" hidden>
                            <p class="mb-0"><span class="text-danger">*</span>Stock : <span id="stock-edit"></span></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <div class="col-12">
                            <label>Unit Price<span class="text-danger">*</span></label>
                            <input class="form-control autoNumeric" type="text" id="price-edit" name="price"
                                placeholder="0" value="{{ $jobProduct->price }}" data-a-sign="" data-a-sep="."
                                data-a-dec="," required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-12">
                    <label for="note-edit">Note</label>
                    <textarea class="form-control" id="note-edit" name="note" rows="3">{{ $jobProduct->note }}</textarea>
                </div>
            </div>
            {{-- /.row --}}
        </div>
        {{-- /.modal-body --}}

        <div class="modal-footer">
            <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary waves-effect waves-light btn-edit-submit">Update</button>
        </div>
    </form>
</div>
{{-- /.modal-content --}}

<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ asset('assets/libs/autonumeric/autoNumeric-min.js') }}"></script>
<script src="{{ asset('assets/js/helper.js') }}"></script>

@include('pages.job.edit.components.modal-edit-job-product-config')
