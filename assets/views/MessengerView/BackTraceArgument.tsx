import * as React from 'react';
import { ArgumentCall } from '../../graphQL/Messenger/queryFailedMessage';

// @ts-ignore
const BackTraceArgument = ({ argument }:{argument:ArgumentCall|ArgumentCall[]}) => {
  if (argument === undefined) {
    return 'undefined';
  }
  if (Array.isArray(argument)) {
    return (
      <ul>
        {argument.map((_argument:ArgumentCall, index:number) => {
          return (
          // eslint-disable-next-line react/no-array-index-key
            <BackTraceArgument key={`_${_argument.value}_${_argument.type}_${index}`} argument={_argument} />
          );
        })}
      </ul>
    );
  }
  return (
    <li>
      {argument.value || '?'}
      {' '}
      (
      {argument.type}
      )
    </li>
  );
};
export default BackTraceArgument;
