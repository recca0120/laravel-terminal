'use strict';

import $ from './jquery';

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

if (!window.Promise) {
    class Promise {
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
    window.Promise = Promise;
}
