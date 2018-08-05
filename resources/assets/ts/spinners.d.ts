export declare class Spinner {
    private spinner;
    private frameIndex;
    private interval;
    constructor(style?: string);
    setStyle(style: string): void;
    start(cb: Function): void;
    stop(): void;
    private wait;
    private frame;
}
