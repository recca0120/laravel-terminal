import axios from 'axios';

export class HttpClient {
    private requestId = 0;

    constructor(private endpoint: string = '', private headers: any = {}) {
        this.headers['content-type'] = 'application/json';
        this.headers['X-Requested-With'] = 'XMLHttpRequest';
    }

    async jsonrpc(command: string, parameters: string[] = []): Promise<any> {
        const params = {
            url: this.endpoint,
            method: 'post',
            data: {
                jsonrpc: '2.0',
                id: ++this.requestId,
                command,
                parameters,
            },
            headers: this.headers,
        };

        return axios(params)
            .catch(error => Promise.reject(error))
            .then(response => response.data || {})
            .then(response => (response.error ? Promise.reject(response.error) : Promise.resolve(response.result)));
    }
}
