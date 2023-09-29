import { graphql } from '../../gql/gql';
import { authenticatedClient } from '../../Context/Authentication/AuthenticatedClient';

export default (
  client: authenticatedClient,
  service: string,
  name: string,
  index: number,
) =>
  client
    .mutation(
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
    )
    .then((data) => data.deleteReservation);
