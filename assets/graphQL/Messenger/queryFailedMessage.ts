import { graphql } from '../../gql/gql';
import { authenticatedClient } from '../../Context/Authentication/AuthenticatedClient';

export default (client: authenticatedClient, id: string) =>
  client
    .query(
      graphql(`
        query FailedMessage($id: Int!) {
          failedMessage(id: $id) {
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
              arguments {
                type
                value
              }
            }
            flattenException {
              message
              code
              previous {
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
      `),
      { id: parseInt(id, 10) },
    )
    .then((data) => data.failedMessage);
