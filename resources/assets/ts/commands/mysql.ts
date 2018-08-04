import { Command } from '../command';

export class MySQL extends Command {
    is(command): boolean {
        return /^(\.\/)?mysql/.test(command);
    }
}
