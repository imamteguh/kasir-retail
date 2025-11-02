<x-layouts.app title="Purchases">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-6 row-gap-4">
            <div class="d-flex flex-column justify-content-center">
                <h4 class="mb-0">Purchases</h4>
                <p class="mb-0">Manage purchases records</p>
            </div>
            <div class="d-flex align-content-center flex-wrap gap-4">
                <button type="button" data-bs-toggle="modal" data-bs-target="#modalAddPurchase" class="btn btn-primary">
                    <i class="icon-base bx bx-plus icon-sm me-0 me-sm-1"></i>
                    <span class="d-none d-sm-inline-block">Add Purchase</span>
                </button>
            </div>
        </div>
        <div class="card">
            <div class="card-datatable table-responsive">
                <table class="datatables-purchases table border-top">
                    <thead>
                        <tr>
                            <th class="text-center" width="50"></th>
                            <th>No. Invoice</th>
                            <th>Supplier</th>
                            <th>Date</th>
                            <th class="text-end">Total</th>
                            <th class="text-center" width="120">Items</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('inventory.purchases.create')

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/buttons.bootstrap5.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/form-validation.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    @endpush

    @push('scripts')
        <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
        <script src="{{ asset('assets/js/page-purchase.js?time='.time()) }}"></script>
    @endpush
</x-layouts.app>