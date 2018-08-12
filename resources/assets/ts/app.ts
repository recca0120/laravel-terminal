import * as $ from 'jquery';
import 'jquery-mousewheel';
import 'jquery.terminal';
import 'core-js/es6/promise';
import 'core-js/modules/es6.object.assign';
import 'core-js/modules/es6.regexp.flags';
import './unix_formatting';
import { HttpClient } from './httpclient';
import { Artisan, Common, Composer, MySQL, Tinker, Vim, Help } from './commands';
import { OutputFormatter } from './output-formatter';
import { Command } from './command';
import { Spinner } from './spinners';

const win: any = <any>window;
$.terminal.defaults.unixFormattingEscapeBrackets = true;
(<any>$.terminal)['ansi_colors']['normal'] = (<any>$.terminal)['ansi_colors']['bold'];

export class Terminal {
    private element: HTMLElement;
    private outputFormatter = new OutputFormatter();
    private spinner = new Spinner();
    private commands: Command[] = [];
    private term: any;

    constructor(elementId: string, private options: any) {
        this.element = <HTMLElement>document.querySelector(elementId);

        const client = new HttpClient(this.options.endpoint, {
            'X-CSRF-TOKEN': this.options.csrfToken,
        });

        this.commands = [
            new Artisan(client, this.outputFormatter, this.options),
            new Composer(client, this.outputFormatter, this.options),
            new Help(client, this.outputFormatter, this.options),
            new MySQL(client, this.outputFormatter, this.options),
            new Tinker(client, this.outputFormatter, this.options),
            new Vim(client, this.outputFormatter, this.options),
            new Common(client, this.outputFormatter, this.options),
        ];

        this.fit();

        this.term = $(this.element).terminal(this.run.bind(this), {
            login: true,
            greetings: this.greetings(),
            prompt: this.prompt(),
        } as any);

        this.run('help');

        window.addEventListener('resize', this.fit.bind(this));
    }

    run(cmd: string): void {
        cmd = cmd.trim();

        const executed: boolean = this.commands.some(
            (command: Command): boolean => {
                if (command.is(cmd) === true) {
                    if (command.interpreterable(cmd) === true) {
                        this.term.push((cmd: string, term: any) => {
                            if (['exit', 'quit'].indexOf(cmd) !== -1) {
                                term.pop();

                                return;
                            }

                            this.run(`${term.name()} ${cmd}`);
                        }, command.getInterpreter());

                        return true;
                    }

                    if (command.comfirmable(cmd) === true) {
                        const { message, title, cancel } = command.getComfirm(cmd);

                        this.confirm(message, title, cancel).then(async result => {
                            if (result === true) {
                                this.executeCommand(command, command.getComfirmCommand(cmd));
                            }
                        });

                        return true;
                    }

                    this.executeCommand(command, cmd);

                    return true;
                }

                return false;
            }
        );

        if (executed === false) {
            this.term.echo(`bash: ${cmd.trim().split(/\s+/)[0]}: command not found`);
        }
    }

    private executeCommand(command: Command, cmd: string): void {
        this.term.pause(true);

        this.spinner.start((frame: string) => {
            this.term.set_prompt(`${this.prompt()}${frame}`);
        });

        command
            .run(cmd)
            .catch(error => {
                this.spinner.stop();
                this.term.resume();
                this.term.focus();
                this.term.set_prompt(this.prompt());
                if (this.outputFormatter.is(error)) {
                    this.term.echo(error);
                } else {
                    this.term.error(error);
                }
                this.term.scroll_to_bottom();
            })
            .then((result: string) => {
                if (result === undefined) {
                    return;
                }
                this.spinner.stop();
                this.term.resume();
                this.term.focus();
                this.term.set_prompt(this.prompt());
                this.term.echo(result);
                this.term.scroll_to_bottom();
            });
    }

    private async confirm(message: string, title: string = '', cancel: string = ''): Promise<any> {
        this.term.history().disable();

        if (!!title) {
            this.term.echo(title);
        }

        this.term.echo(message);
        this.term.scroll_to_bottom();

        return new Promise(resolve => {
            this.term.push(
                (result: any, term: any) => {
                    term.pop();
                    this.term.scroll_to_bottom();
                    this.term.history().enable();
                    if (['y', 'yes', 'true'].indexOf(result.trim().toLowerCase()) !== -1) {
                        resolve(true);
                    } else {
                        resolve(false);
                        if (!!cancel) {
                            this.term.echo(cancel);
                        }
                    }
                },
                {
                    prompt: ' > ',
                }
            );
        });
    }

    private prompt(withOS = false): string {
        const host = [this.options.username, '@', this.options.hostname].reduce(
            (prev, next) => prev + this.outputFormatter.info(next),
            ''
        );

        if (withOS === true) {
            const os = this.outputFormatter.question(this.options.os);
            const path = this.outputFormatter.comment(this.options.basePath);

            return `${host} ${os} ${path} \n$ `;
        }

        const path = this.outputFormatter.question('~');

        return `${host}:${path} $ `;
    }

    private greetings(): string {
        return `
 __                        _    _____              _         _
|  |   ___ ___ ___ _ _ ___| |  |_   ____ ___ _____|_|___ ___| |
|  |__| .'|  _| .'| | | -_| |    | || -_|  _|     | |   | .'| |
|_____|__,|_| |__,|\\_/|___|_|    |_||___|_| |_|_|_|_|_|_|__,|_|

Copyright (c) 2018 Recca Tsai <https://github.com/recca0120/>

Type a command, or type \`${this.outputFormatter.info('help')}\`, for a list of commands.
`;
    }

    private fit(): void {
        const parent: HTMLDivElement = this.element.parentNode as HTMLDivElement;

        if (parent.tagName === 'BODY') {
            this.element.style.width = `${win.innerWidth}px`;
            this.element.style.height = `${win.innerHeight}px`;
        } else {
            this.element.style.width = `${parent.offsetWidth - 20}px`;
            this.element.style.height = `${parent.offsetHeight - 20}px`;
        }
    }
}

win.Terminal = Terminal;
