import * as React from 'react';
import { createContext, useContext, useReducer } from 'react';
import moment from 'moment';

export interface LogMessage {
  id: string;
  message: string;
  level: number;
  level_name: string;
  channel: string;
  datetime: string;
  moment: moment.Moment;
}

export const LOG_LIST_ACTIONS = {
  ADD: 'ADD',
  CLEAR: 'CLEAR',
};

export interface LogListContextData {
  list: LogMessage[];
}

const reducer = (
  previousState: LogListContextData,
  action: { type: string; payload: any },
): LogListContextData => {
  switch (action.type) {
    case LOG_LIST_ACTIONS.ADD:
      previousState.list.unshift({
        ...action.payload.logMessage,
        moment: moment(action.payload.logMessage.datetime),
      });

      return {
        ...previousState,
        list: previousState.list.slice(0, 80),
      };
    case LOG_LIST_ACTIONS.CLEAR:
      return {
        ...previousState,
        list: [],
      };
    default:
      return previousState;
  }
};

const LogListContext = createContext<{
  logListContextData: LogListContextData;
  dispatchLogList: React.Dispatch<any>;
}>({
  logListContextData: { list: [] },
  dispatchLogList: (): void => {},
});

export const useLogList = (): {
  logListContextData: LogListContextData;
  dispatchLogList: React.Dispatch<any>;
} => useContext(LogListContext);

export const LogListProvider = ({ children }: any) => {
  const [state, dispatchLogList] = useReducer(reducer, { list: [] });
  return (
    <LogListContext.Provider
      value={{
        logListContextData: state,
        dispatchLogList,
      }}
    >
      {children}
    </LogListContext.Provider>
  );
};
