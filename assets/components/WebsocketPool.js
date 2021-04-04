import ReconnectingWebSocket from "reconnecting-websocket";

const pool = {};

const generateId = function () {
    return '_' + Math.random().toString(36).substr(2, 9) + Math.random().toString(36).substr(2, 9);
};

export const addClient = (url, onMessage) => {
    let newPool = false;
    if (!pool[url]) {
        pool[url] = {socket: new ReconnectingWebSocket(url), clients: {}, state: null};
        newPool = true;
    }

    const id = generateId();
    pool[url].clients[id] = onMessage;

    if (newPool) {
        pool[url].socket.addEventListener('open', () => pool[url].state = 'open');
        pool[url].socket.addEventListener('close', () => pool[url].state = 'close');
        pool[url].socket.addEventListener('error', () => pool[url].state = 'error');
        pool[url].socket.addEventListener('message', (messagePayload) => {
            const message = JSON.parse(messagePayload.data);
            Object.keys(pool[url].clients).forEach((key) => {
                pool[url].clients[key](message);
            })
        });
    }
    return [id, pool[url].socket];
}

export const removeClient = (url, id) => {
    if (!pool[url]) {
        return;
    }
    if (!pool[url].clients[id]) {
        return;
    }
    delete pool[url].clients[id];
    if (Object.keys(pool[url].clients).length === 0) {
        pool[url].socket.close();
        delete pool[url];
    }
}
