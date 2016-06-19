'user strict';

let $;
if (!global.jQuery) {
    $ = global.jQuery = global.$ = require('jquery');
    $.migrateMute = true;
    require('jquery-migrate');
} else {
    $ = global.jQuery;
}

export default $;
