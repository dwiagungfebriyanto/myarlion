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
                        <div class="col-md-12" id="receipt1-detail">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12" id="receipt2-detail">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="detail-date">Date</label>
                            <p id="detail-date"></p>
                        </div>

                        <div class="col-md-6">
                            <label for="detail-bank-account">Bank Account</label>
                            <p id="detail-bank-account"></p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row" style="margin-left: -12px">
                        <div class="col-md-6">
                            <label for="detail-category">Category</label>
                            <p id="detail-category"></p>
                        </div>

                        <div class="col-md-6">
                            <label for="detail-code">Code</label>
                            <p style="text-transform: capitalize" id="detail-code"></p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row" style="margin-left: -12px">
                        <div class="col-md-6">
                            <label for="detail-amount">Amount</label>
                            <p id="detail-amount"></p>
                        </div>

                        <div class="col-md-6">
                            <label for="detail-recipient">
                                Recipient (<span id="detail-recipient-type" style="text-transform: capitalize"></span>)
                            </label>
                            <p id="detail-recipient"></p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row" style="margin-left: -12px">
                        <div class="col-md-6">
                            <label for="detail-note">Note</label>
                            <p id="detail-note"></p>
                        </div>

                        <div class="col-md-6">
                            <div id="po-stock-detail" style="display: none">
                                <label for="detail-po-stock">PO Stock</label>
                                <p id="detail-po-stock"></p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            {{-- end modal-body --}}

            <div class="modal-footer">
                <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
