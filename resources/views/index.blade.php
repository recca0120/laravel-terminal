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
    <textarea id="editor"></textarea>
    <div id="shell"></div>
    <script src="{{ asset('vendor/terminal/js/plugins.js') }}"></script>
    <script src="{{ asset('vendor/terminal/js/app.js') }}"></script>
    <script>
    (function($) {
        new Term("#shell", $.extend({!! $options !!}, {
            editor: "#editor"
        }));
    })(jQuery);
    </script>
</html>
