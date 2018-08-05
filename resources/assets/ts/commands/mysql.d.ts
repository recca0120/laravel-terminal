import { Command } from '../command';
export declare class MySQL extends Command {
    private connection;
    is(command: string): boolean;
    run(command: string): Promise<any>;
    interpreterable(command: string): boolean;
    getInterpreter(): any;
}
