import { Command } from '../command';
export declare class Help extends Command {
    is(command: string): boolean;
    run(): Promise<any>;
}
