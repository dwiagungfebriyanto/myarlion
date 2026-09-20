@extends('layouts.base')
@section('title', 'Product List')

@section('css')

    <link href="{{ URL::to('/') }}/assets/libs/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet" />
    <link href="{{ URL::to('/') }}/assets/libs/switchery/switchery.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::to('/') }}/assets/libs/select2/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ URL::to('/') }}/assets/libs/bootstrap-select/bootstrap-select.min.css" rel="stylesheet" type="text/css" />

    <style>
        .help-block {
            color: red
        }
    </style>

@endsection

@push('js-head')
@endpush

@section('content')

    <!-- sample modal content -->
    <div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                {{-- @dd($product) --}}
                <form action="{{ route('product.list.update', $product) }}" method="post" class="parsley-examples">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        {{-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> --}}
                        <a href="{{ route('product.list.index') }}" class="btn close"><span
                                style="position: absolute; top: 4.5px; right: 10px;">×</span></a>
                        <h4 class="modal-title" id="myModalLabel">Update Product</h4>


                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="col-12">
                                <label for="username">Category<span class="text-danger">*</span></label>
                                <br>
                                <select class="form-control select2" required data-parsley-required name="category"
                                    id="category">
                                    {!! selectGenerate('Category', $category, 'id', 'category_name', $product->category_id) !!}
                                </select>
                                @if ($errors)
                                    <span class="help-block">
                                        <mdall>{{ $errors->first('category') }}</mdall>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="col-12">
                                        <label for="username">Spesification<spax`n class="text-danger">*</span></label>
                                        <br>
                                        <select class="form-control select2" required data-parsley-required
                                            name="specification" id="specification">
                                            {!! selectGenerate(
                                                'Specification',
                                                $specification,
                                                'specification_code',
                                                'specification_name',
                                                $product->specification_id,
                                            ) !!}
                                        </select>
                                        @if ($errors)
                                            <span class="help-block">
                                                <mdall>{{ $errors->first('specification') }}</mdall>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="col-12">
                                        <label for="username">Packaging/Size<span class="text-danger">*</span></label>
                                        <br>
                                        <select class="form-control select2" required data-parsley-required name="packaging"
                                            id="packaging">
                                            {!! selectGenerate('Packaging', $packaging, 'packaging_code', 'packaging_name', $product->packaging_id) !!}
                                        </select>
                                        @if ($errors)
                                            <span class="help-block">
                                                <mdall>{{ $errors->first('packaging') }}</mdall>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="col-12">
                                        <label for="username">Unit<span class="text-danger">*</span></label>
                                        <br>
                                        <select class="form-control selectpicker" required data-parsley-required
                                            name="unit" id="unit">
                                            {!! selectGenerate('Unit', $unit, 'id', 'unit_name', $product->unit_id) !!}
                                        </select>
                                        @if ($errors)
                                            <span class="help-block">
                                                <mdall>{{ $errors->first('unit') }}</mdall>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="col-12">
                                        <label for="username">Supplier<span class="text-danger">*</span></label>
                                        <br>
                                        <select class="form-control select2" required data-parsley-required name="supplier"
                                            id="supplier">
                                            {!! selectGenerate('Supplier', $supplier, 'id', 'name', $product->supplier_id) !!}
                                        </select>
                                        @if ($errors)
                                            <span class="help-block">
                                                <mdall>{{ $errors->first('supplier') }}</mdall>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-12">
                                <label for="brand">Brand</label>
                                <input class="form-control" type="brand" id="brand" name="brand"
                                    placeholder="Cocopeat" value="{{ $product->brand }}" required data-parsley-required>
                                @if ($errors)
                                    <span class="help-block">
                                        <mdall>{{ $errors->first('brand') }}</mdall>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-12">
                                <label for="password">Note</label>
                                <textarea id="textarea" name="note" class="form-control" maxlength="225" rows="3" placeholder="Add note...">{{ $product->note }}</textarea>
                                @if ($errors)
                                    <span class="help-block">
                                        <mdall>{{ $errors->first('note') }}</mdall>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button> --}}
                        <a href="{{ route('product.list.index') }}" class="btn btn-light waves-effect">Close</a>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Update</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


@endsection

@section('js-vendor')

    <script src="{{ URL::to('/') }}/assets/libs/select2/select2.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/libs/bootstrap-select/bootstrap-select.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/libs/bootstrap-maxlength/bootstrap-maxlength.min.js"></script>
    <script src="{{ URL::to('/') }}/assets/libs/bootstrap-filestyle2/bootstrap-filestyle.min.js"></script>

    <!-- Init js-->
    <script src="{{ URL::to('/') }}/assets/js/pages/form-advanced.init.js"></script>
    <script src="{{ URL::to('/') }}/assets/js/pages/form-validation.init.js"></script>

    <!-- Plugin js-->
    <script src="{{ URL::to('/') }}/assets/libs/parsleyjs/parsley.min.js"></script>

    <script>
        $('body').ready(function() {
            $('#editModal').modal('show');
        });
    </script>


@endsection
