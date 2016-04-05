'user strict';

let $;
if (!window.jQuery) {
    $ = window.jQuery = window.$ = require('jquery');
} else {
    $ = window.jQuery;
}

export default $;
