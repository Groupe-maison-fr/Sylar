import supervisord from "supervisord";
import {addProcessLog, startSupervisorCollector as _startSupervisorCollector} from "./backgroundSupervisordLogCollector.mjs";

const startSupervisorCollector = (getWss) =>{
    _startSupervisorCollector((host, processName, logs) => {
        getWss().clients.forEach(function (client) {
            client.send(JSON.stringify({
                type: 'logs',
                logs,
                host,
                processName
            }));
        });
    });
}

const supervisorProcessHost = () => {
    return (req, res) => {
        const client = supervisord.connect(req.params.host);
        client.getAllProcessInfo(function (err, result) {
            res.send({processes: result})
        });
    }
}

const supervisorStartProcessName = () => {
    return (req, res) => {
        const client = supervisord.connect(req.params.host);
        client.startProcess(req.params.processName, function (err, result) {
            res.send({result})
        });
    }
}

const supervisorStartProcessGroup = () => {
    return (req, res) => {
        const client = supervisord.connect(req.params.host);
        client.startProcessGroup(req.params.processGroup, function (err, result) {
            res.send({result})
        });
    }
}

const supervisorStopProcess = () => {
    return (req, res) => {
        const client = supervisord.connect(req.params.host);
        client.stopProcessGroup(req.params.processGroup, function (err, result) {
            res.send({result})
        });
    }
}

const supervisorRestartProcessGroup = () => {
    return (req, res) =>{
        const client = supervisord.connect(req.params.host);
        client.stopProcessGroup(req.params.processGroup, function (err, result) {
            res.send({result})
        });
        client.startProcessGroup(req.params.processGroup, function (err, result) {
            res.send({result})
        });
    }
}

const supervisorWebsocket = () => {
    return (client, request, next) => {
        client.on('message', function (messagePayload) {
            const data = JSON.parse(messagePayload);
            if (data.type === 'connect') {
                addProcessLog(data.host, data.processName)
                    .then((logs) => {
                        client.send(JSON.stringify({
                            ack: 'stdout',
                            ...request.params
                        }));
                        client.send(JSON.stringify({
                            type: 'logs',
                            logs,
                            host: data.host,
                            processName: data.processName
                        }));
                    }).catch(console.log);
                return;
            }
            client.send(JSON.stringify({
                type: 'error',
                payload: messagePayload,
                ...request.params
            }));
        });

        client.on('close', function (id) {
            console.log('Close websocket', id)
        });

        console.log('Connect websocket')
        client.send(JSON.stringify({
            type: 'ack',
            connection: 'ok'
        }));
    }
}

export {
    startSupervisorCollector,
    supervisorProcessHost,
    supervisorStartProcessName,
    supervisorStartProcessGroup,
    supervisorStopProcess,
    supervisorRestartProcessGroup,
    supervisorWebsocket
}
