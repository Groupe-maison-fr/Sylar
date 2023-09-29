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
    )
    .then((data) => data.addReservation);
