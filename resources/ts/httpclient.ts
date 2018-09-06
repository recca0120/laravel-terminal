import axios from 'axios';

export class HttpClient {
    private requestId = 0;

    constructor(private endpoint: string = '', private headers: any = {}) {
        this.headers['content-type'] = 'application/json';
        this.headers['X-Requested-With'] = 'XMLHttpRequest';
    }

    async jsonrpc(method: string, params: string[] = []): Promise<any> {
        return axios({
            url: this.endpoint,
            method: 'post',
            data: {
                jsonrpc: '2.0',
                id: ++this.requestId,
                method,
                params,
            },
            headers: this.headers,
        })
            .catch(error => Promise.reject(error))
            .then(response => response.data || {})
            .then(
                response =>
                    response.error
                        ? Promise.reject(response.error.data || response.error.message)
                        : Promise.resolve(response.result)
            );
    }
}
