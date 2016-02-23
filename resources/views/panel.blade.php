<textarea id="panel-editor" style="display: none;"></textarea>
<div id="panel-shell"></div>

<script>
(function() {
    var loadStyle = function(id, filename) {
        id = "laravel-terminal-"+id;
        if (document.getElementById(id)) {
            return;
        }
        var link = document.createElement('link');
        link.setAttribute("rel", "stylesheet");
        link.setAttribute("type", "text/css");
        link.setAttribute("href", filename);
        link.setAttribute("id", id);
        var head = document.getElementsByTagName('head');

        if (head.length > 0) {
            head[0].appendChild(link);
        } else {
            document.body.appendChild(link);
        }
    }

    var loadScript = function(id, filename, callback) {
        id = "laravel-terminal-"+id;
        if (document.getElementById(id)) {
            if (callback) {
                callback();
            }
            return;
        }
        var js = document.createElement('script');
        js.setAttribute("type","text/javascript");
        js.setAttribute("src", filename);
        js.setAttribute("id", id);
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

    loadStyle('css', "{{ action('\Recca0120\Terminal\Http\Controllers\TerminalController@media', ['file' => 'css/app.css']) }}");
    var scripts = {
        jquery: "{{ action('\Recca0120\Terminal\Http\Controllers\TerminalController@media', ['file' => 'js/jquery.min.js']) }}",
        terminal: "{{ action('\Recca0120\Terminal\Http\Controllers\TerminalController@media', ['file' => 'js/terminal.js']) }}",
        app: "{{ action('\Recca0120\Terminal\Http\Controllers\TerminalController@media', ['file' => 'js/app.js']) }}"
    };

    var callback = function () {
        loadScript('terminal' ,scripts.terminal, function () {
            loadScript('app', scripts.app, function() {
                new Term("#panel-shell", $.extend({!! $options !!}, {
                    editor: "#panel-editor"
                }));
            });
        });
    }

    if (!window.jQuery) {
        loadScript('jquery', scripts.jquery, callback);
    } else {
        callback();
    }
})();
</script>
