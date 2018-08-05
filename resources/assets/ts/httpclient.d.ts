export declare class HttpClient {
    private endpoint;
    private headers;
    private requestId;
    constructor(endpoint?: string, headers?: any);
    jsonrpc(name: string, params?: string[]): Promise<any>;
}
