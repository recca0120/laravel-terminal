<div id="panel-terminal-shell-{{ $id }}" class="terminal-panel"></div>
<script>
(function() {
    var preload=function(){var createElement=function(tag,attributes){var id="laravel-terminal-"+attributes.id;var element=document.getElementById(id);if(element){return element;};element=document.createElement(tag);for(key in attributes){element.setAttribute(key,attributes[key]);};return element;};var appendTo=function(element){var appendTo=document.getElementsByTagName('head');appendTo=appendTo.length>0?appendTo[0]:document.body;appendTo.appendChild(element);};var f=function(filename){return filename.replace(/\?.*/,'')+'?'+(new Date()).getTime()};return{createElement:function(type,id,filename,callback){var attributes={},source;if(type==='script'){source='src';attributes['type']='text/javascript';}else{source='href';attributes['type']='text/css';attributes['rel']='stylesheet';}
    attributes[source]=filename;attributes['id']='laravel-terminal-'+id;var element=createElement(type,attributes);element.onerror=function(){preload.createElement(type,id,f(filename),callback);};if(callback){element.onload=callback;};appendTo(element);}};}();

    preload.createElement('link', 'css', '{{ action('\Recca0120\Terminal\Http\Controllers\TerminalController@media', ['file' => 'css/terminal.css']) }}');
    preload.createElement('script', 'terminal', '{{ action('\Recca0120\Terminal\Http\Controllers\TerminalController@media', ['file' => 'js/terminal.js']) }}', function () {
        new Terminal('#panel-terminal-shell-{{ $id }}', {!! $options !!});
    });
})();
</script>
