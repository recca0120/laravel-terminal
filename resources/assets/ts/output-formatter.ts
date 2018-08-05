import * as $ from 'jquery';
import 'jquery.terminal';
import './unix_formatting';

export class OutputFormatterStyle {
    // private colorList:any = {
    //     30: 'black',
    //     31: 'red',
    //     32: 'green',
    //     33: 'yellow',
    //     34: 'blue',
    //     35: 'magenta',
    //     36: 'cyan',
    //     37: 'white',

    //     39: 'white',
    // },

    // private backgroundList: any = {
    //     40: 'black',
    //     41: 'red',
    //     42: 'green',
    //     43: 'yellow',
    //     44: 'blue',
    //     45: 'magenta',
    //     46: 'cyan',
    //     47: 'white',

    //     49: 'black',
    // }

    private colors: any = Object.assign($.terminal['ansi_colors']['bold'], {
        white: $.terminal['ansi_colors']['normal']['white'],
        red: $.terminal['ansi_colors']['normal']['red'],
    });

    private foreground: string = '';
    private background: string = '';

    constructor(foreground: string = 'white', background: string = 'black') {
        this.foreground = this.getColor(foreground);
        this.background = this.getColor(background);
    }

    apply(text: string): string {
        return `[[;${this.foreground};${this.background}]${$.terminal.escape_brackets(text)}]`;
    }

    private getColor(color: string): string {
        return this.colors[color] ? this.colors[color] : color;
    }
}

export class OutputFormatter {
    private formatters: any = {
        black: new OutputFormatterStyle('black'),
        red: new OutputFormatterStyle('red'),
        green: new OutputFormatterStyle('green'),
        yellow: new OutputFormatterStyle('yellow'),
        blue: new OutputFormatterStyle('blue'),
        magenta: new OutputFormatterStyle('magenta'),
        cyan: new OutputFormatterStyle('cyan'),
        white: new OutputFormatterStyle('white'),
        error: new OutputFormatterStyle('white', 'red'),
        info: new OutputFormatterStyle('green'),
        comment: new OutputFormatterStyle('yellow'),
        question: new OutputFormatterStyle('magenta'),
    };

    is(text: string): boolean {
        return /\[\d+;(\d+)?/.test(text);
    }

    error(text: string): string {
        return this.formatters.error.apply(text);
    }

    info(text: string): string {
        return this.formatters.info.apply(text);
    }

    comment(text: string): string {
        return this.formatters.comment.apply(text);
    }

    question(text: string): string {
        return this.formatters.question.apply(text);
    }

    black(text: string): string {
        return this.formatters.black.apply(text);
    }

    red(text: string): string {
        return this.formatters.red.apply(text);
    }

    green(text: string): string {
        return this.formatters.green.apply(text);
    }

    yellow(text: string): string {
        return this.formatters.yellow.apply(text);
    }

    blue(text: string): string {
        return this.formatters.blue.apply(text);
    }

    magenta(text: string): string {
        return this.formatters.magenta.apply(text);
    }

    cyan(text: string): string {
        return this.formatters.cyan.apply(text);
    }

    white(text: string): string {
        return this.formatters.white.apply(text);
    }
}
