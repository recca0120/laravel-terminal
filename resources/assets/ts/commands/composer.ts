import { Command } from '../command';

export class Composer extends Command {
    protected commandLine: boolean = true;

    is(command: string): boolean {
        return /^(\.\/)?composer/.test(command);
    }
}
