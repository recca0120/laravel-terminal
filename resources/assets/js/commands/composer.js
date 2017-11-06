import { Command } from './command';

export class Composer extends Command {
    constructor(shell, options) {
        super('composer', shell, options);
    }
}
