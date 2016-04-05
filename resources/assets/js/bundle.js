'use strict';

import $ from './jquery';
require('jquery-mousewheel');
require('jquery.terminal');
require('jquery.terminal/js/unix_formatting');

import './polyfill';
import OutputFormatter from './output-formatter';
import CodeMirror from 'codemirror';
import 'codemirror/addon/dialog/dialog';
import 'codemirror/addon/search/searchcursor';
import 'codemirror/addon/mode/loadmode';
import 'codemirror/addon/edit/matchbrackets';
import 'codemirror/addon/display/fullscreen';
import 'codemirror/mode/clike/clike';
import 'codemirror/mode/meta';
import 'codemirror/keymap/vim';
import 'codemirror/mode/php/php';
import 'codemirror/mode/css/css';
import 'codemirror/mode/javascript/javascript';
import 'codemirror/mode/htmlmixed/htmlmixed';
import 'codemirror/mode/xml/xml';
 
class Loading {
    constructor(term) {
        this.$term = term;
        Object.assign(this, {
            anim: ['/', '|', '\\', '-'],
            prompt: null,
            delay: 50,
            intervalId: null,
            counter: 0
        });
    }
    show() {
        this.counter++;
        this.$term.disable();
        this.prompt = this.$term.get_prompt();
        let i = 0;
        this.intervalId = setInterval(() => {
            this.$term.set_prompt(this.anim[i++]);
            if (i > this.anim.length - 1) {
                i = 0;
            }
        }, this.delay);
    }

    hide() {
        this.counter--;
        if (this.counter <= 0) {
            clearInterval(this.intervalId);
            this.$term.enable();
            this.$term.set_prompt(this.prompt);
            this.counter = 0;
        }
    }
}

class Commands {
    artisan(cmd) {
        let rest = $.terminal.parseCommand(cmd.rest.trim());
        if (this.options.environment === 'production' &&
            rest.args.includes('--force') === false &&
            this.options.confirmToProceed[cmd.name].includes(rest.name) === true
        ) {
            this.$term.echo(this.comment('**************************************'));
            this.$term.echo(this.comment(`*     Application In Production!     *`));
            this.$term.echo(this.comment('**************************************'));
            this.$term.echo(' ');

            let promise = this.confirm(`${this.info('Do you really wish to run this command? [y/N] (yes/no)')} ${this.comment('[no]')}: `)
            promise.then(() => {
                this.makeRequest(cmd).then((response) => {
                    this.echo(response.result);
                });
            }, () => {
                this.echo(" ");
                this.$term.echo(`${this.comment('Command Cancelled!')}`);
                this.echo(" ");
            });
        } else {
            this.makeRequest(cmd).then((response) => {
                this.echo(response.result);
            }, () => {});
        }
    }

    getEditor() {
        let textarea = $('<textarea style="display: none;"></textarea>').appendTo(document.body).get(0);
        let editor = CodeMirror.fromTextArea(textarea, {
            lineNumbers: true,
            matchBrackets: true,
            keyMap: 'vim',
            showCursorWhenSelecting: true,
            theme: 'monokai'
        });
        editor.getWrapperElement().className += ' CodeMirror-fullscreen';
        $(editor.getWrapperElement()).hide();

        return editor;
    }

    vi(cmd) {
        let path = cmd.rest;
        let editor = this.editor;
        this.makeRequest(cmd).then((response) => {
            this.$term.pause();
            let m, info, mode, spec;
            if (m = path.match(/.+\.([^.]+)$/)) {
                info = CodeMirror.findModeByExtension(m[1]);
                if (info) {
                    mode = info.mode;
                    spec = info.mime;
                }
            } else if (/\//.test(path)) {
                info = info = CodeMirror.findModeByMIME(path);
                if (info) {
                    mode = info.mode;
                    spec = info.mime;
                }
            } else {
                mode = spec = val;
            };

            if (['htmlmixed', 'css', 'javascript', 'php'].includes(mode) === false) {
                mode = 'php';
                spec = 'application/x-httpd-php';
            }

            if (mode) {
                editor.setOption("mode", spec);
                CodeMirror.autoLoadMode(editor, mode);
            }

            $(editor.getWrapperElement()).show();
            let doc = editor.getDoc();
            let cm = doc.cm;
            doc.setValue(response.result);
            doc.setCursor(0);
            editor.focus();
            cm.focus();
            let save = () => {
                let value = JSON.stringify(doc.getValue());
                cmd.command += ` --text=${value}`;
                cmd.rest += ` --text=${value}`;
                cmd.args.push += ` --text=${value}`;
                this.makeRequest(cmd).then(() => {}, () => {});
            }
            let quit = () => {
                $(editor.getWrapperElement()).hide();
                this.$term.resume();
                this.$term.focus();
            }
            CodeMirror.Vim.defineEx('q', 'q', function() {
                quit();
            });
            CodeMirror.Vim.defineEx('w', 'w', function() {
                save();
            });
            CodeMirror.Vim.defineEx('wq', 'wq', function() {
                save();
                quit();
            });
        }, () => {});
    }
}

class Terminal extends Commands {
    constructor(element, options = {}) {
        super();
        this.$element = $(element);
        this.$win = $(window);
        this.$term = null;
        Object.assign(this, {
            options: Object.assign({}, options),
            editor: this.getEditor(),
            formatter: new OutputFormatter,
            requestIds: [],
            loading: null,
            prompt: "$ "
        });

        this.$element.terminal(this.run.bind(this), {
            greetings: this.greetings(),
            onInit: (term) => {
                this.$term = term;
                this.loading = new Loading(term);
                this.run('list');
            },
            onClear: () => {
                this.serverInfo();
            },
            prompt: this.prompt
        });

        this.$win.on('resize', () => {
            let parent = this.$element.parent();
            let width, height;
            if (parent.prop('tagName').toLowerCase() === 'body') {
                width = parent.width()-20;
                height = parent.height()-20;
            } else {
                width = parent.width();
                height = parent.height();
            }
            this.$element.width(width);
            this.$element.height(height);
        }).trigger('resize');
    }

    run(command) {
        command = command.trim();
        let cmd = $.terminal.parseCommand(command);
        switch (cmd.command) {
            case 'help':
            case 'list':
                this.echo(this.options.helpInfo);
                this.serverInfo();
                break;
            case '':
                return;
            default:
                for (let interpreter in this.options.interpreters) {
                    if (command === interpreter) {
                        let prompt = this.options.interpreters[interpreter];
                        this.interpreter(prompt);
                        return;
                    }
                }

                switch (cmd.name) {
                    case 'artisan':
                        this.artisan(cmd);
                        break;
                    case 'vi':
                        this.vi(cmd);
                        break;
                    default:
                        this.makeRequest(cmd).then((response) => {
                            this.echo(response.result);
                        }, () => {});
                        break;
                }
                break;
        }
    }

    interpreter(prompt) {
        this.$term.push((command) => {
            this.run(`${prompt.replace(/\s+/g, '-')} ${JSON.stringify(command)}`);
        }, {
            prompt: `${prompt}> `
        })
    }

    makeRequest(cmd) {
        return new Promise((resolve, reject) => {
            this.requestIds[cmd.method] = this.requestIds[cmd.method] || 0;
            this.loading.show();
            $.ajax({
                url: this.options.endPoint,
                dataType: 'json',
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': this.options.csrfToken
                },
                data: {
                    jsonrpc: '2.0',
                    id: ++this.requestIds[cmd.method],
                    cmd: cmd
                }
            }).success((response) => {
                if (response.error === 1) {
                    this.echo(response.result);
                    reject(response);
                    return;
                }
                setTimeout(() => {
                    resolve(response);
                }, 50);
            }).error((jqXhr, json, errorThrown) => {
                this.error(`${jqXhr.status}: ${errorThrown}`);
                reject(errorThrown);
            }).complete(() => {
                setTimeout(() => {
                    this.serverInfo();
                }, 50);
                this.loading.hide();
            });
        });
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

        let host = this.info(`${this.options.username}@${this.options.hostname}`);
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
}

window.Terminal = Terminal;

export default Terminal;
