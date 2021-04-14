import {Command} from '../command';

export class MySQL extends Command {
    private connection = null;

    is(command: string): boolean {
        return /^(\.\/)?mysql/.test(command);
    }

    async run(command: string): Promise<any> {
        const cmd: any = this.parseSentence(command);

        if (cmd.params[0] === 'use') {
            this.connection = cmd.params[1].replace(/;/g, '').trim();

            return Promise.resolve(`current: ${this.connection}`);
        }

        if (this.connection !== null) {
            cmd.params.push(`--connection=${this.connection}`);
        }

        return await this.client.jsonrpc(cmd.method, [`--command="${cmd.params.join(' ')}"`]);
    }

    interpreterable(command: string): boolean {
        return command.trim() === 'mysql';
    }

    getInterpreter(): any {
        return {
            prompt: 'mysql> ',
            name: 'mysql',
        };
    }
}
