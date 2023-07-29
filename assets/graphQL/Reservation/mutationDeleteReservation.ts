import { mutation } from '../GraphQL';
import { graphql } from '../../gql/gql';

export default (service: string, name: string, index: number) =>
  mutation(
    graphql(`
      mutation MutationDeleteReservation(
        $service: String!
        $name: String!
        $index: Int!
      ) {
        deleteReservation(
          input: { service: $service, index: $index, name: $name }
        ) {
          ... on SuccessOutput {
            success
          }
          ... on FailedOutput {
            code
            message
          }
        }
      }
    `),
    { service, name, index },
  ).then((data) => data.deleteReservation);
