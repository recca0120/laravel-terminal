export declare class Spinner {
    private spinner;
    private frameIndex;
    private interval;
    constructor(style?: string);
    setStyle(style: string): Spinner;
    start(cb: Function): Spinner;
    stop(): Spinner;
    private wait;
    private frame;
}
