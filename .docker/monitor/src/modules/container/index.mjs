import {getDockerStats, startDockerCollector as _startDockerCollector} from "./backgroundDockerStatsCollector.mjs";
import Docker from 'dockerode';

const docker = new Docker({socketPath: '/var/run/docker.sock'});

const startDockerCollector = (getWss) => {
    _startDockerCollector(docker, (dockerStatistics) => {
        getWss().clients.forEach(function (client) {
            client.send(JSON.stringify({
                type: 'dockerStatistics',
                dockerStatistics
            }));
        });
    });
};

const containers = () => {
    return (req, res) =>{
        docker
            .listContainers()
            .then(function (containers) {
                const result = {containers: []};
                containers.forEach(function (containerInfo) {
                    containerInfo.TextPorts = containerInfo.Ports.map((port) => {
                        return {
                            label: `${port.type ? port.type + '/' : ''}${port.PublicPort||'-'}:${port.PrivatePort===port.PublicPort?'*':(port.PrivatePort||'')}`,
                            sortableValue: port.PrivatePort
                        };
                    })
                    containerInfo.TextName = containerInfo.Names.join('/')
                    containerInfo.Status = containerInfo.Status
                        .replace(' seconds','s')
                        .replace(' minutes','m')
                        .replace(' hours','h')
                        .replace(' days','d')
                    result.containers.push(containerInfo)
                })
                return result
            })
            .then(result => res.send(result));
    }
}

const containersStats = () => {
    return (req, res) => {
        res.send(getDockerStats());
    }
}

const containersWebsocket = () => {
    return (client, request, next) => {
        client.on('message', function (messagePayload) {
        });

        client.on('close', function (id) {
            console.log('Close websocket', id)
        });

        console.log('Connect websocket')
        client.send(JSON.stringify({
            type: 'ack-container',
            connection: 'ok'
        }));
    }
}

export {
    startDockerCollector,
    containers,
    containersStats,
    containersWebsocket
};
