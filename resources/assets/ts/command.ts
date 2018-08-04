import { HttpClient } from './httpclient';
const parseSentence = require('minimist-string');

export abstract class Command {
    constructor(protected client: HttpClient = new HttpClient(), protected options: any = []) {}

    abstract is(command: string): boolean;

    async run(command: string): Promise<any> {
        const cmd: any = this.parseSentence(command);

        return await this.client.jsonrpc(cmd.cmd, [`--command="${cmd.parameters.join(' ')}"`]);
    }

    protected parseSentence(command): any {
        command = command.replace(/^\.\//, '').trim();
        const minimist: any = parseSentence(command);
        const cmd: string = minimist._.shift();
        const parameters: string[] = [];
        parameters.push(...minimist._);
        for (let index in minimist) {
            if (index === '_') {
                continue;
            }

            parameters.push(`${index}=${minimist[index]}`);
        }

        return {
            cmd,
            parameters,
        };
    }
}
