import { Command } from '../command';

export class Tinker extends Command {
    is(command): boolean {
        return /^(\.\/)?tinker/.test(command);
    }
}
