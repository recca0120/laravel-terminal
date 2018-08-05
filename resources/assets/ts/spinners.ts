const cliSpinners = require('cli-spinners');

export class Spinner {
    private spinner: any = cliSpinners.dots;
    private frameIndex = 0;
    private interval;

    constructor(style: string = 'dots') {
        this.setStyle(style);
    }

    setStyle(style: string) {
        this.spinner = cliSpinners[style];
    }

    start(cb: Function) {
        this.interval = setInterval(() => {
            cb(this.frame());
        }, this.wait());
    }

    stop() {
        if (this.interval) {
            clearInterval(this.interval);
        }
    }

    private wait() {
        return this.spinner.interval;
    }

    private frame() {
        const frames = this.spinner.frames;
        let frame = frames[this.frameIndex];

        this.frameIndex = ++this.frameIndex % frames.length;

        return `${frame} `;
    }
}
