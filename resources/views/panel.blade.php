<style>
{!! $resources['style'] !!}
</style>

<textarea id="editor"></textarea>
<div id="panel-shell"></div>

<script type="text/javascript">
if (!window.jQuery) {
    {!! $resources['jquery'] !!}
}

if (!window.jQuery.fn.mousewheel) {
    {!! $resources['mousewheel'] !!}
}

{!! $resources['terminal'] !!}

{!! $resources['app'] !!}

</script>

<script>
(function() {
    new Term("#panel-shell", $.extend({!! $options !!}, {
        editor: "#editor"
    }));
})();
</script>
