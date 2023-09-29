import * as React from 'react';
import { DebugTraceCallArgument } from '../../gql/graphql';

const BackTraceArgument = ({
  argument,
}: {
  argument: DebugTraceCallArgument | DebugTraceCallArgument[];
}) => {
  if (argument === undefined) {
    return 'undefined';
  }
  if (Array.isArray(argument)) {
    return (
      <ul>
        {argument.map((_argument: DebugTraceCallArgument, index: number) => {
          return (
            <BackTraceArgument
              key={`_${_argument?.value}_${_argument.type}_${index}`}
              argument={_argument}
            />
          );
        })}
      </ul>
    );
  }
  return (
    <li>
      {argument.value || '?'} ({argument.type})
    </li>
  );
};
export default BackTraceArgument;
