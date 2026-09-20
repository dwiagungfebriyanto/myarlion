<!-- resources/views/modals/export-modal.blade.php -->

<div id="exportModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('supplier.export') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Export Data by Excel</h4>
                </div>

                <div class="modal-body">
                    <!-- From Code Input -->
                    <div class="form-group">
                        <label for="fromCode">From Code:</label>
                        <input type="text" name="fromCode" id="fromCode" class="form-control" placeholder="e.g., 001" required>
                    </div>

                    <!-- To Code Input -->
                    <div class="form-group">
                        <label for="toCode">To Code:</label>
                        <input type="text" name="toCode" id="toCode" class="form-control" placeholder="e.g., 002" required>
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
