<!-- resources/views/modals/export-modal.blade.php -->

<div id="exportModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('vendor.export') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Export Data by Excel</h4>
                </div>

                <div class="modal-body">
                    <!-- From Code Input -->
                    <div class="form-group">
                        <label for="fromCode">From Code:</label>
                        <input type="number" name="fromCode" id="fromCode" class="form-control" placeholder="e.g., 1" required min="1" step="1">
                    </div>

                    <!-- To Code Input -->
                    <div class="form-group">
                        <label for="toCode">To Code:</label>
                        <input type="number" name="toCode" id="toCode" class="form-control" placeholder="e.g., 2" required min="1" step="1">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect btn-close" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>
