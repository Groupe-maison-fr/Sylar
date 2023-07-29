import { mutation } from '../GraphQL';
import { graphql } from '../../gql/gql';

export default (ids: string[]) =>
  mutation(
    graphql(`
      mutation MutationRejectFailedMessage($ids: [ID!]) {
        rejectFailedMessage(input: { ids: $ids }) {
          success
        }
      }
    `),
    { ids: ids.map((id: string) => parseInt(id, 10)) },
  ).then((data) => data.rejectFailedMessage);
