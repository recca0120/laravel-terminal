'use babel';

import $ from '../bootstrap';

export default class Command {
    constructor(api, options) {
        Object.assign(this, {
            api,
            options,
            requestId: 0,
        });
    }

    match(name = false) {
        return name;
    }

    call(cmd) {
        this.api.loading.show();
        this.makeRequest(cmd.command).then((response) => {
            const responseResult = response.result ? response.result : response;
            this.api.loading.hide();
            this.api.echo(responseResult);
            this.api.serverInfo();
        }, (response) => {
            const responseResult = response.result ? response.result : response;
            this.api.loading.hide();
            this.api.echo(responseResult);
            this.api.serverInfo();
        });
    }

    addslashes(str) {
        return (String(str)).replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
    }

    makeRequest(command) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: this.api.options.endpoint,
                dataType: 'json',
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': this.api.options.csrfToken,
                },
                data: {
                    jsonrpc: '2.0',
                    id: ++this.requestId,
                    command,
                },
                success: (response) => {
                    if (response.error !== 0) {
                        reject(response);
                        return;
                    }

                    resolve(response);
                },
                error: (jqXhr, json, errorThrown) => {
                    reject({
                        result: `${jqXhr.status}: ${errorThrown}`,
                    });
                },
            });
        });
    }
}
