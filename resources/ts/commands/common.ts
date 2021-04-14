import {Command} from '../command';

export class Common extends Command {
    is(command: string): boolean {
        return true;
    }
}
