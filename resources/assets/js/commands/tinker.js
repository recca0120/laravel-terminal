import { Command } from './command';

export class Tinker extends Command {
    constructor(shell, options) {
        super('tinker', shell, options);
    }
}
