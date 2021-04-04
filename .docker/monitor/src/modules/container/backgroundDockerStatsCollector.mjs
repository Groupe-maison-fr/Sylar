import interval from "interval-promise";
import moment from "moment";
import {fifo} from "../../lib/fifo.mjs";

let stoppedExternally = false
let dockerStatistics = {keys: [], cpuUsage: [], memUsage: []};

const stopExternally = () => {
    stoppedExternally = true
}

const getDockerStats = () => {
    return dockerStatistics;
}

function startDockerCollector(docker, callback) {
    interval(async (iteration, stop) => {
        if (stoppedExternally) {
            stop()
        }
        return new Promise(function (resolve) {
            docker
                .listContainers()
                .then(function (containers) {
                    Promise
                        .all(containers.map(function (containerInfo) {
                            return docker
                                .getContainer(containerInfo.Id)
                                .stats({stream: false})
                        }))
                        .then((results) => {
                            const time = moment().format('HH:mm:ss');
                            dockerStatistics = {
                                keys: results.map((containerStat) => containerStat.name),
                                cpuUsage: fifo(dockerStatistics.cpuUsage, 10, results.reduce((accumulator, containerStat) => {
                                    accumulator.time = time;
                                    const cpuDelta = containerStat.cpu_stats.cpu_usage.total_usage - containerStat.precpu_stats.cpu_usage.total_usage;
                                    const systemDelta = containerStat.cpu_stats.system_cpu_usage - containerStat.precpu_stats.system_cpu_usage;

                                    accumulator[containerStat.name] = cpuDelta / systemDelta * 100;
                                    return accumulator;
                                }, {})),
                                memUsage: fifo(dockerStatistics.memUsage, 10, results.reduce((accumulator, containerStat) => {
                                    accumulator.time = time;
                                    accumulator[containerStat.name] = containerStat.memory_stats.usage / 1024 / 1024
                                    return accumulator;
                                }, {}))
                            }
                            callback(dockerStatistics)
                            resolve();
                        })
                });
        });
    }, 1000, {stopOnError: false})
}

export {
    stopExternally,
    getDockerStats,
    startDockerCollector,
}
