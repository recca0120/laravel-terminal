import { Command } from '../command';

export class Tinker extends Command {
    is(command): boolean {
        return /^(\.\/)?tinker/.test(command);
    }

    isInterpreter(command: string): boolean {
        return ['artisan tinker', 'tinker'].indexOf(command.trim()) !== -1;
    }

    getInterpreter(): any {
        return {
            prompt: 'tinker> ',
            name: 'tinker',
        };
    }
}
