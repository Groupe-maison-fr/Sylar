import * as React from 'react';
import FunctionCallDisplay from './FunctionCallDisplay';
import BackTraceArgument from './BackTraceArgument';
import { ArrElement } from '../../components/Helper';
import { FailedMessageQuery } from '../../gql/graphql';

const BackTraceDisplay = ({
  backtrace,
}: {
  backtrace: ArrElement<FailedMessageQuery['failedMessage']['backtrace']>[];
}) => {
  if (!backtrace) {
    return null;
  }
  return (
    <ul>
      {backtrace.map((call, index) => {
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
            <BackTraceArgument argument={call.arguments} />
          </li>
        );
      })}
    </ul>
  );
};

export default BackTraceDisplay;
