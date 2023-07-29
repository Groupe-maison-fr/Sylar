import { mutation } from '../GraphQL';
import { graphql } from '../../gql/gql';

export default (service: string, name: string, index: number) =>
  mutation(
    graphql(`
      mutation MutationAddReservation(
        $service: String!
        $index: Int!
        $name: String!
      ) {
        addReservation(
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
    { service, index, name },
  ).then((data) => data.addReservation);
