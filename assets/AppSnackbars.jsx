import React, {useEffect, useState} from 'react';
import EventBus from './components/EventBus';
import { useSnackbar } from 'notistack';

const AppSnackbars = () => {
    const {enqueueSnackbar} = useSnackbar();

    const displayError = (eventName, arg) => {
        enqueueSnackbar(arg.message);
    }

    const displayFailedMessage = (eventName, arg) => {
        enqueueSnackbar(`${arg.message} (${arg.exception})`);
    }

    const displayStart = (eventName, arg) => {
        enqueueSnackbar(`Service ${arg.masterName}:${arg.instanceName} started`);
    }

    const displayStop = (eventName, arg) => {
        enqueueSnackbar(`Service ${arg.masterName}:${arg.instanceName} stopped`);
    }

    useEffect(() => {
        EventBus.on('serviceCloner:start', displayStart);
        EventBus.on('serviceCloner:stop', displayStop);
        EventBus.on('serviceCloner:error', displayError);
        EventBus.on('filesystem:error', displayError);
        EventBus.on('container:error', displayError);
        EventBus.on('failedMessage:new', displayFailedMessage);
        return () =>{
            EventBus.remove('serviceCloner:error', displayError);
            EventBus.remove('failedMessage:new', displayFailedMessage);
            EventBus.remove('serviceCloner:start', displayStart);
            EventBus.remove('filesystem:error', displayError);
            EventBus.remove('container:error', displayError);
            EventBus.remove('serviceCloner:stop', displayStop);
        }
    }, []);

    return <React.Fragment></React.Fragment>;
};

export default AppSnackbars;
