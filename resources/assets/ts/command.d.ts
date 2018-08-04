import { HttpClient } from './httpclient';
export abstract class Command {
    protected client: HttpClient;
    constructor(client?: HttpClient);
    abstract is(command: any): boolean;
    run(command: any): Promise<any>;
    protected parseSentence(command: any): any;
}
