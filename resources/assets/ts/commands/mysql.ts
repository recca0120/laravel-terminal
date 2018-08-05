import { Command } from '../command';

export class MySQL extends Command {
    private connection = null;

    is(command: string): boolean {
        return /^(\.\/)?mysql/.test(command);
    }

    async run(command: string): Promise<any> {
        const cmd: any = this.parseSentence(command);

        if (cmd.parameters[0] === 'use') {
            this.connection = cmd.parameters[1].replace(/;/g, '').trim();

            return `current: ${this.connection}`;
        }

        if (this.connection !== null) {
            cmd.parameters[0].push(`--connection=${this.connection}`);
        }

        return await this.client.jsonrpc(cmd.cmd, [`--command="${cmd.parameters.join(' ')}"`]);
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
