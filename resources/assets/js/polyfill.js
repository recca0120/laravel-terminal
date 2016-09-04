'use strict';

import './jquery';
// import 'babel-polyfill';

(function() {
    var testObject = {};

    if (!(Object.setPrototypeOf || testObject.__proto__)) {
        var nativeGetPrototypeOf = Object.getPrototypeOf;

        Object.getPrototypeOf = function(object) {
            if (object.__proto__) {
                return object.__proto__;
            } else {
                return nativeGetPrototypeOf.call(Object, object);
            }
        }
    }
})();

if (!Object.assign) {
    Object.assign = $.extend;
}

if (!Array.prototype.includes) {
    Object.defineProperty(Array.prototype, 'includes', {
        value(...args) {
            return $.inArray(...args, this) !== -1;
        }
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
