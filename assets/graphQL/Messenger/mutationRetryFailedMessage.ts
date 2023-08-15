import { graphql } from '../../gql/gql';
import { authenticatedClient } from '../../Context/Authentication/AuthenticatedClient';

export default (client: authenticatedClient, id: string) =>
  client
    .mutation(
      graphql(`
        mutation MutationRetryFailedMessage($id: ID!) {
          retryFailedMessage(input: { id: $id }) {
            success
          }
        }
      `),
      { id: parseInt(id, 10) },
    )
    .then((data) => data.retryFailedMessage);
