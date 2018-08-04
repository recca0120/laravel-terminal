import 'jquery-mousewheel';
import 'jquery.terminal';
import './unix_formatting';
export declare class Terminal {
    private options;
    private element;
    private outputFormatter;
    private commands;
    private term;
    constructor(elementId: any, options: any);
    run(cmd: string): Promise<void>;
    private prompt;
    private greetings;
    private fit;
}
