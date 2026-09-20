<div id="jobProductDraftModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="jobProductDraftLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="job-product-draft-form" method="post" class="parsley-examples">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="jobProductDraftLabel">Add Job Product</h4>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="job-product-draft-section">
                    <input type="hidden" id="job-product-draft-index">

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="job-product-draft-main-category">Main Category</label>
                            <br>
                            <select class="form-control select2" id="job-product-draft-main-category">
                                {!! selectGenerate('Main Category', $mainCategories, 'id', ['code', 'main_category_name'], null) !!}
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="job-product-draft-supplier">Supplier</label>
                            <br>
                            <select class="form-control select2" id="job-product-draft-supplier" disabled>
                                <option selected disabled>-- Select supplier --</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12 validate-input">
                            <label for="job-product-draft-product">Product<span class="text-danger">*</span></label>
                            <br>
                            <select class="form-control select2" id="job-product-draft-product" disabled required>
                                <option selected disabled>-- Select product --</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="col-12 validate-input">
                                    <label for="job-product-draft-qty">Quantity<span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input class="form-control autoNumeric" type="text" id="job-product-draft-qty"
                                            placeholder="0" data-a-sign="" data-a-sep="." data-a-dec="," required>
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="job-product-draft-qty-unit">-</span>
                                        </div>
                                    </div>
                                    <input type="number" id="job-product-draft-stock-validation" hidden>
                                    <p class="mb-0"><span class="text-danger">*</span>Stock :
                                        <span id="job-product-draft-stock"></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="col-12 validate-input">
                                    <label for="job-product-draft-price">Unit Price<span class="text-danger">*</span></label>
                                    <input class="form-control autoNumeric" type="text" id="job-product-draft-price"
                                        placeholder="0" data-a-sign="" data-a-sep="." data-a-dec="," required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-12">
                            <label for="job-product-draft-note">Note</label>
                            <textarea class="form-control" id="job-product-draft-note" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light"
                        id="btn-save-job-product-draft">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
