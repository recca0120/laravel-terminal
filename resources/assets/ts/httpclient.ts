import * as $ from 'jquery';

export class HttpClient {
    private requestId = 0;

    constructor(private endpoint: string = '', private headers: any = {}) {}

    async jsonrpc(command: string, parameters: string[] = []) {
        console.log(parameters);
        return new Promise((resolve, reject) => {
            $.ajax({
                url: this.endpoint,
                dataType: 'json',
                type: 'post',
                data: {
                    jsonrpc: '2.0',
                    id: ++this.requestId,
                    command,
                    parameters,
                },
                beforeSend: (jqXHR: JQueryXHR) => {
                    for (let index in this.headers) {
                        jqXHR.setRequestHeader(index, this.headers[index]);
                    }
                },
                success: (response: any) => {
                    resolve(response.result ? response.result : response.error);
                },
                error: (jqXHR: JQueryXHR, status: string, errorThrown: string) => {
                    reject(errorThrown);
                },
            });
        });
    }
}
