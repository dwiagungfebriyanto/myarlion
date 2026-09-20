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
                    <div class="col-12 validate-input">
                        <label for="role">Role Name<span class="text-danger">*</span></label>
                        <p id="detail-role-name"></p>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row ml-2">
                        <div class="col-12">
                            <h4 class="header-title" style="text-transform: capitalize">Role Permissions</h4>
                            <div class="row" id="detail-permissions">
                            </div>
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
