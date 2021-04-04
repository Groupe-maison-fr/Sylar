import {Writable} from "stream";

export default class BufferableStream extends Writable {
    chunksQQ = [];

    constructor(opts) {
        super(opts);
        this.chunks = '';
    }

    _write(chunk, _, next) {
        this.chunks += chunk;
        next();
    }

    toString() {
        return this.chunks;
    }
}
