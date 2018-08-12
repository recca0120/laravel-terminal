import { Command, Interpreterable } from '../command';

export class Tinker extends Command implements Interpreterable {
    is(command: string): boolean {
        return /^(\.\/)?tinker/.test(command);
    }

    async run(command: string): Promise<any> {
        const cmd: any = this.parseSentence(command);

        return await this.client.jsonrpc(cmd.method, [`--command="${cmd.params.join(' ')}"`]);
    }

    interpreterable(command: string): boolean {
        return ['artisan tinker', 'tinker'].indexOf(command.trim()) !== -1;
    }

    getInterpreter(): any {
        return {
            prompt: 'tinker> ',
            name: 'tinker',
        };
    }
}
