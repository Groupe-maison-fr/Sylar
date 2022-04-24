import bodyParser from "body-parser";
import express from 'express';
import expressWs from 'express-ws';
import http from 'http';
import {
    vapidKeys,
    subscriptionSet,
    subscriptionGet,
    subscriptionDelete,
    subscriptionEnabled,
    sendNotification
} from "./modules/webpush/index.mjs";
import {
    startSupervisorCollector,
    supervisorProcessHost,
    supervisorStartProcessName,
    supervisorStartProcessGroup,
    supervisorStopProcess,
    supervisorRestartProcessGroup,
    supervisorWebsocket
} from "./modules/supervisor/index.mjs";

const app = express();
const server = http.createServer(app);

const {getWss} = expressWs(app, server);

//app.options('*', cors());
//app.use(cors());
app.use(bodyParser.urlencoded({extended: false}));
app.use(bodyParser.json());

app.get('/monitor/subscription/vapidkeys', vapidKeys())

app.post('/monitor/subscription', subscriptionSet())
app.post('/monitor/subscription/enabled', subscriptionEnabled())
app.get('/monitor/subscription', subscriptionGet())
app.delete('/monitor/subscription', subscriptionDelete())
app.post('/monitor/notification', sendNotification())

app.get('/monitor/supervisord/:host', supervisorProcessHost());
app.get('/monitor/supervisord/:host/start/:processName', supervisorStartProcessName());
app.get('/monitor/supervisord/:host/startGroup/:processGroup', supervisorStartProcessGroup());
app.get('/monitor/supervisord/:host/stop/:processGroup', supervisorStopProcess());
app.get('/monitor/supervisord/:host/restart/:processGroup', supervisorRestartProcessGroup());
app.ws('/monitor/supervisor/', supervisorWebsocket());
startSupervisorCollector(getWss);

server.listen(8080, () => {
    console.log('http Server is running on 8080 '+ Date.now())
});

/*
const proxyfiedServices = [{
    serviceContainer: 'rkt-maildev',
    servicePort: 1080,
    exposedPort: 13900,
}, {
    serviceContainer: 'rkt-web-tty',
    servicePort: 8080,
    exposedPort: 13901,
}, {
    serviceContainer: 'rkt-rabbitmq',
    servicePort: 15672,
    exposedPort: 13902,
}, {
    serviceContainer: 'rkt-webserver',
    servicePort: 80,
    exposedPort: 13903,
}];

proxyfiedServices.forEach((service) => httpProxy.createServer({
    target: {
        host: service.serviceContainer,
        port: service.servicePort
    },
    ws: true,
    ssl: tlsOptions,
}).on('proxyReq', function (proxyReq, req, res, options) {
    res.setHeader('Access-Control-Allow-Origin', '*');
}).listen(service.exposedPort, () => {
    console.log(`Proxy Https Server is running ${service.serviceContainer}:${service.servicePort} to localhost:${service.exposedPort}`)
}));
*/
