<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Export Invoice</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="formInvoice" action="{{ route('job.export_invoice', $job) }}" method="POST">
                    @csrf
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="bankAccount" class="form-label">Bank Account</label>
                            
                            <select id="bankAccount" name="bank_account_id" class="form-control select2" required>
                                <option selected disabled>-- Select Bank Account --</option>

                                @foreach ($bankAccounts as $bankAccount)
                                    <option value="{{ $bankAccount->id }}">
                                        {{ $bankAccount->bankAccountLabel() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            {{-- /.modal-body --}}

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" form="formInvoice">Export</button>
            </div>
        </div>
    </div>
</div>


<button type="button" class="btn btn-outline-primary waves-effect ml-2" data-toggle="modal" data-target="#exportModal">
    <i class="mdi mdi-file-excel mr-1"></i> Export Invoice
</button>
