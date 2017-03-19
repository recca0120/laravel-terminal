'use babel';

import $ from './bootstrap';
import OutputFormatter from './output-formatter';
import Loading from './loading';
import Artisan from './commands/artisan';
import Composer from './commands/composer';
import Default from './commands/default';
import Help from './commands/help';
import Mysql from './commands/mysql';
import Tinker from './commands/tinker';
import Vi from './commands/vi';

export default class Terminal {
    constructor(element, options = {}) {
        this.$element = $(element);
        this.$parent = this.$element.parent();
        this.$win = $(window);
        this.$term = null;

        const parentTagName = this.$parent.prop('tagName').toLowerCase();

        if (!options.endPoint) {
            options.endPoint = options.endpoint;
        }

        Object.assign(this, {
            options: Object.assign({}, options),
            formatter: new OutputFormatter(),
            prompt: '$ ',
        });

        this.$element.terminal(this.run.bind(this), {
            greetings: this.greetings(),
            onInit: (term) => {
                this.$term = term;
                this.loading = new Loading(term);
                this.commands = [
                    new Help(this, options),
                    new Artisan(this, options),
                    new Composer(this, options),
                    new Mysql(this, options),
                    new Tinker(this, options),
                    new Vi(this, options),
                    new Default(this, options),
                ];
                this.run('list');
            },
            onClear: () => {
                this.serverInfo();
            },
            prompt: this.prompt,
        });

        this.$win.on('resize', () => {
            if (parentTagName === 'body') {
                this.$element.width(this.$parent.width() - 20);
                this.$element.height(this.$parent.height() - 20);
            } else {
                this.scrollToBottom();
            }
        }).trigger('resize');

        this.$parent.on('click', () => {
            if (this.$term) {
                this.$term.focus();
            }
        });
    }

    scrollToBottom(delay = 200) {
        setTimeout(() => {
            this.$element.parent().animate({
                scrollTop: 9e9,
            });
        }, delay);
    }

    run(command) {
        for (const interpreter in this.options.interpreters) {
            if (command === interpreter) {
                const prompt = this.options.interpreters[interpreter];
                this.interpreter(prompt);
                return;
            }
        }

        const cmd = $.terminal.parse_command(command.trim());
        for (let i = 0; i < this.commands.length; i++) {
            command = this.commands[i];
            if (command.match(cmd.name) === true) {
                command.call(cmd);
                break;
            }
        }
    }

    interpreter(prompt) {
        this.$term.push((command) => {
            this.run(`${prompt.replace(/\s+/g, '-')} ${command}`);
        }, {
            prompt: `${prompt}> `,
        });
    }

    greetings() {
        return `
 __                        _    _____              _         _
|  |   ___ ___ ___ _ _ ___| |  |_   ____ ___ _____|_|___ ___| |
|  |__| .'|  _| .'| | | -_| |    | || -_|  _|     | |   | .'| |
|_____|__,|_| |__,|\\_/|___|_|    |_||___|_| |_|_|_|_|_|_|__,|_|

Copyright (c) 2015 Recca Tsai <http://phpwrite.blogspot.tw/>

Type a command, or type \`${this.info('help')}\`, for a list of commands.`;
    }

    serverInfo() {
        if (this.$term.level() > 1) {
            return;
        }

        const host = `${this.info(this.options.username)}${this.info('@')}${this.info(this.options.hostname)}`;
        const os = this.question(`${this.options.os}`);
        const path = this.comment(`${this.options.basePath}`);
        this.$term.echo(`${host} ${os} ${path}`);
        this.scrollToBottom();
    }

    error(text) {
        return this.formatter.error(text);
    }

    info(text) {
        return this.formatter.info(text);
    }

    comment(text) {
        return this.formatter.comment(text);
    }

    question(text) {
        return this.formatter.question(text);
    }

    echo(text) {
        const regex = new RegExp('(\\033\\[(\\d+)(;\\d+)?m(((?!\\033\\[\\d+).)*)\\033\\[(\\d+)(;\\d+)?m)|(\\[|\\])', 'g');
        let content;
        text = text.replace(regex, (...m) => {
            if (['[', ']'].includes(m[0]) === true) {
                return $.terminal.escape_brackets(m[0]);
            }
            content = $.terminal.escape_brackets(m[4]);
            if (m[2] === '32') {
                return this.info(content);
            }

            if (m[2] === '33') {
                return this.comment(content);
            }

            if (m[2] === '37') {
                return this.error(content);
            }

            return m[0];
        });
        text.split('\n').forEach((line) => {
            if (line === '') {
                line = ' ';
            }
            this.$term.echo(line);
        });
    }

    confirm(message, title = '') {
        return new Promise((resolve, reject) => {
            const history = this.$term.history();
            history.disable();

            if (title !== '') {
                this.$term.echo(title);
            }
            this.$term.echo(message);
            this.scrollToBottom();
            this.$term.push((result) => {
                if (this.toBoolean(result) === true) {
                    resolve(true);
                } else {
                    reject(false);
                    this.serverInfo();
                }
                this.$term.pop();
                history.enable();
            }, {
                prompt: ' > ',
            });
        });
    }

    toBoolean(value) {
        return ['y', 'yes', 'true'].includes(value.toLowerCase());
    }
}
