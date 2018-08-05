import { Command } from '../command';

export class Composer extends Command {
    is(command: string): boolean {
        return /^(\.\/)?composer/.test(command);
    }
}
