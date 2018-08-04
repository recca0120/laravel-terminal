export declare class HttpClient {
    private endpoint;
    private headers;
    private requestId;
    constructor(endpoint?: string, headers?: any);
    jsonrpc(command: string, parameters?: string[]): Promise<any>;
}
