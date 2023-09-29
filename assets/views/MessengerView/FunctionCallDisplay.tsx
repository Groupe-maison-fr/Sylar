import * as React from 'react';
import { styled } from '@mui/material/styles';
import { ArrElement } from '../../components/Helper';
import { FailedMessageQuery } from '../../gql/graphql';

const PREFIX = 'FunctionCallDisplay';

const classes = {
  backtraceNamespace: `${PREFIX}-backtraceNamespace`,
  backtraceShortClass: `${PREFIX}-backtraceShortClass`,
  backtraceFunction: `${PREFIX}-backtraceFunction`,
  backtraceLine: `${PREFIX}-backtraceLine`,
};

const Root = styled('div')(() => ({
  [`& .${classes.backtraceNamespace}`]: {
    color: 'red',
  },

  [`& .${classes.backtraceShortClass}`]: {
    color: 'cyan',
    whiteSpace: 'nowrap',
  },

  [`& .${classes.backtraceFunction}`]: {
    color: 'green',
  },

  [`& .${classes.backtraceLine}`]: {
    color: 'purple',
  },
}));

function FunctionCallDisplay({
  call,
}: {
  call: ArrElement<FailedMessageQuery['failedMessage']['backtrace']>;
}) {
  if (
    call.namespace === '' &&
    call.short_class === '' &&
    call.function === '' &&
    call.type === ''
  ) {
    return `${call.file?.split('/').pop()} (${call.line})`;
  }
  return (
    <Root>
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
      <span className={classes.backtraceLine}>({call.line})</span>
    </Root>
  );
}
export default FunctionCallDisplay;
