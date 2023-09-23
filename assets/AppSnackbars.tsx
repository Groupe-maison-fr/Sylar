import React, { useEffect } from 'react';
import { useSnackbar } from 'notistack';
import EventBus from './components/EventBus';

const AppSnackbars = () => {
  const { enqueueSnackbar } = useSnackbar();

  const displayError = (eventName: string, arg: { message:string}) => {
    enqueueSnackbar(arg.message);
  };

  const displayFailedMessage = (eventName:string, arg: { message:string, exception: string}) => {
    enqueueSnackbar(`${arg.message} (${arg.exception})`);
  };

  const displayStart = (eventName:string, arg: { masterName:string, instanceName: string}) => {
    enqueueSnackbar(`Service ${arg.masterName}:${arg.instanceName} started`);
  };

  const displayStop = (eventName:string, arg: { masterName:string, instanceName: string}) => {
    enqueueSnackbar(`Service ${arg.masterName}:${arg.instanceName} stopped`);
  };

  useEffect(() => {
    EventBus.on('serviceCloner:start', displayStart);
    EventBus.on('serviceCloner:stop', displayStop);
    EventBus.on('serviceCloner:error', displayError);
    EventBus.on('filesystem:error', displayError);
    EventBus.on('container:error', displayError);
    EventBus.on('failedMessage:new', displayFailedMessage);
    return () => {
      EventBus.remove('serviceCloner:error', displayError);
      EventBus.remove('failedMessage:new', displayFailedMessage);
      EventBus.remove('serviceCloner:start', displayStart);
      EventBus.remove('filesystem:error', displayError);
      EventBus.remove('container:error', displayError);
      EventBus.remove('serviceCloner:stop', displayStop);
    };
  }, []);

  return null;
};

export default AppSnackbars;
