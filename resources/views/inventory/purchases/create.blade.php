<div class="modal fade" id="modalAddPurchase" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Purchase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createPurchaseForm" class="needs-validation" novalidate>
                <div class="modal-body">
                    <div class="row g-6">
                        <div class="col-md-12">
                            <label class="form-label">Supplier (optional)</label>
                            <select name="supplier_id" id="supplierSelect" class="form-select">
                                <option value="">-- Without Supplier --</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">Items</h5>
                                <button type="button" class="btn btn-sm btn-primary" id="addItemRow">
                                    <i class="icon-base bx bx-plus me-1"></i> Add Item
                                </button>
                            </div>
                            <div class="table-responsive mt-3">
                                <table class="table table-sm" id="itemsTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 35%">Product</th>
                                            <th style="width: 15%" class="text-center">Qty</th>
                                            <th style="width: 20%" class="text-end">Purchase Price</th>
                                            <th style="width: 20%" class="text-end">Subtotal</th>
                                            <th style="width: 10%" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-end">
                                <div class="text-end">
                                    <div class="fw-medium">Total</div>
                                    <div id="grandTotal" class="fs-5">Rp 0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Purchase</button>
                </div>
            </form>
        </div>
    </div>
</div>