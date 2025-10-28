<x-layouts.auth title="Register">
    <!-- Register -->
    <div class="card px-sm-6 px-0">
        <div class="card-body">
            <!-- Logo -->
            <div class="app-brand justify-content-center">
                <x-auth-brand />
            </div>
            <!-- /Logo -->
            <h4 class="mb-1">Register new account</h4>
            <p class="mb-6">Please fill in the details to create an account.</p>
            <form id="formRegister" class="mb-6" action="/register" method="POST">
                @csrf
                <x-inputs.basic 
                    label="Full Name" 
                    name="name" 
                    type="text" 
                    wrapperClass="mb-4" 
                    required/>
                <x-inputs.basic 
                    label="Email" 
                    name="email" 
                    type="text" 
                    wrapperClass="mb-4" 
                    required/>
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
                <x-inputs.basic 
                    label="Store Name" 
                    name="store_name" 
                    type="text" 
                    wrapperClass="mb-4" 
                    required/>
                <div class="my-7">
                    <div class="d-flex justify-content-between">
                        <div class="form-check mb-0 form-control-validation">
                            <input class="form-check-input" type="checkbox" name="terms" id="terms-conditions" />
                            <label class="form-check-label" for="terms-conditions"> 
                                I agree to <a href="javascript:void(0);">privacy policy & terms</a>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mb-6">
                    <button class="btn btn-primary d-grid w-100" type="submit">Sign up</button>
                </div>
            </form>

            <p class="text-center">
                <span>Already have an account?</span>
                <a href="/login">
                    <span>Sign in instead</span>
                </a>
            </p>
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
    <script src="{{ asset('assets/js/page-register.js?time=' . time()) }}"></script>
    @endpush
</x-layouts.auth>
