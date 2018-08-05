import { HttpClient } from './httpclient';
import { OutputFormatter } from './output-formatter';
const parseSentence = require('minimist-string');

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
    constructor(
        protected client: HttpClient,
        protected outputFormatter: OutputFormatter,
        protected options: any = []
    ) {}

    abstract is(command: string): boolean;

    async run(command: string): Promise<any> {
        const cmd: any = this.parseSentence(command);

        return await this.client.jsonrpc(cmd.name, [`--command="${cmd.params.join(' ')}"`]);
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
        command = command.replace(/^\.\//, '').trim();
        const minimist: any = parseSentence(command);
        const name: string = minimist._.shift();
        const params: string[] = [];
        params.push(...minimist._);
        for (let index in minimist) {
            if (index === '_') {
                continue;
            }

            const key = index.length === 1 ? `-${index}` : `--${index}`;
            const value = minimist[index];

            if (value === true) {
                params.push(key);
            } else if (value !== false) {
                params.push(`${key}=${value}`);
            }
        }

        return {
            name,
            params,
        };
    }
}
