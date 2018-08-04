import { Command } from '../command';

export class Composer extends Command {
    is(command): boolean {
        return /^(\.\/)?composer/.test(command);
    }
}
