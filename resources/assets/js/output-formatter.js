'use babel';

import $ from './bootstrap';

console.log($.fn)

class OutputFormatterStyle {
    constructor(foreground = 'white', background = 'black', options = []) {
        Object.assign(this, {
            options,
            colorList: {
                30: 'black',
                31: 'red',
                32: 'green',
                33: 'yellow',
                34: 'blue',
                35: 'magenta',
                36: 'cyan',
                37: 'white',

                39: 'white',
            },

            backgroundList: {
                40: 'black',
                41: 'red',
                42: 'green',
                43: 'yellow',
                44: 'blue',
                45: 'magenta',
                46: 'cyan',
                47: 'white',

                49: 'black',
            },

            colors: Object.assign($.terminal.ansi_colors.bold, {
                white: $.terminal.ansi_colors.normal.white,
                red: $.terminal.ansi_colors.normal.red,
            }),
        });

        this.foreground = this.getColor(foreground);
        this.background = this.getColor(background);
    }

    getColor(color) {
        return this.colors[color] ? this.colors[color] : color;
    }

    apply(text) {
        return `[[;${this.foreground};${this.background}]${$.terminal.escape_brackets(text)}]`;
    }
}

export default class OutputFormatter {
    constructor() {
        Object.assign(this, {
            formatters: {
                error: new OutputFormatterStyle('white', 'red'),
                info: new OutputFormatterStyle('green'),
                comment: new OutputFormatterStyle('yellow'),
                question: new OutputFormatterStyle('magenta'),
            },
        });
    }

    error(text) {
        return this.formatters.error.apply(text);
    }

    info(text) {
        return this.formatters.info.apply(text);
    }

    comment(text) {
        return this.formatters.comment.apply(text);
    }

    question(text) {
        return this.formatters.question.apply(text);
    }
}
