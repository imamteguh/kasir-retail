<x-layouts.auth title="Login">
    <!-- Register -->
    <div class="card px-sm-6 px-0">
        <div class="card-body">
            <!-- Logo -->
            <div class="app-brand justify-content-center">
                <x-auth-brand />
            </div>
            <!-- /Logo -->
            @if (session('success'))
            <div class="alert alert-success mb-6">{{ session('success') }}</div>
            @endif
            
            <form id="formLogin" class="mb-6" action="/login" method="POST">
                @csrf
                <x-inputs.basic 
                    label="Email" 
                    name="email" 
                    type="text" 
                    placeholder="Enter your email" 
                    wrapperClass="mb-6" 
                    required/>
                <x-inputs.group 
                    label="Password" 
                    name="password" 
                    type="password" 
                    placeholder="Enter your password"
                    wrapperClass="mb-6 form-password-toggle"
                    append='<i class="icon-base bx bx-hide"></i>'
                    required />
                <div class="my-7">
                    <div class="d-flex justify-content-between">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember-me" />
                            <label class="form-check-label" for="remember-me"> Remember Me </label>
                        </div>
                        <a href="/forgot-password">
                            <span>Forgot Password?</span>
                        </a>
                    </div>
                </div>
                <div class="mb-6">
                    <button class="btn btn-primary d-grid w-100" type="submit">Login</button>
                </div>
            </form>

            <p class="text-center">
                <span>New on our platform?</span>
                <a href="/register">
                    <span>Create an account</span>
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
    <script src="{{ asset('assets/js/page-login.js?time=' . time()) }}"></script>
    @endpush
</x-layouts.auth>
