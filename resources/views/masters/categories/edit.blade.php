<!-- Modal to edit category -->
<div class="modal fade animate__animated fadeIn" id="modalEditCategory" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-simple modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h4>Edit Category</h4>
                </div>
                <form id="editCategoryForm">
                    @method('PUT')
                    @csrf
                    <input type="hidden" name="id" />
                    <x-inputs.basic
                        name="name"
                        type="text"
                        placeholder="Enter category name"
                        wrapperClass="mb-6" />
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1">
                            <i class="icon-base bx bx-save me-1"></i> Save Changes
                        </button>
                        <button type="button" class="btn btn-label-danger" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- / Modal -->