import GraphQL from '../GraphQL';

export interface ArgumentCall {
  type: string;
  value?: string;
}
export interface FunctionCall {
  namespace: string;
  short_class: string;
  class: string;
  type: string;
  function: string;
  file: string;
  line: number;
  arguments: ArgumentCall | ArgumentCall[];
}

export interface Exception {
  message: string;
  code: string;
  previous: Exception;
  traceAsString: string;
  class: string;
  statusCode: string;
  statusText: string;
  headers: string;
  file: string;
  line: number;
}

export interface FailedMessage {
  id: number;
  className: string;
  message: string;
  exceptionMessage: string;
  backtrace: FunctionCall[];
  flattenException: Exception;
  date: string;
}

export default (id: number): Promise<FailedMessage> =>
  GraphQL.query(
    `
    query {
        failedMessage(
            id: ${id}
        ) {
            id
            className
            message
            exceptionMessage
            backtrace {
              namespace
              short_class
              class
              type
              function
              file
              line
              arguments
            }
            flattenException{
                message
                code
                previous{
                    message
                    code
                    class
                    statusCode
                    statusText
                    headers
                    file
                    line
                }
                traceAsString
                class
                statusCode
                statusText
                headers
                file
                line
            }
            date
        }
    }
`,
  )
    .then((response) => response.json())
    .then((responseAsJson) => responseAsJson.data.failedMessage);
