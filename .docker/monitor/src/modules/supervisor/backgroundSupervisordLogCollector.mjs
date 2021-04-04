import interval from "interval-promise";
import supervisord from "supervisord";

let stoppedExternally = false
let supervisorLogs = {};

const stopExternally = () => {
    stoppedExternally = true
}

const tailProcessStdoutLog = (supervisorClient, supervisorClientConfiguration, processName, offset, length) => {
    return new Promise((resolve, reject) => {
        supervisorClient.tailProcessStdoutLog(processName, offset, length, function (err, result) {
            if (err) {
                console.log(err);
                reject(err.body);
                return;
            }
            //console.log('0', supervisorClientConfiguration, processName, result[0]);
            resolve([supervisorClientConfiguration, processName, result[0], result[1]]);
        });
    });
}

const readProcessStdoutLog = (supervisorClient, supervisorClientConfiguration, processName, offset, length) => {
    return new Promise((resolve, reject) => {
        supervisorClient.readProcessStdoutLog(processName, offset, length, function (err, result) {
            if (err) {
                reject(err.body);
                return;
            }
            resolve([supervisorClientConfiguration, processName, result, result.length]);
        });
    });
}

const getSupervisorLogs = (processName) => {
    return supervisorLogs[processName].stdout;
}

const addProcessLog = async (supervisorClientConfiguration, processName) => {
    const processKey = supervisorClientConfiguration + '-' + processName;
    //console.log([supervisorClientConfiguration, processName]);
    if (supervisorLogs.hasOwnProperty(processKey)) {
        return supervisorLogs[processKey].stdout;
    }

    const supervisorClient = supervisord.connect(supervisorClientConfiguration);
    supervisorLogs[processKey] = {
        supervisorClient,
        supervisorClientConfiguration,
        processName,
        stdout: '',
        stdoutOffset: 0
    }
    await tailProcessStdoutLog(
        supervisorClient,
        supervisorClientConfiguration,
        processName,
        0,
        1000
    ).then(([supervisorClientConfiguration, processName, body, offset]) => {
        //console.log('00',supervisorClientConfiguration,processName, body);
        supervisorLogs[processKey].stdout = body;
        supervisorLogs[processKey].stdoutOffset = offset;
    });
    return supervisorLogs[processKey].stdout;
}

const fetchLog = (processKey) => {
    const supervisorLog = supervisorLogs[processKey];
    return tailProcessStdoutLog(
        supervisorLog.supervisorClient,
        supervisorLog.supervisorClientConfiguration,
        supervisorLog.processName,
        supervisorLog.stdoutOffset,
        1000
    ).then(([supervisorClientConfiguration, processName, body, offset]) => {
        supervisorLog.stdout = supervisorLog.stdout + body;
        supervisorLog.stdoutOffset = offset;
        //console.log('1',supervisorClientConfiguration,processName, body);
        return [supervisorClientConfiguration, processName, body, offset];
    }).catch((error)=>console.log(error));
}

const startSupervisorCollector = (callback) => {
    interval(async (iteration, stop) => {
        if (stoppedExternally) {
            stop()
        }
        return Promise.all(Object.keys(supervisorLogs).map((processKey) => {
            return fetchLog(processKey)
                .then(([supervisorClientConfiguration, processName, logs, offset]) => {
                    //console.log('2',supervisorClientConfiguration,processName, logs);
                    if (logs.length > 0) {
                        callback(
                            supervisorClientConfiguration,
                            processName,
                            logs
                        )
                    }
                    return [supervisorClientConfiguration, processName, logs, offset];
                }).catch((error)=>console.log(error));
        })).catch((error)=>console.log(error));
    }, 1500, {
        stopOnError: false
    })
}

export {
    getSupervisorLogs,
    addProcessLog,
    startSupervisorCollector,
    stopExternally,
}
