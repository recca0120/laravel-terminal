'use strict';

const $ = (window.jQuery) ? window.jQuery : require('jquery');
$.migrateMute = true;
const jQuery = $;

if (!window.jQuery) {
    window.jQuery = window.$ = $;
}
