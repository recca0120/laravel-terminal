<textarea id="panel-editor" style="display: none;"></textarea>
<div id="panel-shell"></div>


<script>
(function() {
    var loadStyle = function(filename) {
        var link = document.createElement('link');
        link.setAttribute("rel", "stylesheet")
        link.setAttribute("type", "text/css")
        link.setAttribute("href", filename)
        var head = document.getElementsByTagName('head');

        if (head.length > 0) {
            head[0].appendChild(link);
        } else {
            document.body.appendChild(link);
        }
    }

    var loadScript = function(filename, callback) {
        var js = document.createElement('script');
        js.setAttribute("type","text/javascript");
        js.setAttribute("src", filename);
        if (callback) {
            js.onload = callback;
        }
        var head = document.getElementsByTagName('head');
        if (head.length > 0) {
            head[0].appendChild(js);
        } else {
            document.body.appendChild(js);
        }
    }

    loadStyle("{{ action('\Recca0120\Terminal\Http\Controllers\TerminalController@media', ['file' => 'css/app.css']) }}");
    var scripts = {
        jquery: "{{ action('\Recca0120\Terminal\Http\Controllers\TerminalController@media', ['file' => 'js/jquery.min.js']) }}",
        mousewheel: "{{ action('\Recca0120\Terminal\Http\Controllers\TerminalController@media', ['file' => 'js/jquery.mousewheel.min.js']) }}",
        terminal: "{{ action('\Recca0120\Terminal\Http\Controllers\TerminalController@media', ['file' => 'js/terminal.js']) }}",
        app: "{{ action('\Recca0120\Terminal\Http\Controllers\TerminalController@media', ['file' => 'js/app.js']) }}"
    };

    var callback = function () {
        loadScript(scripts.mousewheel, function () {
            loadScript(scripts.terminal, function () {
                loadScript(scripts.app, function() {
                    new Term("#panel-shell", $.extend({!! $options !!}, {
                        editor: "#panel-editor",
                        csrfToken: "{{ csrf_token() }}"
                    }));
                });
            });
        });
    }

    if (!window.jQuery) {
        loadScript(scripts.jquery, callback);
    } else {
        callback();
    }
})();
</script>
