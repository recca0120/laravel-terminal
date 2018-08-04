import { Command } from '../command';

export class MySQL extends Command {
    is(command): boolean {
        return /^(\.\/)?mysql/.test(command);
    }

    isInterpreter(command: string): boolean {
        return command.trim() === 'mysql';
    }

    getInterpreter(): any {
        return {
            prompt: 'mysql> ',
            name: 'mysql',
        };
    }
}
