import { Command } from '../command';

export class Composer extends Command {
    is(command: string): boolean {
        return /^(\.\/)?composer/.test(command);
    }

    async run(command: string): Promise<any> {
        const cmd: any = this.parseSentence(command);

        return await this.client.jsonrpc(cmd.method, [`--command="${cmd.params.join(' ')}"`]);
    }
}
