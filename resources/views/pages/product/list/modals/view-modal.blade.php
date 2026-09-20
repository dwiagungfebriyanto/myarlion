<!-- sample modal content -->
<div id="viewModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">Detail Product - <p id="sku" style="display:inline;">
                    </p>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <div class="col-md-12 px-0">
                                <label for="">Main Category :</label>
                                <p class="main_category" id="view-main_category"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <div class="col-md-12 px-0">
                                <label for="">Sub Category :</label>
                                <p class="sub_category" id="view-sub_category"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <div class="col-md-12 px-0">
                                <label for="brand">Brand :</label>
                                <p class="brand" id="view-brand"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <div class="col-md-12 px-0">
                                <label for="">Packaging/Size :</label>
                                <p class="packaging" id="view-packaging"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12 px-0">
                        <label for="product_type">Product :</label>
                        <p class="product_type" id="view-product_type"></p>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12 px-0">
                        <label for="specification">Specification :</label>
                        <p class="specification" id="view-specification"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="col-md-12 px-0">
                                <label for="">Supplier :</label>
                                <p class="supplier" id="view-supplier"></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="stock">Stock :</label>
                            <br>
                            <p id="stock" style="display: inline"></p> <span id="stock_unit"></span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="harga_rata_rata">Rata-rata Pembelian :</label>
                            <p id="harga_rata_rata"></p>
                        </div>

                        <div class="col-md-6">
                            <label for="harga_tertinggi">Harga Tertinggi :</label>
                            <p id="harga_tertinggi"></p>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12 px-0">
                        <label for="note">Note :</label>
                        <p id="note"></p>
                    </div>
                </div>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
