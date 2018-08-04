import * as $ from 'jquery';
import 'jquery-mousewheel';
import 'jquery.terminal';
import './unix_formatting';
import { HttpClient } from './httpclient';
import { Artisan, Composer, MySQL, Tinker, Vim } from './commands';

const win: any = <any>window;
$.terminal.defaults.unixFormattingEscapeBrackets = true;
$.terminal['ansi_colors']['normal'] = $.terminal['ansi_colors']['bold'];

export class Terminal {
    private element: HTMLDivElement;
    private commands = [];
    private term;

    constructor(elementId, options: any) {
        const client = new HttpClient(options.endpoint, {
            'X-CSRF-TOKEN': options.csrfToken,
        });

        this.commands = [
            new Artisan(client),
            new Composer(client),
            new MySQL(client),
            new Tinker(client),
            new Vim(client),
        ];

        this.element = document.querySelector(elementId);
        this.term = $(this.element).terminal(this.run.bind(this), {});

        this.fit();
        window.addEventListener('resize', this.fit.bind(this));
    }

    async run(cmd: string) {
        cmd = cmd.trim();
        for (const index in this.commands) {
            const command = this.commands[index];
            try {
                if (command.is(cmd) === true) {
                    this.term.echo(await command.run(cmd));

                    break;
                }
            } catch (e) {
                this.term.error(e);
            }
        }
    }

    fit() {
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
