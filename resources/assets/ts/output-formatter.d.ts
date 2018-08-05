import 'jquery.terminal';
import './unix_formatting';
export declare class OutputFormatterStyle {
    private colors;
    private foreground;
    private background;
    constructor(foreground?: string, background?: string);
    apply(text: string): string;
    private getColor;
}
export declare class OutputFormatter {
    private formatters;
    is(text: string): boolean;
    error(text: string): string;
    info(text: string): string;
    comment(text: string): string;
    question(text: string): string;
    black(text: string): string;
    red(text: string): string;
    green(text: string): string;
    yellow(text: string): string;
    blue(text: string): string;
    magenta(text: string): string;
    cyan(text: string): string;
    white(text: string): string;
}
