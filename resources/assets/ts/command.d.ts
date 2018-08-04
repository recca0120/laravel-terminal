import { HttpClient } from './httpclient';
export declare abstract class Command {
    private client;
    constructor(client?: HttpClient);
    abstract is(command: any): boolean;
    run(command: any): Promise<{}>;
    protected parseSentence(command: any): any;
}
