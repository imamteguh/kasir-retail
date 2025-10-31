<!-- Modal to add product -->
<div class="modal fade animate__animated fadeIn" id="modalAddProduct" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-simple modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-6">
                    <h4 class="mb-2">Add New Product</h4>
                    <p>Add new product to the system.</p>
                </div>
                <form id="addNewProductForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-6">
                        <div class="col-md-12">
                            <x-inputs.basic name="name" type="text" placeholder="Enter product name" label="Name" />
                        </div>
                        <div class="col-md-6">
                            <x-selects.basic label="Category" name="category_id" placeholder="-- Select Category --" wrapperClass="select2"  />
                        </div>
                        <div class="col-md-6">
                            <x-selects.basic label="Unit" name="unit_id" placeholder="-- Select Unit --" wrapperClass="select2" />
                        </div>
                        <div class="col-md-6">
                            <x-inputs.basic name="cost_price" type="number" step="0.01" placeholder="0" label="Cost Price" />
                        </div>
                        <div class="col-md-6">
                            <x-inputs.basic name="selling_price" type="number" step="0.01" placeholder="0" label="Selling Price" />
                        </div>
                        <div class="col-md-6">
                            <x-inputs.basic name="stock" type="number" placeholder="0" label="Stock" />
                        </div>
                        <div class="col-md-6">
                            <x-inputs.basic name="min_stock" type="number" placeholder="0" label="Minimum Stock" />
                        </div>
                        <div class="col-md-12">
                            <div class="form-control-validation">
                                <label for="image" class="form-label">Image</label>
                                <input type="file" name="image" id="image" class="form-control" accept="image/*" />
                            </div>
                        </div>
                        <div class="col-12">
                            <x-switch label="Publish product?" name="is_active" wrapperClass="my-2" />
                        </div>
                    </div>
                    <div class="text-center mt-6">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1">
                            <i class="icon-base bx bx-save me-1"></i> Save
                        </button>
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->