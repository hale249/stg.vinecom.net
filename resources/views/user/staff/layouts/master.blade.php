<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ gs()->siteName($pageTitle ?? '') }}</title>

    <link rel="shortcut icon" type="image/png" href="{{siteFavicon()}}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/vendor/bootstrap-toggle.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/global/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/global/css/line-awesome.min.css')}}">

    @stack('style-lib')

    <link rel="stylesheet" href="{{asset('assets/global/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/app.css')}}">
    <link rel="stylesheet" href="{{asset('assets/global/css/custom.css')}}">

    <style>
        :root {
            --primary-color: #6366f1;
            --primary-color-hover: #4f46e5;
            --secondary-color: #10b981;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
            --border-color: #e2e8f0;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #3b82f6;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: #334155;
        }
        
        .card {
            border-radius: 0.75rem;
            border: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }
        
        .card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .btn {
            border-radius: 0.5rem;
            font-weight: 500;
            padding: 0.6rem 1.2rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-color-hover);
            border-color: var(--primary-color-hover);
        }
        
        .form-control {
            border-radius: 0.5rem;
            padding: 0.6rem 1rem;
            border: 1px solid var(--border-color);
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.1);
        }
        
        .table {
            border-radius: 0.5rem;
            overflow: hidden;
        }
        
        .badge {
            padding: 0.35em 0.65em;
            border-radius: 0.375rem;
            font-weight: 500;
        }
        
        .alert {
            border-radius: 0.5rem;
        }
    </style>

    @stack('style')
</head>
<body>
@yield('content')

<script src="{{asset('assets/global/js/jquery-3.7.1.min.js')}}"></script>
<script src="{{asset('assets/global/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('assets/admin/js/vendor/bootstrap-toggle.min.js')}}"></script>
<script src="{{ asset('assets/global/js/nicEdit.js') }}"></script>
<script src="{{asset('assets/global/js/select2.min.js')}}"></script>
<script src="{{asset('assets/admin/js/app.js')}}"></script>

@include('partials.notify')

{{-- LOAD NIC EDIT --}}
<script>
    "use strict";
    bkLib.onDomLoaded(function() {
        $( ".nicEdit" ).each(function( index ) {
            $(this).attr("id","nicEditor"+index);
            new nicEditor({fullPanel : true}).panelInstance('nicEditor'+index,{hasPanel : true});
        });
    });
</script>

@stack('script-lib')
@stack('script')
</body>
</html> 