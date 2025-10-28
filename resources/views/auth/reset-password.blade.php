<x-layouts.auth title="Reset Password">
    <!-- Register -->
    <div class="card px-sm-6 px-0">
        <div class="card-body">
            <!-- Logo -->
            <div class="app-brand justify-content-center">
                <x-auth-brand />
            </div>
            <!-- /Logo -->
            <h4 class="mb-1">Reset Password</h4>
            <p class="mb-6">Enter the new password to reset your password</p>
            @if (session('success'))
            <div class="alert alert-success mb-6">{{ session('success') }}</div>
            @endif

            <form id="formPassword" class="mb-6" action="/reset-password" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">
                <x-inputs.group 
                    label="Password" 
                    name="password" 
                    type="password" 
                    wrapperClass="mb-4 form-password-toggle"
                    append='<i class="icon-base bx bx-hide"></i>'
                    required />
                <x-inputs.group 
                    label="Password Confirmation" 
                    name="password_confirmation" 
                    type="password" 
                    wrapperClass="mb-4 form-password-toggle"
                    append='<i class="icon-base bx bx-hide"></i>'
                    required />
                <div class="mb-6">
                    <button class="btn btn-primary d-grid w-100" type="submit">Save New Password</button>
                </div>
            </form>

            <div class="text-center">
                <a href="/login" class="d-flex justify-content-center">
                    <i class="icon-base bx bx-chevron-left me-1"></i>
                    Back to login
                </a>
            </div>
        </div>
    </div>
    <!-- /Register -->

    @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/form-validation.css') }}" />
    @endpush

    @push('scripts')
    <script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
    <script src="{{ asset('assets/js/page-forgot-password.js?time=' . time()) }}"></script>
    @endpush
</x-layouts.auth>
