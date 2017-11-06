import { Command } from './command';

export class MySQL extends Command {
    constructor(shell, options) {
        super('mysql', shell, options);
    }
}
