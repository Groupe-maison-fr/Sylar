import { graphql } from '../../gql/gql';
import { authenticatedClient } from '../../Context/Authentication/AuthenticatedClient';

export default (client: authenticatedClient, max: number) =>
  client
    .query(
      graphql(`
        query FailedMessages($max: Int!) {
          failedMessages(max: $max) {
            id
            className
            exceptionMessage
            date
          }
        }
      `),
      { max },
    )
    .then((data) => data.failedMessages);
