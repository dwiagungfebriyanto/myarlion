<button type="button" class="btn btn-success waves-effect waves-light btn-add-product" data-toggle="modal"
    data-target="#createProductModal">
    <i class="mdi mdi-plus mr-1"></i> Add Product
</button>

<div id="createProductModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="create-form" method="post" class="parsley-examples">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Add Job Product</h4>
                </div>

                <div class="modal-body">
                    <input type="text" name="job_id" id="job_id" value="{{ $job->id }}" hidden>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="main_category">Main Category</label>
                            <br>
                            <select class="form-control select2" id="main_category">
                                {!! selectGenerate('Main Category', $mainCategories, 'id', ['code', 'main_category_name'],
                                old('main_category')) !!}
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="supplier">Supplier</label>
                            <br>
                            <select class="form-control select2" id="supplier" disabled>
                                <option selected disabled>-- Select supplier --</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="job_has_product">Product<span class="text-danger">*</span></label>
                            <br>
                            <select class="form-control select2 job_has_product" required name="job_has_product"
                                id="job_has_product" disabled>

                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="col-12 validate-input">
                                    <label for="qty">Quantity<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input class="form-control autoNumeric qty" type="text" id="qty" name="qty"
                                            placeholder="0" value="{{ old('qty') }}" data-a-sign=""
                                            data-a-sep="." data-a-dec="," required>
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="qty-unit">-</span>
                                        </div>
                                    </div>
                                    <input type="number" id="stock-validation" hidden>
                                    <p class="mb-0"><span class="text-danger">*</span>Stock : <span id="stock"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="col-12">
                                    <label for="price">Unit Price<span class="text-danger">*</span></label>
                                    <input class="form-control autoNumeric price" type="text" id="price" name="price"
                                        placeholder="0" value="{{ old('price') }}" data-a-sign=""
                                        data-a-sep="." data-a-dec="," required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="note">Note</label>
                            <textarea class="form-control" id="note" name="note" rows="3">{{ old('note') }}</textarea>
                        </div>
                    </div>
                </div>
                {{-- /.modal-body --}}

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="button"
                        class="btn btn-primary waves-effect waves-light btn-create-submit">Add</button>
                </div>
            </form>
        </div>
        {{-- /.modal-content --}}
    </div>
    {{-- /.modal-dialog --}}
</div>
{{-- /.modal --}}
