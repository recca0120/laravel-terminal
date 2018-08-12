import * as $ from 'jquery';
import { HttpClient } from './httpclient';
import { OutputFormatter } from './output-formatter';

class Parser {
    private command_re = /((?:"[^"\\]*(?:\\[\S\s][^"\\]*)*"|'[^'\\]*(?:\\[\S\s][^'\\]*)*'|\/[^\/\\]*(?:\\[\S\s][^\/\\]*)*\/[gimsuy]*(?=\s|$)|(?:\\\s|\S))+)(?=\s|$)/gi;
    private string_re = /("(?:[^"\\]|\\(?:\\\\)*"|\\\\)*"|'(?:[^'\\]|\\(?:\\\\)*'|\\\\)*')/;
    private float_re = /^[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?$/;
    private re_re = /^\/((?:\\\/|[^/]|\[[^\]]*\/[^\]]*\])+)\/([gimsuy]*)$/;

    parse(string) {
        return this.process_command(string, this.parse_argument.bind(this));
    }

    private process_command(string, fn) {
        const array = string.match(this.command_re) || [];

        if (array.length) {
            const name = array.shift();
            const args = $.map(array, arg => {
                if (arg.match(/^["']/)) {
                    arg = arg.replace(/\n/g, '\\u0000\\u0000\\u0000\\u0000');
                    arg = fn(arg);
                    return arg.replace(/\x00\x00\x00\x00/g, '\n');
                }

                return fn(arg);
            });

            const quotes = $.map(array, arg => {
                const m = arg.match(/^(['"]).*\1$/);
                return (m && m[1]) || '';
            });

            const rest = string.substring(name.length).trim();

            return {
                command: string,
                name,
                args,
                args_quotes: quotes,
                rest,
            };
        }

        return {
            command: string,
            name: '',
            args: [],
            args_quotes: [],
            rest: '',
        };
    }

    private parse_string(string) {
        // split string to string literals and non-strings
        return string
            .split(this.string_re)
            .map(string => {
                // remove quotes if before are even number of slashes
                // we don't remove slases becuase they are handled by JSON.parse
                if (string.match(/^['"]/)) {
                    // fixing regex to match empty string is not worth it
                    if (string === '""' || string === "''") {
                        return '';
                    }
                    const quote = string[0];
                    const re = new RegExp(`(^|(?:\\\\(?:\\\\)*)?)${quote}`, 'g');
                    string = string.replace(re, '$1');
                }
                string = `"${string}"`;

                // use build in function to parse rest of escaped characters
                return JSON.parse(string);
            })
            .join('');
    }

    private parse_argument(arg, strict) {
        if (strict === false) {
            if (arg[0] === "'" && arg[arg.length - 1] === "'") {
                return arg.replace(/^'|'$/g, '');
            }

            if (arg[0] === '"' && arg[arg.length - 1] === '"') {
                return arg.replace(/^"|"$/g, '').replace(/\\([" ])/g, '$1');
            }

            if (arg.match(/\/.*\/[gimy]*$/)) {
                return arg;
            }

            if (arg.match(/['"]]/)) {
                // part of arg is in quote
                return this.parse_string(arg);
            }

            return arg.replace(/\\ /g, ' ');
        }
        const regex = arg.match(this.re_re);

        if (regex) {
            return new RegExp(regex[1], regex[2]);
        }

        if (arg.match(/['"]/)) {
            return this.parse_string(arg);
        }

        if (arg.match(/^-?[0-9]+$/)) {
            return parseInt(arg, 10);
        }

        if (arg.match(this.float_re)) {
            return parseFloat(arg);
        }

        return arg.replace(/\\(['"() ])/g, '$1');
    }
}

export interface Comfirmable {
    comfirmable(command): boolean;
    getComfirm(command): string;
    getComfirmCommand(command: string): string;
}

export interface Interpreterable {
    interpreterable(command: string): boolean;

    getInterpreter(): any;
}

export abstract class Command implements Interpreterable, Comfirmable {
    private parser: Parser;

    constructor(protected client: HttpClient, protected outputFormatter: OutputFormatter, protected options: any = []) {
        this.parser = new Parser();
    }

    abstract is(command: string): boolean;

    async run(command: string): Promise<any> {
        const cmd: any = this.parseSentence(command);

        return await this.client.jsonrpc(cmd.method, cmd.params);
    }

    interpreterable(command: string): boolean {
        return false;
    }

    getInterpreter(): any {
        return {};
    }

    comfirmable(command: string): boolean {
        return false;
    }

    getComfirm(command: string): any {
        return {};
    }

    getComfirmCommand(command: string): string {
        return command;
    }

    protected environment(): string {
        return this.options.environment;
    }

    protected isProduction(): boolean {
        return this.environment() === 'production';
    }

    protected parseSentence(command): any {
        const result = this.parser.parse(command);

        return {
            method: result.name || result.command,
            params: result.args,
        };
    }
}
