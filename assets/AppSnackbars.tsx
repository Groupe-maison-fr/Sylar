import { useEffect } from 'react';
import { useSnackbar } from 'notistack';
import EventBus from './components/EventBus';
import { LOG_LIST_ACTIONS, LogMessage, useLogList } from './Context/LogListContext';

const AppSnackbars = () => {
  const { enqueueSnackbar } = useSnackbar();
  const { dispatchLogList } = useLogList();

  // @ts-ignore TS6133
  const displayError = (eventName: string, arg: { message:string}) => {
    enqueueSnackbar(arg.message);
  };

  // @ts-ignore TS6133
  const displayFailedMessage = (eventName:string, arg: { message:string, exception: string}) => {
    enqueueSnackbar(`${arg.message} (${arg.exception})`);
  };

  // @ts-ignore TS6133
  const displayStart = (eventName:string, arg: { masterName:string, instanceName: string}) => {
    enqueueSnackbar(`Service ${arg.masterName}:${arg.instanceName} started`);
  };

  // @ts-ignore TS6133
  const displayStop = (eventName:string, arg: { masterName:string, instanceName: string}) => {
    enqueueSnackbar(`Service ${arg.masterName}:${arg.instanceName} stopped`);
  };

  // @ts-ignore TS6133
  const displayLogMessage = (eventName:string, logMessage: LogMessage) => {
    dispatchLogList({ type: LOG_LIST_ACTIONS.ADD, payload:{logMessage} });
  };

  // @ts-ignore TS6133
  const displayLogMessages = (eventName:string, arg: { messages: LogMessage[]}) => {
    arg.messages.forEach((message) => displayLogMessage(eventName, message));
  };

  useEffect(() => {
    EventBus.on('serviceCloner:start', displayStart);
    EventBus.on('serviceCloner:stop', displayStop);
    EventBus.on('serviceCloner:error', displayError);
    EventBus.on('filesystem:error', displayError);
    EventBus.on('container:error', displayError);
    EventBus.on('failedMessage:new', displayFailedMessage);
    EventBus.on('log:message', displayLogMessage);
    EventBus.on('log:messages', displayLogMessages);
    return () => {
      EventBus.remove('serviceCloner:error', displayError);
      EventBus.remove('failedMessage:new', displayFailedMessage);
      EventBus.remove('serviceCloner:start', displayStart);
      EventBus.remove('filesystem:error', displayError);
      EventBus.remove('container:error', displayError);
      EventBus.remove('serviceCloner:stop', displayStop);
      EventBus.remove('log:message', displayLogMessage);
      EventBus.remove('log:messages', displayLogMessages);
    };
  }, []);

  return null;
};

export default AppSnackbars;
