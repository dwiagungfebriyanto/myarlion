<!-- sample modal content -->
<div id="detailModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myModalLabel">Detail {{ $pageTitle }}</h4>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Product</h5>
                            <p id="detail-product"></p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Datetime</h5>
                            <p id="detail-datetime"></p>
                        </div>

                        <div class="col-md-6">
                            <h5>Status</h5>
                            <p id="detail-status"></p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Warehouse</h5>
                            <p id="detail-warehouse"></p>
                        </div>

                        <div class="col-md-6">
                            <h5>Original Warehouse</h5>
                            <p id="detail-original-warehouse"></p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Purchase Cost</h5>
                            <p id="detail-purchase-cost"></p>
                        </div>

                        <div class="col-md-6">
                            <h5>Purchase Cost per Unit</h5>
                            <p id="detail-purchase-cost-per-unit"></p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Quantity</h5>
                            <p id="detail-quantity"></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Type</h5>
                            <p id="detail-type" class="text-capitalize"></p>
                        </div>
                    </div>
                </div>

                <div class="form-group" style="margin-left: -12px">
                    <div class="col-md-12">
                        <h5>Notes</h5>
                        <p id="detail-notes"></p>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
