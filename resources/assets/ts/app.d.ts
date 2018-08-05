import 'jquery-mousewheel';
import 'jquery.terminal';
import './unix_formatting';
export declare class Terminal {
    private options;
    private element;
    private outputFormatter;
    private spinner;
    private commands;
    private term;
    constructor(elementId: any, options: any);
    run(cmd: string): void;
    private executeCommand;
    private confirm;
    private prompt;
    private greetings;
    private fit;
}
