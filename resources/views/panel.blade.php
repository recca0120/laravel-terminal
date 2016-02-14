<style>
{!! $style !!}
</style>

<textarea id="editor"></textarea>
<div id="shell"></div>

<script type="text/javascript">
if (!window.jQuery) {
    {!! $jquery !!}
}

if (!window.jQuery.fn.mousewheel) {
    {!! $mousewheel !!}
}

{!! $terminal !!}

{!! $script !!}

</script>

<script>
(function() {
    {!! $script !!}
    new Term("#shell", $.extend({!! $options !!}, {
        editor: "#editor"
    }));
})();
</script>
