<div id="panel-terminal-shell"></div>
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
        link.onerror = function() {
            loadStyle(id, filename);
        }
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
        js.onerror = function() {
            loadScript(id, filename, callback);
        }
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

    loadStyle('css', "{{ action('\Recca0120\Terminal\Http\Controllers\TerminalController@media', ['file' => 'css/bundle.css']) }}");
    var scripts = {
        terminal: "{{ action('\Recca0120\Terminal\Http\Controllers\TerminalController@media', ['file' => 'js/bundle.js']) }}",
    };

    loadScript('terminal' ,scripts.terminal, function () {
        new Terminal("#panel-terminal-shell", {!! $options !!});
    });
})();
</script>
