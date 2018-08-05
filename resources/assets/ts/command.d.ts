import { HttpClient } from './httpclient';
import { OutputFormatter } from './output-formatter';
export interface Comfirmable {
    comfirmable(command: any): boolean;
    getComfirm(command: any): string;
    getComfirmCommand(command: string): string;
}
export interface Interpreterable {
    interpreterable(command: string): boolean;
    getInterpreter(): any;
}
export declare abstract class Command implements Interpreterable, Comfirmable {
    protected client: HttpClient;
    protected outputFormatter: OutputFormatter;
    protected options: any;
    constructor(client: HttpClient, outputFormatter: OutputFormatter, options?: any);
    abstract is(command: string): boolean;
    run(command: string): Promise<any>;
    interpreterable(command: string): boolean;
    getInterpreter(): any;
    comfirmable(command: string): boolean;
    getComfirm(command: string): any;
    getComfirmCommand(command: string): string;
    protected environment(): any;
    protected isProduction(): boolean;
    protected parseSentence(command: any): any;
}
