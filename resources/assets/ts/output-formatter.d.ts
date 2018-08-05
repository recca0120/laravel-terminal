import 'jquery.terminal';
import './unix_formatting';
export declare class OutputFormatterStyle {
    private colors;
    private foreground;
    private background;
    constructor(foreground?: string, background?: string);
    apply(text: any): string;
    private getColor;
}
export declare class OutputFormatter {
    private formatters;
    is(text: any): boolean;
    error(text: any): any;
    info(text: any): any;
    comment(text: any): any;
    question(text: any): any;
    black(text: any): any;
    red(text: any): any;
    green(text: any): any;
    yellow(text: any): any;
    blue(text: any): any;
    magenta(text: any): any;
    cyan(text: any): any;
    white(text: any): any;
}
