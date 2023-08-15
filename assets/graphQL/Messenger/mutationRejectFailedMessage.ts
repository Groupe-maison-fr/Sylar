import { graphql } from '../../gql/gql';
import { authenticatedClient } from '../../Context/Authentication/AuthenticatedClient';

export default (client: authenticatedClient, ids: string[]) =>
  client
    .mutation(
      graphql(`
        mutation MutationRejectFailedMessage($ids: [ID!]) {
          rejectFailedMessage(input: { ids: $ids }) {
            success
          }
        }
      `),
      { ids: ids.map((id: string) => parseInt(id, 10)) },
    )
    .then((data) => data.rejectFailedMessage);
