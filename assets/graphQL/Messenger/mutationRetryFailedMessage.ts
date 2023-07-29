import { mutation } from '../GraphQL';
import { graphql } from '../../gql/gql';

export default (id: string) =>
  mutation(
    graphql(`
      mutation MutationRetryFailedMessage($id: ID!) {
        retryFailedMessage(input: { id: $id }) {
          success
        }
      }
    `),
    { id: parseInt(id, 10) },
  ).then((data) => data.retryFailedMessage);
