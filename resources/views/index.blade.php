<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Terminal</title>
    <link href="{{ asset('vendor/terminal/css/app.css') }}" rel="stylesheet"/>
</head>
<body>
    <script src="{{ asset('vendor/terminal/js/plugins.js') }}"></script>
    <script src="{{ asset('vendor/terminal/js/app.js') }}"></script>
    <script>
    (function($) {
        new Term(document.body, {
            environment: "{{ $environment }}",
            endPoint: "{{ $endPoint }}",
        });
    })(jQuery);
    </script>
</html>
