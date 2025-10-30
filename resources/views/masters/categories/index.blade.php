<x-layouts.app title="Master Categories">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
            <div class="d-flex flex-column justify-content-center">
                <h4 class="mb-0">Categories</h4>
                <p class="mb-0">Manage master category data</p>
            </div>
            <div class="d-flex align-content-center flex-wrap gap-4">
                <button type="button" data-bs-toggle="modal" data-bs-target="#modalAddCategory" class="btn btn-primary">
                    <i class="icon-base bx bx-plus icon-sm me-0 me-sm-1"></i>
                    <span class="d-none d-sm-inline-block">Add Category</span>
                </button>
            </div>
        </div>
        <div class="card">
            <div class="card-datatable table-responsive">
                <table class="datatables-categories table border-top">
                    <thead>
                        <tr>
                            <th class="text-center" width="50"></th>
                            <th>Name</th>
                            <th width="200">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('masters.categories.create')

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/buttons.bootstrap5.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/form-validation.css') }}" />
    @endpush

    @push('scripts')
        <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/cleave-zen/cleave-zen.js') }}"></script>
        <script src="{{ asset('assets/js/page-category.js?time='.time()) }}"></script>
    @endpush
</x-layouts.app>