import * as React from 'react';
import { FunctionCall } from '../../graphQL/Messenger/queryFailedMessage';
import FunctionCallDisplay from './FunctionCallDisplay';
import BackTraceArgument from './BackTraceArgument';

const BackTraceDisplay = ({ backtrace }: { backtrace: FunctionCall[] }) => {
  if (!backtrace) {
    return null;
  }
  return (
    <ul>
      {backtrace.map((call: FunctionCall, index) => {
        return (
          <li key={`_${index}`}>
            <a
              target="_blank"
              href={`${call.file}:${call.line}`}
              rel="noreferrer"
            >
              <FunctionCallDisplay call={call} />
            </a>
            {/* @ts-ignore */}
            <BackTraceArgument argument={JSON.parse(call.arguments)} />
          </li>
        );
      })}
    </ul>
  );
};

export default BackTraceDisplay;
