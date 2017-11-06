const oldjQuery = window.jQuery;

window.jQuery = require('jquery');
require('jquery-mousewheel');
require('jquery.terminal');
require('jquery.terminal/js/unix_formatting');

if (oldjQuery) {
    window.jQuery = window.$ = oldjQuery;
}
