import 'jquery-mousewheel';
import 'jquery.terminal';
import './unix_formatting';
export declare class Terminal {
    private element;
    private commands;
    private term;
    constructor(elementId: any, options: any);
    run(cmd: string): Promise<void>;
    fit(): void;
}
