function fifo(data, length, object) {
    const arr = data.slice(-length);
    arr.push(object);
    return arr;
}

export {
    fifo
}
