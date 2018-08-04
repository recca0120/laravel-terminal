import { HttpClient } from './httpclient';
const parseSentence = require('minimist-string');

export abstract class Command {
    constructor(private client: HttpClient = new HttpClient()) {}

    abstract is(command): boolean;

    run(command) {
        const cmd: any = this.parseSentence(command.replace(/^\.\//, ''));

        return this.client.jsonrpc(cmd.cmd, [`--command="${cmd.parameters.join(' ')}"`]);
    }

    protected parseSentence(command): any {
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
