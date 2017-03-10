'use babel';

const $ = window.jQuery ? window.jQuery : require('jquery');
$.migrateMute = true;
window.$ = $;
window.jQuery = $;

require('jquery-mousewheel');
require('jquery.terminal');
require('jquery.terminal/js/unix_formatting');

if (!Object.assign) {
    Object.assign = $.extend;
}

if (!Array.prototype.includes) {
    Object.defineProperty(Array.prototype, 'includes', {
        value(...args) {
            return $.inArray(...args, this) !== -1;
        },
    });
}

class jQueryPromise {
    constructor(callback) {
        this.deferred = $.Deferred();
        callback((o) => {
            this.deferred.resolve(o);
        }, (o) => {
            this.deferred.reject(o);
        });
        this.promise = this.deferred.promise();
    }

    then(resolve, reject) {
        this.promise.done(resolve);
        this.promise.fail(reject);
        return this;
    }

    catch(reject) {
        this.promise.fail(reject);
        return this;
    }
}

if (!window.Promise) {
    window.Promise = jQueryPromise;
}

(function setPrototypeOf() {
    const testObject = {};

    if (!(Object.setPrototypeOf || testObject.prototype)) {
        const nativeGetPrototypeOf = Object.getPrototypeOf;

        Object.getPrototypeOf = function getPrototypeOf(object) {
            if (object.prototype) {
                return object.prototype;
            }

            return nativeGetPrototypeOf.call(Object, object);
        };
    }
}());

export default $;
