'use strict';

import './jquery';
import 'jquery-mousewheel';
import 'jquery.terminal';
import 'jquery.terminal/js/unix_formatting';

import './polyfill';
import OutputFormatter from './output-formatter';
import Loading from './loading';

import Artisan from './commands/artisan';
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
        let parentTagName = this.$parent.prop('tagName').toLowerCase();

        if (!options.endPoint) {
            options.endPoint = options.endpoint;
        }

        Object.assign(this, {
            options: Object.assign({}, options),
            formatter: new OutputFormatter,
            prompt: "$ ",
        });

        this.$element.terminal(this.run.bind(this), {
            greetings: this.greetings(),
            onInit: (term) => {
                this.$term = term;
                this.loading = new Loading(term);
                this.commands = [
                    new Help(this, options),
                    new Artisan(this, options),
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
            prompt: this.prompt
        });

        this.$win.on('resize', () => {
            if (parentTagName === 'body') {
                let width = this.$parent.width() - 20;
                let height = this.$parent.height() - 20;
                this.$element.width(width);
                this.$element.height(height);
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
                scrollTop: 9e9
            });
        }, delay);
    }

    run(command) {
        for (let interpreter in this.options.interpreters) {
            if (command === interpreter) {
                let prompt = this.options.interpreters[interpreter];
                this.interpreter(prompt);
                return;
            }
        }

        let cmd = $.terminal.parseCommand(command.trim());
        for (let i = 0; i < this.commands.length; i++) {
            let command = this.commands[i];
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
            prompt: `${prompt}> `
        })
    }

    greetings() {
        return [
            " __                        _    _____              _         _ ",
            "|  |   ___ ___ ___ _ _ ___| |  |_   ____ ___ _____|_|___ ___| |",
            "|  |__| .'|  _| .'| | | -_| |    | || -_|  _|     | |   | .'| |",
            "|_____|__,|_| |__,|\\_/|___|_|    |_||___|_| |_|_|_|_|_|_|__,|_|",
            "",
            "Copyright (c) 2015 Recca Tsai <http://phpwrite.blogspot.tw/>",
            "",
            "Type a command, or type `" + this.info('help') + "`, for a list of commands.",
            "",
        ].join("\n");
    }

    serverInfo() {
        if (this.$term.level() > 1) {
            return;
        }

        let host = `${this.info(this.options.username)}${this.info('@')}${this.info(this.options.hostname)}`;
        let os = this.question(`${this.options.os}`);
        let path = this.comment(`${this.options.basePath}`);
        this.$term.echo(`${host} ${os} ${path}`);
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
        let regex = new RegExp("(\\033\\[(\\d+)(;\\d+)?m(((?!\\033\\[\\d+).)*)\\033\\[(\\d+)(;\\d+)?m)|(\\[|\\])", "g");
        let content;
        text = text.replace(regex, (...m) => {
            if (["[", "]"].includes(m[0]) === true) {
                return $.terminal.escape_brackets(m[0]);
            } else {
                content = $.terminal.escape_brackets(m[4]);
                switch (m[2]) {
                    case '32':
                        return this.info(content);
                        break;
                    case '33':
                        return this.comment(content);
                        break;
                    case '37':
                        return this.error(content);
                        break;
                    default:
                        return m[0];
                        break;
                }
            }
        });
        text.split("\n").forEach((line) => {
            if (line === '') {
                line = ' ';
            }
            this.$term.echo(line);
        })
    }

    confirm(message, title = '') {
        return new Promise((resolve, reject) => {
            let history = this.$term.history();
            history.disable();

            if (title != '') {
                this.$term.echo(title);
            }
            this.$term.echo(message);
            this.$term.push((result) => {
                if (this.toBoolean(result) === true) {
                    resolve(true);
                } else {
                    reject(false)
                    this.serverInfo();
                }
                this.$term.pop();
                history.enable();
                return;
            }, {
                prompt: ' > '
            });
        });
    }

    toBoolean(value) {
        switch (value) {
            case 'y':
            case 'yes':
            case 'true':
                return true;
                break;
            case 'n':
            case 'no':
            case 'false':
            case '':
                return false;
                break;
        }

        return false;
    }
};
