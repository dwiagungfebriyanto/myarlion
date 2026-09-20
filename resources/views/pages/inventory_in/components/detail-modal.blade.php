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
                            <label for="detail-po-stock">PO</label>
                            <br>
                            <p id="detail-po-stock"></p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="detail-product">Product</label>
                            <br>
                            <p id="detail-product"></p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="detail-datetime">Datetime</label>
                            <p id="detail-datetime"></p>
                        </div>

                        <div class="col-md-6">
                            <label for="detail-status">Status</label>
                            <p id="detail-status"></p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="detail-warehouse">Warehouse</label>
                            <p id="detail-warehouse"></p>
                        </div>

                        <div class="col-md-6">
                            <label for="detail-quantity">Quantity</label>
                            <p id="detail-quantity"></p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="detail-purchase-cost">Purchase Cost</label>
                            <p id="detail-purchase-cost"></p>
                        </div>

                        <div class="col-md-6">
                            <label for="detail-purchase-cost-per-unit">Purchase Cost per Unit</label>
                            <p id="detail-purchase-cost-per-unit"></p>
                        </div>
                    </div>
                </div>

                <div class="form-group" style="margin-left: -12px">
                    <div class="col-md-12">
                        <label for="detail-notes">Notes</label>
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
