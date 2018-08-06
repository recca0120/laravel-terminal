export declare class HttpClient {
    private endpoint;
    private headers;
    private requestId;
    constructor(endpoint?: string, headers?: any);
    jsonrpc(method: string, params?: string[]): Promise<any>;
}
