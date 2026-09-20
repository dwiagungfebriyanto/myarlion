<div class="mt-4 pt-3 border-top job-product-create-section" data-section-key="{{ $sectionKey }}">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
        <h5 class="mb-2 mb-md-0">Job Product</h5>

        <button type="button"
            class="btn btn-success waves-effect waves-light btn-open-job-product-modal"
            data-section-key="{{ $sectionKey }}">
            <i class="mdi mdi-plus mr-1"></i> Add Product
        </button>
    </div>

    <p class="text-muted mb-3">Minimal satu Job Product harus ditambahkan sebelum Job disubmit.</p>

    <div class="alert alert-danger d-none" id="job-product-error-{{ $sectionKey }}"></div>
    <input type="hidden" name="job_products" id="job_products_{{ $sectionKey }}" value="[]">

    <div class="table-responsive">
        <table class="table table-bordered table-sm mb-0">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Note</th>
                    <th class="text-center" style="width: 120px;">Action</th>
                </tr>
            </thead>
            <tbody id="job-product-items-{{ $sectionKey }}">
                <tr class="job-product-empty-row">
                    <td colspan="6" class="text-center text-muted">No product added.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
