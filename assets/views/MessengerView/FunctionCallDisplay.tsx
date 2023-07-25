import * as React from 'react';
import { makeStyles } from '@material-ui/core';
import { FunctionCall } from '../../graphQL/Messenger/queryFailedMessage';

const useStyles = makeStyles(() => ({
  backtraceNamespace: {
    color: 'red',
  },
  backtraceShortClass: {
    color: 'cyan',
    whiteSpace: 'nowrap',
  },
  backtraceFunction: {
    color: 'green',
  },
  backtraceLine: {
    color: 'purple',
  },
}));

function FunctionCallDisplay({ call }: { call: FunctionCall }) {
  const classes = useStyles();
  if (
    call.namespace === '' &&
    call.short_class === '' &&
    call.function === '' &&
    call.type === ''
  ) {
    return `${call.file.split('/').pop()} (${call.line})`;
  }
  return (
    <>
      {call.namespace && (
        <span className={classes.backtraceNamespace}>{call.namespace}\</span>
      )}
      {call.short_class && (
        <span className={classes.backtraceShortClass}>
          {call.short_class}
          &nbsp;
        </span>
      )}
      {call.function && (
        <span className={classes.backtraceFunction}>{call.function}</span>
      )}
      &nbsp;
      <span className={classes.backtraceLine}>({call.line})</span>
    </>
  );
}
export default FunctionCallDisplay;
