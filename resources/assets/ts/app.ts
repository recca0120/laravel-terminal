import * as $ from 'jquery';
import 'jquery-mousewheel';
import 'jquery.terminal';
import './unix_formatting';
import { HttpClient } from './httpclient';
import { Artisan, Composer, MySQL, Tinker, Vim, Help } from './commands';
import { OutputFormatter } from './output-formatter';
import { Command } from './command';

const win: any = <any>window;
$.terminal.defaults.unixFormattingEscapeBrackets = true;
$.terminal['ansi_colors']['normal'] = $.terminal['ansi_colors']['bold'];

export class Terminal {
    private element: HTMLDivElement;
    private outputFormatter = new OutputFormatter();
    private commands: any[] = [];
    private term;

    constructor(elementId, private options: any) {
        console.log(this.options);
        const client = new HttpClient(this.options.endpoint, {
            'X-CSRF-TOKEN': this.options.csrfToken,
        });

        this.commands = [
            new Artisan(client, this.options),
            new Composer(client, this.options),
            new Help(client, this.options),
            new MySQL(client, this.options),
            new Tinker(client, this.options),
            new Vim(client, this.options),
        ];

        this.element = document.querySelector(elementId);
        this.term = $(this.element).terminal(this.run.bind(this), {
            greetings: this.greetings(),
            prompt: this.prompt(),
        } as any);

        this.run('help');

        this.fit();
        window.addEventListener('resize', this.fit.bind(this));
    }

    async run(cmd: string) {
        cmd = cmd.trim();
        for (const index in this.commands) {
            const command = this.commands[index];
            try {
                if (this.interpreter(command, cmd) === true) {
                    break;
                }

                if (command.is(cmd) === true) {
                    this.term.pause();
                    this.term.echo(await command.run(cmd)).resume();

                    break;
                }
            } catch (e) {
                this.term.error(e);
            }
        }
    }

    private interpreter(command: Command, cmd: string): boolean {
        if (command.isInterpreter(cmd) === false) {
            return false;
        }

        const interpreter = command.getInterpreter();
        this.term.push((cmd: string, term: any) => {
            if (cmd === 'exit') {
                term.pop();

                return;
            }

            this.run(`${term.name()} ${cmd}`);
        }, interpreter);

        return true;
    }

    private prompt() {
        // const host = [this.options.username, '@', this.options.hostname].reduce(
        //     (prev, next) => prev + this.outputFormatter.info(next),
        //     ''
        // );
        // const os = this.outputFormatter.question(this.options.os);
        // const path = this.outputFormatter.comment(this.options.basePath);

        // return `${host} ${os} ${path} $ `;

        const host = [this.options.username, '@', this.options.hostname].reduce(
            (prev, next) => prev + this.outputFormatter.info(next),
            ''
        );
        const path = this.outputFormatter.comment('~');

        return `${host}:${path} $ `;
    }

    private greetings() {
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
