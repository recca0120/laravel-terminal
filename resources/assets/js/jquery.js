'use strict';

const $ = (window.jQuery) ? window.jQuery : require('jquery');
$.migrateMute = true;
const jQuery = $;
window.jQuery = window.$ = $;
