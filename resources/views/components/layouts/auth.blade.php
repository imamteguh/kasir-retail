<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
    class="layout-wide customizer-hide" 
    dir="ltr"
    data-skin="default"
    data-assets-path="{{ url('assets') }}/"
    data-template="vertical-menu-template"
    data-bs-theme="light"
    data-base-url="{{ url('/') }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>{{ isset($title) ? $title . ' - ' . config('app.name') : config('app.name') }}</title>
    <meta name="description" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon -->
    {{-- <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" /> --}}
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}"/>
    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/pickr/pickr-themes.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}"/>
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/animate-css/animate.css') }}" />
    <!-- endbuild -->
    <link rel="stylesheet" href="{{ asset('assets/css/page-auth.css') }}" />
    @stack('styles')
    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
</head>

<body>

<!-- Content -->
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            {{ $slot }}
        </div>
    </div>
</div>
<!-- / Content -->

<!-- Core JS -->
<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/pickr/pickr.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
<script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
<!-- endbuild -->
<!-- Main JS -->
<script src="{{ asset('assets/js/main.js') }}"></script>
<!-- Page JS -->
@stack('scripts')
</body>

</html>
