import { Command } from '../command';

export class Artisan extends Command {
    is(command): boolean {
        return /^(\.\/)?artisan/.test(command);
    }
}
