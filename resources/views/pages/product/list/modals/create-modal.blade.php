<div class="modal-content">
    <form action="{{ route('product.list.store') }}" method="post" class="parsley-examples">
        @csrf
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title" id="myModalLabel">Add Product</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="">Main Category<span class="text-danger">*</span></label>
                            <br>
                            <select class="form-control select2 main_category" required name="main_category" id="main_category">
                                {!! selectGenerate('Main Category', $main_category , 'id', ['code', 'main_category_name'], old('main_category')) !!}
                            </select>
                            {{-- @if ($errors)
                        <span class="help-block">
                            <mdall>{{ $errors->first('category') }}</mdall>
                        </span>
                    @endif --}}
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="">Sub Category<span class="text-danger">*</span></label>
                            <br>
                            <select class="form-control select2 sub_category" required name="sub_category"
                                id="sub_category" disabled>

                            </select>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="">Product Type<span class="text-danger">*</span></label>
                            <br>
                            <select class="form-control select2 product_type" required name="product_type"
                                id="product_type" disabled>

                            </select>

                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="">Brand<span class="text-danger">*</span></label>
                            <br>
                            <select class="form-control select2 brand" required name="brand" id="brand" disabled>

                            </select>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="">Specification<span class="text-danger">*</span></label>
                            <br>
                            <select class="form-control select2 specification" required name="specification"
                                id="specification" disabled>

                            </select>

                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="">Packaging/Size<span class="text-danger">*</span></label>
                            <br>
                            <select class="form-control select2 packaging" required name="packaging" id="packaging" disabled>

                            </select>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="">Supplier<span class="text-danger">*</span></label>
                            <br>
                            <select class="form-control select2 supplier" required name="supplier" id="supplier" disabled>

                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="">Unit<span class="text-danger">*</span></label>
                            <br>
                            <select class="form-control select2" required name="unit" id="unit">
                                {!! selectGenerate('Unit', $unit, 'id', 'unit_name', old('unit')) !!}
                            </select>

                        </div>
                    </div>
                </div>
            </div>
            
            {{-- New field --}}
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <div class="col-12">
                            <label for="">Color<span class="text-danger">*</span></label>
                            <br>
                            <input type="text" class="form-control" required name="color" id="color">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-12">
                    <label for="note">Note</label>
                    <textarea id="textarea" name="note" class="form-control" maxlength="225" rows="3" placeholder="Add note...">{{ old('note') }}</textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary waves-effect waves-light" id="formSubmit">Add</button>
        </div>
    </form>
</div><!-- /.modal-content -->

@include('pages.product.list.js.fetchRelationMainCategory')


<script src="{{ URL::to('/') }}/assets/libs/select2/select2.min.js"></script>

<script src="{{ URL::to('/') }}/assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>

<!-- Init js-->
<script src="{{ URL::to('/') }}/assets/js/pages/form-advanced.init.js"></script>
<script src="{{ URL::to('/') }}/assets/js/pages/form-validation.init.js"></script>

<!-- Plugin js-->
<script src="{{ URL::to('/') }}/assets/libs/parsleyjs/parsley.min.js"></script>
